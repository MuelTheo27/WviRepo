<?php

namespace App\Http\Controllers;
use App\Http\Services\Apr\AprService;
use App\Models\Sponsor;
use App\Models\SponsorCategory;
use App\Models\Child;
use App\Models\Content;
use Error;
use Log;
use Symfony\Component\Console\Output\ConsoleOutput;
use App\Http\Services\Excel\ExcelService;

use Illuminate\Http\Request;
use App\Http\Controllers\ProgressController;
use Throwable;

class FileController extends Controller
{
    
    protected $aprService;

    protected $console;
    public function __construct() {

        $this->console = new ConsoleOutput();

        $this->aprService = new AprService();

    }
    
    public function validateRowContent(Array $row): bool
    {
        return count(array_filter($row)) === 4; 
    }

    public function upload(Request $request) {
    
        
        // return Response()->error('Error on database');
        $status = [];

        $request->validate([
            'file.*' => 'required|mimes:xlsx,csv'
        ], [
            'file.required' => 'Please upload a file',
            'file.*.mimes' => 'Format file harus xlsx atau csv',
        ]);

        $files = is_array($request->file('file')) ? $request->file('file') : [$request->file('file')];
        $fileIds = json_decode($request->input("fileId"), true);
        $fiscal_year = $request->input('fiscalYear');

        

        foreach($files as $key => $file){
            $status[$fileIds[$key]] = [
                "success" => 0,
                "message" => []
            ];
            try {
                
                $records = (new ExcelService())->processExcel($file);

                ProgressController::startUploadBroadcastProgress($fileIds[$key], count($records));
             
                foreach($records as $index => $record){
                    if($this->validateRowContent($record)){
                        $store = $this->store($record, $fiscal_year);
                       
                        if($store instanceof Error){
                            $status[$fileIds[$key]]["message"][] = $store->getMessage();
                            $status[$fileIds[$key]]["message"] = array_unique($status[$fileIds[$key]]["message"]);
                        }
                        else{
                            $status[$fileIds[$key]]["success"]++;
                        }
                    }
                    ProgressController::updateUploadBroadcastProgress($fileIds[$key]);

                }
            } catch (Throwable $th) {
                ProgressController::startUploadBroadcastProgress($fileIds[$key], 1);
                $status[$fileIds[$key]]["message"][] = $th->getMessage();
                ProgressController::updateUploadBroadcastProgress($fileIds[$key]);
            }
        }

        return response()->json($status);
        
        
}
  
public function store(array $record, string $fiscal_year){
    try {
        return \DB::transaction(function() use ($record, $fiscal_year) {
            $sponsorCategory = SponsorCategory::where('sponsor_category_name', $record[3])->lockForUpdate()->first();
            if(!$sponsorCategory){
                throw new Error("Sponsor Category not found");
            }
            $sponsor = Sponsor::where('id', $record[1])->lockForUpdate()->first();
            if (!$sponsor) {
                
                $sponsor = Sponsor::create([
                    'id' => $record[1],
                    'name' => $record[2],
                    'sponsor_category_id' => $sponsorCategory->id
                ]);
            }
        
            $child = Child::where('child_idn', $record[0])->lockForUpdate()->first();
        
            if (!$child) {
                $child = Child::create([
                    'child_idn' => $record[0],
                    'sponsor_id' => $sponsor->id
                ]);
            }

            $content = Content::where('fiscal_year', $fiscal_year)
                  ->where('child_idn', $child->child_idn)  
                  ->lockForUpdate()
                  ->first();

            if (!$content) {

                $contentUrl = $this->aprService->getAnnualPerformanceReportUrl($child->child_idn, $fiscal_year);
                
                if ($contentUrl === null) {
                    throw new Error("Failed to get content URL on certain Child IDN");
                }
                
                Content::create([
                    'child_idn' => $child->child_idn,
                    'fiscal_year' => $fiscal_year,
                    'content_url' => $contentUrl
                ]);
            }
            
            return true; 
        });
    } catch (Throwable $th) {
       
        if ($th instanceof \Illuminate\Database\QueryException || 
            $th instanceof \PDOException) {
            return new Error('Error on database');
        } else {
            return new Error($th->getMessage());
        }
    }
}


}


     // Telescope::recordLog(new IncomingEntry([
            //     'level' => LogLevel::INFO,
            //     'message' => json_encode($processedData),
            // ]));
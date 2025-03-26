<?php

namespace App\Http\Controllers;
use App\Http\Services\Apr\AprService;
use App\Http\Services\Child\StoreChildren;
use App\Http\Services\Content\StoreContent;
use App\Http\Services\Sponsor\StoreSponsor;
use App\Models\SponsorCategory;
use Log;
use Symfony\Component\Console\Output\ConsoleOutput;
use App\Http\Controllers\Data\ContentController;
use App\Http\Controllers\Data\SponsorController;
use App\Http\Controllers\Data\ChildController;
use App\Http\Services\Excel\ExcelService;

use Illuminate\Http\Request;


class FileController extends Controller
{
    //
    protected $storeContent;
    protected $storeSponsor;
    protected $aprService;
    protected $storeChildren;

    protected $console;
    public function __construct() {
        $this->storeContent = new StoreContent();
        $this->storeSponsor = new StoreSponsor();
        $this->storeChildren = new StoreChildren();
        $this->aprService = new AprService();
        $this->console = new ConsoleOutput();
    }
    

    public function uploadXslx(Request $request) {
        $errorItems = [];
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ], [
            'file.required' => 'Please upload a file',
            
        ]);
        try {
            $uploadedFile = $request->file("file");
            $processedData = (new ExcelService())->processExcel($uploadedFile);

            if (is_null($processedData)) {
                return response()->json(['error' => 'Failed to process the Excel file'], 422);
            }
            if (is_array($processedData)) {
                foreach ($processedData as $item) {

                    $result = $this->store([
                        "child_code" => $item["child_code"],
                        "sponsor_name" => $item["sponsor_name"],
                        "sponsor_category" => $item["sponsor_category"],
                    ]);

                    if ($result instanceof Error) {
                        array_push($errorItems, $item["child_code"]);
                    }
                }
            }
        }
        catch(\Throwable $th){
            error_log($th->getMessage());
            return response()->json(['error' => $th->getMessage()], 500);
        }

        if(empty($errorItems)){
            return response()->json([
                "upload_message" => "Upload Success",
            ], 200);
        }
        
        else{
            return response()->json([
                "upload_message" => "Upload Partially Success",
                "error_list" => $errorItems
            ], 422);
        }


    }
  
    public function store(array $data){
        try {
            return \DB::transaction(function () use ($data) {

                $sponsor = $this->storeSponsor->store(
                    $data["sponsor_name"],
                    SponsorCategory::where("sponsor_category_name", $data["sponsor_category"])
                        ->firstOrFail()->id
                );
    
                $children = $this->storeChildren->store([
                    "child_code" => $data["child_code"],
                    "sponsor_id" => $sponsor->id
                ]);
    
                $pdfUrl = $this->aprService->getPdfUrl($children->child_code);
                if ($pdfUrl instanceof Error) {
                    throw new \Exception($pdfUrl->getMessage());
                }
    
                $this->storeContent->store([
                    "child_id"   => $children->id,
                    "content_url"  => $pdfUrl,
                    "fiscal_year"  => (int)date('m') >= 10 ? (int)date('Y') : (int)date('Y') - 1
                ]);
    
                return true;
            });
        } catch (\Throwable $th) {
            return new \Error($th->getMessage()); 
        }
    }

}

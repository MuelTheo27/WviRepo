<?php

namespace App\Http\Controllers;
use App\Http\Services\Apr\AprService;
use App\Http\Services\Child\StoreChildren;
use App\Http\Services\Content\StoreContent;
use App\Http\Services\Sponsor\StoreSponsor;
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

    protected $storeChildren;
    public function __construct() {
        $this->storeContent = new StoreContent();
        $this->storeSponsor = new StoreSponsor();
        $this->storeChildren = new StoreChildren();
    }

    public function uploadXslx(Request $request) {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ], [
            'file.required' => 'Please upload a file',
            'file.mimes' => 'Format file harus xlsx atau csv',
        ]);
        try {
            $uploadedFile = $request->file("file");
            $processedData = (new ExcelService())->processExcel($uploadedFile);
            
            if (is_null($processedData)) {
                return response()->json(['error' => 'Failed to process the Excel file'], 422);
            }

            // $this->store(array_merge($processedData, [
            //     "content_url" =>
            // ]));

            // return response()->json([
            //     "uploadResponse" => [
            //         "pdfPreview" => $responseData["previewData"],
            //         "fileAmount" => $responseData["fileAmount"],
            //         "pdfLinks" => $responseData["previewData"],
            //     ]
            //     ]);
        }
        catch(\Throwable $th){
            error_log($th->getMessage());
            return response()->json(['error' => 'An error occurred: ' . $th->getMessage()], 500);
        }

    }
    /* function buat masukin hasil data proses excel ke database
        bentuk array =
        $record = [child_codes: [], sponsor_category: string, sponsor_name: string}
    */
    public function getExcelData($record){
        
        $aprService = new AprService();
        $sponsor_id = SponsorController::storeSponsor($record["sponsor_name"], $record["sponsor_category"]);
        $pdfUrlArray = [];
      
        // array_map(function($childcode) use ($aprService, $sponsor_id, &$pdfUrlArray){
        //     $pdf_url = $aprService->getPdfUrl($childcode);
           
        //     $this->store([
        //         ""
        //     ])
        // }, $record["child_codes"]);

      
    }

    public function store(array $data){
        
        $sponsor = $this->storeSponsor->store($data["sponsor_name"], $data["sponsor_category_id"]);

        $children = $this->storeChildren->store([
            "child_code" => $data["child_code"],
            "sponsor_id" => $sponsor->id
        ]);

        $content = $this->storeContent->store([
            "child_id"   => $children->id,
            "content_url"  => $data["content_url"],
            "fiscal_year"  => $data["fiscal_year"]  
        ]);

    }

}

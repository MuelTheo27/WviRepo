<?php

namespace App\Http\Controllers;
use App\Http\Services\Apr\AprService;
use Log;
use Symfony\Component\Console\Output\ConsoleOutput;

use App\Http\Services\Excel\ExcelService;

use Illuminate\Http\Request;

class FileController extends Controller
{
    //



    /*function buat handle upload


    */
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

            $this->storeExcelData($processedData);
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
    public function storeExcelData($record){
        $aprService = new AprService();
        $sponsor_id = SponsorController::storeSponsor($record["sponsor_name"], $record["sponsor_category"]);

        array_map(function($childcode) use ($aprService, $sponsor_id){

            $content_id = ContentController::store($aprService->getPdfUrl($childcode));
            ChildController::store($childcode, $sponsor_id, $content_id );

        }, $record["child_codes"]);
    }

}

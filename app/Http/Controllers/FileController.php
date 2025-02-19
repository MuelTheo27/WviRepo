<?php

namespace App\Http\Controllers;

use App\Services\ExcelService;
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
            return response()->json(['error' => 'An error occurred: ' . $th->getMessage()], 500);
        }
   
       


    

    }
    /* function buat masukin hasil data proses excel ke database
        bentuk array =
        $record = [child_codes: [], sponsor_category: string, sponsor_name: string}
    */
    private function storeExcelData(array $record){

    }

}

<?php

namespace App\Http\Controllers;
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
            $output = new ConsoleOutput();
            $output->writeln(print_r($processedData, true));
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
    private function storeExcelData($record){

    }

}

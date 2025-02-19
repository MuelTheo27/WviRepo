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

        $file = $request->file("file");
        $excel_record = new ExcelService()->processExcel($file);

        if(!$excel_record){ return; }

        $this->store($excel_record);

    }
    /* function buat masukin hasil data proses excel ke database
        bentuk array =
        $record = [child_codes: [], sponsor_category: string, sponsor_name: string}
    */
    private function store(array $record){

    }

}

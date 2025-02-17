<?php

namespace App\Services;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet;
use App\Imports\StudentImport;
use App\Exports\StudentExport;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelService
{
    protected $httpService;
   
    public function __construct()
    {
      
    }

    
    public function getFileExtensions(UploadedFile $excel_file){
        if(strcmp($excel_file->getExtension(), "xlsx") == 0){
            return "Xlsx";
        }
        else if(strcmp($excel_file->getExtension(), "csv") == 0){
            return "Csv";
        }
        
    }

    public function getRequiredData(Worksheet $spreadsheet){
        $first_row_child_code = 5;
        $last_row_child_code = $spreadsheet->getHighestRow('A');
        $child_codes = [];

        for ($i=$first_row_child_code; $i <= $last_row_child_code; ++$i) { 
            array_push($child_codes, $spreadsheet->getCell("A" . $i)->getValue());

        }

        return [
            "sponsor_name" => $spreadsheet->getCell('B1')->getValue(),
            "sponsor_category" => $spreadsheet->getCell('B2')->getValue(),
            "child_codes" => $child_codes,
        ];
    }

    public function validateCellContent(Worksheet $spreadsheet): bool{
        if($spreadsheet->getCell('A1')->getValue() !== "sponsor_name" && 
           $spreadsheet->getCell('A2')->getValue() !== "sponsor_category" && 
           $spreadsheet->getCell('A4')->getValue() !== "child_code" )
        {
            return false;
        }
        
        return true;
    }

    public function processExcel(UploadedFile $excel_file)
    {
        

        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($this->getFileExtensions($excel_file));

            $spreadsheet = $reader->load($excel_file->getPathname())->getActiveSheet();
            
         



       
        } catch (\Throwable $th) {
            //throw $th;
        }
        
    }

  
}
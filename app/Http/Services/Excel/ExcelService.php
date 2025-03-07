<?php

namespace App\Http\Services\Excel;

use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\Console\Output\ConsoleOutput;
class ExcelService
{

    protected $console;
    public function __construct() {
     
        $this->console = new ConsoleOutput();
    }
    public function getFileExtensions(UploadedFile $excel_file)
    {
        $extension = strtolower($excel_file->getClientOriginalExtension());

        return match ($extension) {
            "xlsx" => "Xlsx",
            "csv"  => "Csv",
            default => throw new \Exception("Unsupported file type")
        };
    }

    public function getRequiredData(Worksheet $spreadsheet)
    {
        $table = [
        ];

        $index = 0;
        foreach ($spreadsheet->getRowIterator() as $row) {  
            if ($index === 0) { 
                $index++; 
                continue;
            }

            $cellIterator = $row->getCellIterator();

            $cellValues = [];
            foreach ($cellIterator as $cell) {
                $cellValue = $cell->getValue();
                if(isset($cellValue)){
                    array_push($cellValues, $cellValue);
                }
            }

            if (count($cellValues) >= 3) {
                array_push($table,["child_code" => $cellValues[0] ,
                "sponsor_name" =>$cellValues[1],
                "sponsor_category" => $cellValues[2]]);
            }

            else{
                throw new \Exception("An empty cell content exists");
            }

            $index++;
        }

        return $table;
    }

    public function validateCellContentTag(Worksheet $spreadsheet): bool
    {
        return $spreadsheet->getCell('A1')->getValue() === "child_code" &&
               $spreadsheet->getCell('B1')->getValue() === "sponsor_name" &&
               $spreadsheet->getCell('C1')->getValue() === "sponsor_category";
    }

    public function validateExcel(UploadedFile $excel_file){
        try {
            $reader = IOFactory::createReader($this->getFileExtensions($excel_file));
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($excel_file->getPathname())->getActiveSheet();
            if (!$this->validateCellContentTag($spreadsheet)) { 
                throw new \Exception("Incorrect cell content"); 
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function processExcel(UploadedFile $excel_file)
    {
        try {
            $reader = IOFactory::createReader($this->getFileExtensions($excel_file));
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($excel_file->getPathname())->getActiveSheet();
            
            if (!$this->validateCellContentTag($spreadsheet)) { 
                throw new \Exception("Incorrect cell content"); 
            }

            $table = $this->getRequiredData($spreadsheet);
        
            return $table;
            
        } catch (\Throwable $th) {
            throw new \Exception("Excel processing failed: " . $th->getMessage(), 0, $th);
        }
    }

    
}

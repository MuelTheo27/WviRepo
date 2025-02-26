<?php

namespace App\Http\Services\Excel;

use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelService
{
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
            "child_code" => [],
            "sponsor_category" => [],
            "sponsor_name" => []
        ];

        $index = 0;
        foreach ($spreadsheet->getRowIterator() as $row) {  
            if ($index === 0) { 
                $index++; 
                continue;
            }

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(true);

            $cellValues = [];
            foreach ($cellIterator as $cell) {
                $cellValues[] = $cell->getValue();
            }

            if (count($cellValues) >= 3) {
                
                array_push($table,["child_code" => $cellValues[0] ,
                "sponsor_name" =>$cellValues[1],
                "sponsor_category" => $cellValues[2]]);
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
            // if (!$table["child_code"] || !$table["sponsor_name"] || !$table["sponsor_category"]) {
            //     throw new \Exception("An empty cell content exists");
            // }

            return $table;
            
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    
}

<?php

namespace App\Http\Services\Excel;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\Console\Output\ConsoleOutput;

use App\Http\Services\Apr\AprService;
class ExcelService
{

    protected $console;
    protected $aprService;
    public function __construct() {
        $this->aprService = new AprService();
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


    public function validateColumnHeaders(Array $headers): bool
    {
        return $headers[0] === "Child IDN" && $headers[1] === "Sponsor ID" && $headers[2] === "Sponsor Name" && $headers[3] === "Sponsor Category";
    }

   
    public function processExcel(UploadedFile $excel_file)
    {
     
        try {
                
            if(!$this->getFileExtensions($excel_file)){
                throw new \Exception("Invalid file type");
            }

            $isFirstRow = true;

            $collections = (new FastExcel())->withoutHeaders()->import($excel_file->getPathname(), function($line) use (&$isFirstRow){
                /* line[4] buat content_url */
                if($isFirstRow){
                    if(!$this->validateColumnHeaders($line)){
                        throw new \Exception("Invalid column headers");
                    }
                    $isFirstRow = false;
                }
                else{
            
                    return $line;
                }
            }); 
      
            return $collections;
              
        } catch (\Throwable $th) {
            throw new \Exception("Excel processing failed: " . $th->getMessage(), 0, $th);
        }
    }

    
}

<?php

namespace App\Http\Services\Apr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;
use Illuminate\Http\Client\RequestException;
use XMLReader;
use Sabre\Xml\Reader;
use Error;
use Log;
use Symfony\Component\Console\Output\ConsoleOutput;
class AprService
{


    public function __construct()
    {
      
    }

    private function findValuebyName(array $array, string $name){
        $result = null;
        foreach($array as $item){
            if($item === $name && isset($array["value"]))  {
                return $array["value"];
            }
            else if(is_array($item)){
                $result = $this->findValuebyName($item, $name);
                if($result){
                    return $result;
                }
            }
        }
        return null;
    }
    public function filterChildElement(array $array, array &$filters){
        if(empty($filters)) {
            return true;
        }

        foreach($filters as $index => $matcher) {
            if(isset($matcher["filter_name"], $matcher["filter_value"]) && 
            isset($array["name"], $array["value"]) &&
               $array["name"] == $matcher["filter_name"] && 
               $array["value"] == $matcher["filter_value"]) 
            {    
                unset($filters[$index]);
            }
        }

        foreach($array as $item){
            if(is_array($item)){

                $status = $this->filterChildElement($item, $filters);
                
                if($status === true){ 
                    return $status; 
                }
            }
        }

        return false;
    }
    public function filterArrayByParentAndChildren(array $array, $parent_name, array &$filters){
       
        foreach($array as $item){
            if(is_array($item)){
                if(isset($array["name"]) && $array["name"] == $parent_name){

                    $status = $this->filterChildElement($item,$filters);
                    
                    if(isset($status) && $status == true){ 
                        return $array; 
                    }

                }

                $result = $this->filterArrayByParentAndChildren($item, $parent_name, $filters);
                
                if($result !== null) { 
                    return $result; 
                }
            }
        }

        return null;
    }
    
    public function getPdfUrl($child_code){
        $xml_data = $this->fetchAprEndpoint($child_code);
        
        if(!$xml_data) { return false; }

        $pdf_url_from_apr = $this->readXml($xml_data);
        
        if(!$pdf_url_from_apr) { return false; }

        return $pdf_url_from_apr;
   
    }

    public function readXml($xml_data){

        $input = $xml_data;
        try {
            $service = new Reader();
            $service->xml($input);

            $current_fiscal_year = (int)date('m') >= 10 ? (int)date('Y') : (int)date('Y') - 1;

            $apr_filter = [
                ["filter_name" => "{}category_value_description" , "filter_value" => "FY 2024"],
                ["filter_name" => "{}subtype" , "filter_value" => "Annual Progress Report"],
            ];

            $apr_row_array = $this->filterArrayByParentAndChildren($service->parse(), "{}csi_response_row", $apr_filter);
            if (is_null($apr_row_array)) {
                throw new \Exception("Error: filterArrayByParentAndChildren returned null for apr_row_array");
            }

            $pdf_filter = [
                ["filter_name" => "{}name", "filter_value" => "original"]
            ];

            $pdf_url = $this->filterArrayByParentAndChildren($apr_row_array, "{}csi_derivative_response", $pdf_filter);
            if (is_null($pdf_url)) {
                throw new \Exception("Error: filterArrayByParentAndChildren returned null for pdf_url");
            }

            $data_result = $this->findValuebyName($pdf_url, "{}url");
            if (is_null($data_result)) {
                throw new \Exception("Error: findValuebyName returned null for data_result");
            }

        return $data_result;
        
        } catch (\Throwable $th) {
            return new Error($th->getMessage());
        }

    }
    
    public function fetchAprEndpoint(string $child_code){
        $aprEndpoint = url(env("APR_URI"). "?").Arr::query(["child_code" => $child_code]) . "&" . env("APR_URI_ADDITIONAL_PARAM");

        $xmlResponse = Http::withBasicAuth(env("APR_USERNAME"), env("APR_PASSWORD"))
                   ->withHeaders(["wv-version" => "v500", 'Accept' => 'application/xml'])
                   ->get($aprEndpoint);
                   
        if($xmlResponse->successful()){
            return $xmlResponse->body();
        }
        else{
            Log::error("APR endpoint error: " . $xmlResponse->body());
            return null;
        }
       

    }

}
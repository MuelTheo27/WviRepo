<?php

namespace App\Http\Services\Apr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;
use Illuminate\Http\Client\RequestException;
use XMLReader;
use Sabre\Xml\Service;
use Error;
use Log;
class AprService
{


    public function __construct()
    {
      
    }


    public function helper($array, $tag, $val): bool {
        foreach ($array as $key => $value) {
            if (isset($value["name"]) && isset($value["value"])) {
            
                $Tag = str_replace("{}", "", $value["name"]);
                $Val = is_string($value["value"]) ? str_replace("{}", "", $value["value"]) : $value["value"];
    
                if ($Tag === $tag && $Val === $val) {
                    return true; 
                }
            }
        }
        return false;
    }
    
    public function iterator(array $array, string $tag, string $val): ?array {
        foreach ($array as $key => $value) {
            if (is_array($value) && count($value) > 0) {
             
                if ($this->helper($value, $tag, $val)) {
                    return $array;
                }
                $found = $this->iterator($value, $tag, $val);
                if ($found !== null) {
                    return $found; 
                }
            }
        }
    
        return null; 
    }

    public function getPdfUrl(string $child_code){
        $xml_data = $this->fetchAprEndpoint($child_code);
        
       
        if(!$xml_data) { return false; }

        $apr_data = $this->readXml($xml_data);
    
        if(!$apr_data) { return false; }

        $pdfUrl = "";

        array_map(function($data) use (&$pdfUrl){
            if(isset($data["name"]) && $data["name"] === "{}url"){
                $pdfUrl = $data["value"];
            }
        },$apr_data["value"]);

        return $pdfUrl;
   
    }

    public function readXml(String $input){

        try {
            $service = new Service();
            $xmlArray = $service->parse($input);
        } catch (\Throwable $th) {
            return new Error($th->getMessage());
        }

   
       
        $data_result = $this->iterator($xmlArray, "name", "original");

        return $data_result ? $data_result : Log::Error("Error on parsing xml");
        
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
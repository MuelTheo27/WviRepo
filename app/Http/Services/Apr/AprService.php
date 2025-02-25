<?php

namespace App\Http\Services\Apr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;
use Illuminate\Http\Client\RequestException;
use XMLReader;
use Sabre\Xml\Reader;
use Error;
use Log;
class AprService
{


    public function __construct()
    {
      
    }

    public function xmlParser(array $array, $name, string $children_name, string $value){
        if(isset($array["value"], $array["name"]) && $array["value"] == $value){ 
            print_r($array["value"]);
            return ["result" => true];
        }
        foreach($array as $item){
            if(is_array($item)){
                if(isset($array["name"]) && $array["name"] == $name){
                    $status = $this->xmlParser($item,$children_name, $children_name, $value);
                    if(isset($status["result"]) && $status["result"] == true) { return $array; }
                }
                $result = $this->xmlParser($item, $name, $children_name, $value);
                if($result) { return $result; }
            }
        }
    }
    
    public function getPdfUrl(){
        $xml_data = $this->fetchAprEndpoint("196811-1660");
        
       
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

    public function readXml($xml_data){

        $input = $xml_data;
        try {
            $service = new Reader();
            $service->xml($input);
            dd($this->xmlParser($service->parse(), "{}csi_catalog_attribute", "{}category_value_description", "FY 2025"));
        } catch (\Throwable $th) {
            return new Error($th->getMessage());
        }

        

        
        // $data_result = $this->iterator($xmlArray, "name", "original");

        // return $data_result ? $data_result : Log::Error("Error on parsing xml");
        
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
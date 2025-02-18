<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;
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

     /* iterator(one of array member key) : return the main array 
        function should returning an array that matches one of the member with the function argument
    */
    public function iterator(array $array, string $tag, string $val): array {
        $result = [];
        foreach ($array as $key => $value) {
            if($array[$tag] === $value){
                return $array;
            }

            if(is_array($value) && count($value) > 0){
                $content = $this->iterator($value, $tag, $val );
                if(!is_null($content)) {
                    $result = $content;
                }
            }
        }
        return $result;
    }

    public function getPdfUrl(string $child_code){
        $xml_data = $this->fetchAprEndpoint($child_code);
        
        if(!$xml_data) { return false; }

        $apr_data = $this->readXml($xml_data);

        if(!$apr_data) { return false; }

        return $apr_data["url"];
    }

    public function readXml(String $input){

        try {
            $xmlArray = new Service()->parse($input);
        } catch (\Throwable $th) {
            return new Error($th->getMessage());
        }
       
        $data_result = $this->iterator($xmlArray, "name", "original");

        return $data_result ? $data_result : Log::Error("Error on parsing xml");
        
    }
    
    public function fetchAprEndpoint(string $child_code){
        $aprEndpoint = url(env("APR_URI"), ["child_code" => $child_code]) . env("APR_URI_ADDITIONAL_PARAM");
        $xmlResponse = Http::withBasicAuth(env("APR_USERNAME"), env("APR_PASSWORD"))
                   ->withHeaders(["wv-version" => "v500", 'Accept' => 'application/xml'])
                   ->get($aprEndpoint);
        if($xmlResponse->successful()) {
            return $xmlResponse->body();
        }
        else{
            $xmlResponse->onError(function(RequestException $err){
                Log::error("APR endpoint error: " . $err->getMessage());
            });
            return null;
        }
       

    }
}
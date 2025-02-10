<?php

namespace App\Http\Controllers;
use Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Sabre\Xml\Service;

class AprController extends Controller
{
    //
    public function findValueWithKey(array $list, $key){
        $value = null;
        foreach($list as $item){
          
            if(array_key_exists("name", $list)){
                if($list["name"] === $key){
                    $value = $list["value"];
                }
            }
            if(is_array($item)){
                /* rekursi , kalau hasilnya null jangan di tambahin ke variable temp */
                $temp = $this->findValueWithKey($item, $key);
                if ($temp !== null) {
                    if (is_array($value)) {
                        $value = array_merge($value, $temp);  
                    } else {
                        $value = $temp;  
                    }
                }
            }
        }
        return $value;
    }
    
    public function getApr(){

        $url = env("APR_URI");
        $password = env("APR_PASSWORD");
        $username = env("APR_USERNAME");

        $response = Http::withBasicAuth($username, $password)->withHeaders(["wv-version" => "v500", 'Accept' => 'application/xml'])->get($url);
        $response->onError(function ($err){
            Log::error('Request failed: ' . $err->getMessage());
        });

        $service = new Service();
        $parsed_xml = $service->parse($response->body());
        $value = $this->findValueWithKey($parsed_xml, "{}child_code");
        
        if($value == null){
            print_r("Key-Value Pair not found");
        }
        else{
            print_r($value);
        }
}

}

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
        $value = [];
        foreach($list as $item){
          
            if(array_key_exists("name", $list)){
                if($list["name"] === $key){
                    array_push($value, $list["value"]);
                }
            }
            if(is_array($item)){
                /* rekursi , kalau hasilnya null jangan di tambahin ke variable temp */
                $temp = $this->findValueWithKey($item, $key);
                if ($temp !== null) {
                 
                    $value = array_merge($value, $temp);  
                 
                }
            }
        }
        return $value;
    }
    
    public function getAPR(){

        $url = env("APR_URI");
        $password = env("APR_PASSWORD");
        $username = env("APR_USERNAME");

        $response = Http::withBasicAuth($username, $password)->withHeaders(["wv-version" => "v500", 'Accept' => 'application/xml'])->get($url);
        
        $response->onError(function ($err){
            Log::error('Request failed: ' . $err->getMessage());
            return;
        });

        $service = new Service();
        $parsed_xml = $service->parse($response->body());
        $value = $this->findValueWithKey($parsed_xml, "{}url");
        
        if($value == null){
            print_r("Key-Value Pair not found");
        }
        else{
            $this->retrieveAPRUrl($value);
        }
    }

    public function retrieveAPRUrl(array $arr){
        if(count($arr) === 9){
            $original_url = $arr[0];
            $thumb_url = $arr[3];
            $web_url = $arr[8];

            print_r($original_url);
            print_r($thumb_url);
            print_r($web_url);
        }
        else{
            Log::error("Wrong array!");
        }


    }

}

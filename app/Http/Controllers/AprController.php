<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
class AprController extends Controller
{
    //
    public function getApr(){
        $url = 'https://serviceonemedia.wvi.org:8443/media/child';

        $response = Http::withBasicAuth("", "")->withHeader("wv-version", "v500")->get($url);

        dd($response);
}

}

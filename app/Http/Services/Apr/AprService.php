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
    public $console;

    public function __construct() {
        $this->console = new ConsoleOutput();
    }

    public function fetchRMTEndpoint(string $child_idn){
        $aprEndpoint = url(env("APR_URI"). "?") .env("APR_URI_ADDITIONAL_PARAM") . "&" . Arr::query(["child_code" => $child_idn]);

        $xmlResponse = Http::withBasicAuth(env("APR_USERNAME"), env("APR_PASSWORD"))
                   ->withHeaders(["wv-version" => "v500", 'Accept' => 'application/xml'])
                   ->get($aprEndpoint);
                   
        if($xmlResponse->successful()){
            return $xmlResponse->body();
        }
        else{
            throw new RequestException($xmlResponse);
        }
    }

    public function getAnnualPerformanceReportUrl(string $child_idn, string $fiscal_year){
        try {

            $rmtXml = simplexml_load_string($this->fetchRMTEndpoint($child_idn));     

            $csiRows = $rmtXml->xpath("csi_response_query/csi_response_row");

            $formattedFiscalYear = "FY " . $fiscal_year;

            $contentUrl = null;

            foreach($csiRows as $row){
                if(!$row->xpath(".//category_value_description[text()='{$formattedFiscalYear}']")){
                    continue;
                }
                if(!$row->xpath(".//subtype[text()='Annual Progress Report']")){
                    continue;
                }

                $contentUrl = $row->xpath(".//csi_derivative_response/name[text()='original']/../url");


                if ($contentUrl === null) {
                    throw new Error("Annual Progress Report not found for child ID: {$child_idn} for fiscal year: {$fiscal_year}");
                }
                      
                return ((string) $contentUrl[0]);
               
            }

        } catch (RequestException $th) {
            throw new Error("Failed to fetch RMT endpoint");
        }
        catch (\Exception $e) {
            throw new Error($e->getMessage());
        }
    }

}
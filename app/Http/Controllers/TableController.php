<?php

namespace App\Http\Controllers;

use App\Models\Child;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TableController extends Controller
{
    /*
    function buat dapetin semua data buat tabel
    */
    public function getTableData() {}
    /*
    function buat view table ( ambil data dari category child sponsor )
    */
    public function getTablePage()
    {

        $child = Child::all();
        dd($child);
        return;
    }
    /* function buat search */
    public function searchSponsor(Response $response)
    {
        // query nya dari url - parameter
 
        
    }

    /* function buat sort */
    public function sortSponsor(Response $response) {}
}

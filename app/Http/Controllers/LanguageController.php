<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LanguageController extends Controller
{
    /**
    * Returns existing languages.
    *
    * @return array response includes array of languages
    *
    * @api
    */
    public function index() {
        return json_encode(DB::connection('mysql')->table('languages')->get());
    }
}

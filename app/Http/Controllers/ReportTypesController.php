<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportTypesController extends Controller
{
    public function index() {
        return DB::connection('mysql')->table('report_types')->orderBy('name', 'asc')->get();
    }
}

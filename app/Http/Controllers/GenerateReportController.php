<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GenerateReportController extends Controller
{
    public function store() {
        request()->validate([
            'reports.*' => 'exists:report_types,id'
        ]);

        // dispatch job for each report type but only send one email

        return 'Course successfully assigned a language.';
    }
}

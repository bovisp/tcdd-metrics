<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BadgeController extends Controller
{
    public function index() {
        //only badges not already in badge-languages
        $assignedBadgeIds = DB::connection('mysql')->table('badge_language')->select('badge_id')->get()->map(function ($assignedBadgeId) {
            return $assignedBadgeId->badge_id;
        })->toArray();

        return json_encode(DB::connection('mysql2')->table('mdl_badge')->whereNotIn('id', $assignedBadgeIds)->get());
    }
}

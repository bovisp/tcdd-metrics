<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BadgeController extends Controller
{
    /**
    * Returns array of training portal badges that have not already been assigned languages.
    *
    * @return array response includes array of training portal badges
    *
    * @api
    */
    public function index() {
        //only badges not already in badge-languages
        $assignedBadgeIds = DB::connection('mysql')->table('badge_language')
            ->select('badge_id')->get()
            ->map(function ($assignedBadgeId) {
                return $assignedBadgeId->badge_id;
            })->toArray();
        
        return DB::connection('mysql2')->table('mdl_badge')
            ->whereNotIn('id', $assignedBadgeIds)
            ->orderBy('name', 'asc')->get();
    }
}

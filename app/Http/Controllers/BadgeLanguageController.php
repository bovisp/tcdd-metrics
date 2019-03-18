<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BadgeLanguageController extends Controller
{
    public function store() {
        request()->validate([
            'language_id' => 'exists:languages,id',
            'badge_id' => 'exists:mysql2.mdl_badge,id'
        ]);

        DB::connection('mysql')->table('badge_language')->insert([
            'badge_id' => request('badge_id'), 
            'language_id' => request('language_id')
        ]);
    }

    public function update($badgeLanguageId) {
        request()->validate([
            'language_id' => 'exists:languages,id'
        ]);

        DB::connection('mysql')->table('badge_language')->where(['id' => $badgeLanguageId])
        ->update([
            'language_id' => request('language_id')
        ]);
    }

    public function destroy($badgeLanguageId) {
        DB::connection('mysql')->table('badge_language')->delete([
            'id' => $badgeLanguageId
        ]);
    }
}
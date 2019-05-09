<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BadgeLanguageController extends Controller
{
    public function store() {
        request()->validate([
            'language_id' => 'required|exists:languages,id',
            'badge_id' => 'required|exists:mysql2.mdl_badge,id'
        ]);

        DB::connection('mysql')->table('badge_language')->insert([
            'badge_id' => request('badge_id'),
            'language_id' => request('language_id'),
        ]);

        return 'Badge(s) successfully assigned a language.';
    }

    public function update($badgeLanguageId) {
        request()->validate([
            'language_id' => 'exists:languages,id'
        ]);

        if(request()->query('confirm') === 'false') {
            if($this->checkIfBadgeIssued($badgeLanguageId)) {
                return response("Badge has already been issued.", 422);
            }
        }

        DB::connection('mysql')->table('badge_language')
        ->where(['id' => $badgeLanguageId])
        ->update([
            'badge_id' => request('badge_id'),
            'language_id' => request('language_id')
        ]);

        return response("Successfully updated this badge's language.", 200);
    }

    public function destroy($badgeLanguageId) {
        if(request()->query('confirm') === 'false') {
            if($this->checkIfBadgeIssued($badgeLanguageId)) {
                return response("Badge has already been issued.", 422);
            }
        }

        DB::connection('mysql')->table('badge_language')
        ->delete([
            'id' => $badgeLanguageId
        ]);

        return response("Successfully deleted this badge's language.", 200);
    }

    public function index() {
        return DB::connection('mysql')->table('badge_language')
            ->join('moodledb.mdl_badge', 'badge_language.badge_id', '=', 'moodledb.mdl_badge.id')
            ->join('languages', 'badge_language.language_id', '=', 'languages.id')
            ->select('badge_language.id', 'badge_language.badge_id as badge_id', 'moodledb.mdl_badge.name as badge_name', 'languages.id as language_id', 'languages.name as language_name')
            ->orderBy('badge_language.id', 'asc')
            ->get();
    }

    protected function checkIfBadgeIssued($badgeLanguageId) {
        $badgeId = DB::connection('mysql')->table('badge_language')
            ->where(['id' => $badgeLanguageId])
            ->select('badge_id')
            ->get()
            ->map(function ($badgeLanguage) {return $badgeLanguage->badge_id;})[0];

        return DB::connection('mysql2')->table('mdl_badge_issued')
            ->where(['badgeid' => $badgeId])
            ->exists();
    }
}

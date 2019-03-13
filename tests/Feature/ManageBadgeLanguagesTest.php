<?php

namespace Tests\Feature;

use App\Language;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManageBadgeLanguagesTest extends TestCase
{
    /** @test */
    public function a_user_can_assign_a_language_to_a_badge()
    {
        //create a language
        $language = factory(Language::class)->create();
        //create a badge
        $badge = factory(Badge::class)->create();

        $attributes = [
            'language_id' => $language->id,
            'badge_id' => $badge->id
        ];
        //post to badge-languages...or update badges/{id}...?
        $this->post('/badge-languages', $attributes);

        $this->assertDatabaseHas('badge_language', $attributes);
    }
}

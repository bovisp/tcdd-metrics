<?php

namespace Tests\Feature;

use App\Language;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ManageBadgeLanguagesTest extends TestCase
{
    use DatabaseMigrations;
    /** @test */
    public function a_user_can_assign_a_language_to_a_badge()
    {
        $this->withoutExceptionHandling();
        //create a language
        $language = factory(Language::class)->create();
        //create a badge
        //$badge = factory(Badge::class)->create();
        $badge = new \stdClass;
        $badge->id = 1;

        $attributes = [
            'language_id' => $language->id,
            'badge_id' => $badge->id
        ];
        //post to badge-languages...or update badges/{id}...?
        $this->post('/badge-languages', $attributes);

        $this->assertDatabaseHas('badge_language', $attributes);
    }

    /** @test */
    public function it_requires_the_language_id_to_exist_in_the_database()
    {
        $language = factory(Language::class)->create();
        $badge = new \stdClass;
        $badge->id = 1;

        $attributes = [
            'language_id' => 99999999999999,
            'badge_id' => $badge->id
        ];
        $this->post('/badge-languages', $attributes)->assertSessionHasErrors(['language_id']);
    }

    /** @test */
    public function it_requires_the_badge_id_to_exist_in_the_database()
    {
        $language = factory(Language::class)->create();
        $badge = new \stdClass;
        $badge->id = 1;

        $attributes = [
            'language_id' => $language->id,
            'badge_id' => 9999999999999999
        ];
        $this->post('/badge-languages', $attributes)->assertSessionHasErrors(['badge_id']);
    }
}

<?php

namespace Tests\Feature;

use App\Language;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ManageBadgeLanguagesTest extends TestCase
{
    use DatabaseMigrations;
    
    /** @test */
    public function a_user_can_view_their_badge_languages() 
    {
        $this->withoutExceptionHandling();

        //create a language

        $language = new \stdClass;
        $language->id = 1;
        $language->name = 'English';
        DB::connection('mysql')->table('languages')->insert([
            'id' => $language->id,
            'name' => $language->name
        ]);

        //create a badge
        $badge = new \stdClass;
        $badge->id = 1;
        $badge->name = 'test badge';

        $attributes = [
            'language_id' => $language->id,
            'language_name' => $language->name,
            'badge_id' => $badge->id,
            'badge_name' => $badge->name
        ];

        $this->post('/badge-languages', $attributes);

        $this->get('/badge-languages')->assertJsonFragment([
            'language_id' => $language->id,
            'badge_id' => $badge->id]
        );
    }

    /** @test */
    public function a_user_can_assign_a_language_to_a_badge() 
    {
        $this->withoutExceptionHandling();

        //create a language
        $language = new \stdClass;
        $language->id = 1;
        $language->name = 'English';
        DB::connection('mysql')->table('languages')->insert([
            'id' => $language->id,
            'name' => $language->name
        ]);

        //create a badge
        $badge = new \stdClass;
        $badge->id = 1;

        $attributes = [
            'language_id' => $language->id,
            'badge_id' => $badge->id
        ];

        $this->post('/badge-languages', $attributes);

        $this->assertDatabaseHas('badge_language', $attributes);
    }

    /** @test */
    public function it_requires_the_language_id_to_exist_in_the_database()
    {
        $language = new \stdClass;
        $language->id = 1;
        $language->name = 'English';
        DB::connection('mysql')->table('languages')->insert([
            'id' => $language->id,
            'name' => $language->name
        ]);

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
        $language = new \stdClass;
        $language->id = 1;
        $language->name = 'English';
        DB::connection('mysql')->table('languages')->insert([
            'id' => $language->id,
            'name' => $language->name
        ]);

        $badge = new \stdClass;
        $badge->id = 1;

        $attributes = [
            'language_id' => $language->id,
            'badge_id' => 9999999999999999
        ];

        $this->post('/badge-languages', $attributes)->assertSessionHasErrors(['badge_id']);
    }

    /** @test */
    public function a_user_can_remove_a_badge_language()
    {
        $this->withoutExceptionHandling();

        //create a language
        $language = new \stdClass;
        $language->id = 1;
        $language->name = 'English';
        DB::connection('mysql')->table('languages')->insert([
            'id' => $language->id,
            'name' => $language->name
        ]);

        //create a badge
        $badge = new \stdClass;
        $badge->id = 1;

        $attributes = [
            'language_id' => $language->id,
            'badge_id' => $badge->id
        ];

        $badgeLanguageId = DB::connection('mysql')->table('badge_language')->insertGetId($attributes);
        $this->assertDatabaseHas('badge_language', $attributes);
        
        $this->delete("/badge-languages/{$badgeLanguageId}");
        $this->assertDatabaseMissing('badge_language', $attributes);
    }

    /** @test */
    public function a_user_can_update_a_badge_language()
    {
        $this->withoutExceptionHandling();

        //create a language
        $language1 = new \stdClass;
        $language1->id = 1;
        $language1->name = 'English';
        DB::connection('mysql')->table('languages')->insert([
            'id' => $language1->id,
            'name' => $language1->name
        ]);

        $language2 = new \stdClass;
        $language2->id = 2;
        $language2->name = 'French';
        DB::connection('mysql')->table('languages')->insert([
            'id' => $language2->id,
            'name' => $language2->name
        ]);

        //create a badge
        $badge = new \stdClass;
        $badge->id = 1;

        $attributes = [
            'language_id' => $language1->id,
            'badge_id' => $badge->id
        ];

        $badgeLanguageId = DB::connection('mysql')->table('badge_language')->insertGetId($attributes);
        $this->assertDatabaseHas('badge_language', $attributes);
        
        $this->put("/badge-languages/{$badgeLanguageId}", [
            'badge_id'=>$badge->id,
            'language_id'=>$language2->id
        ]);

        $this->assertDatabaseHas('badge_language', [
            'id'=>$badgeLanguageId,
            'language_id'=>$language2->id
        ]);
    }
}

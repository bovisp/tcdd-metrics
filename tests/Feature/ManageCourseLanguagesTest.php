<?php

namespace Tests\Feature;

use App\Language;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ManageCourseLanguagesTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_assign_a_language_to_a_course()
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
        //create a course
        $course = new \stdClass;
        $course->id = 1;
        $course->name = 'test course';

        $attributes = [
            'language_id' => $language->id,
            'course_id' => $course->id,
        ];

        $this->post('/course-languages', $attributes);

        $this->assertDatabaseHas('course_language', $attributes);
    }

    /** @test */
    public function a_user_can_view_their_course_languages() {
        $this->withoutExceptionHandling();
        //create a language
        //$language = factory(Language::class)->create();
        $language = new \stdClass;
        $language->id = 1;
        $language->name = 'English';
        DB::connection('mysql')->table('languages')->insert([
            'id' => $language->id,
            'name' => $language->name
        ]);
        //create a course
        $course = new \stdClass;
        $course->id = 1;
        $course->name = 'test course';

        $attributes = [
            'language_id' => $language->id,
            'language_name' => $language->name,
            'course_id' => $course->id,
            'course_name' => $course->name
        ];

        $this->post('/course-languages', $attributes);

        $this->get('/course-languages')->assertJsonFragment([
            'language_id' => $language->id,
            'course_id' => $course->id]
        );
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
        $course = new \stdClass;
        $course->id = 1;

        $attributes = [
            'language_id' => 99999999999999,
            'course_id' => $course->id
        ];
        $this->post('/course-languages', $attributes)->assertSessionHasErrors(['language_id']);
    }

    /** @test */
    public function it_requires_the_course_id_to_exist_in_the_database()
    {
        $language = new \stdClass;
        $language->id = 1;
        $language->name = 'English';
        DB::connection('mysql')->table('languages')->insert([
            'id' => $language->id,
            'name' => $language->name
        ]);
        $course = new \stdClass;
        $course->id = 1;

        $attributes = [
            'language_id' => $language->id,
            'course_id' => 9999999999999999
        ];
        $this->post('/course-languages', $attributes)->assertSessionHasErrors(['course_id']);
    }

    /** @test */
    public function a_user_can_remove_a_course_language()
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
        //create a course
        $course = new \stdClass;
        $course->id = 1;

        $attributes = [
            'language_id' => $language->id,
            'course_id' => $course->id
        ];

        $courseLanguageId = DB::connection('mysql')->table('course_language')->insertGetId($attributes);
        $this->assertDatabaseHas('course_language', $attributes);
        
        $this->delete("/course-languages/{$courseLanguageId}");
        $this->assertDatabaseMissing('course_language', $attributes);

    }

    /** @test */
    public function a_user_can_update_a_course_language()
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
        //create a course
        $course = new \stdClass;
        $course->id = 1;

        $attributes = [
            'language_id' => $language1->id,
            'course_id' => $course->id
        ];
        
        $courseLanguageId = DB::connection('mysql')->table('course_language')->insertGetId($attributes);
        $this->assertDatabaseHas('course_language', $attributes);
        
        $this->put("/course-languages/{$courseLanguageId}", [
            'course_id'=>$course->id,
            'language_id'=>$language2->id
        ]);

        $this->assertDatabaseHas('course_language', [
            'id'=>$courseLanguageId,
            'language_id'=>$language2->id
        ]);
    }
}

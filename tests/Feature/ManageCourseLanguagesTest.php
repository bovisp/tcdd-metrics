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
}

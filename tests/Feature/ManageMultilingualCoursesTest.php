<?php

namespace Tests\Feature;

use App\Language;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ManageMultilingualCoursesTest extends TestCase
{
    use DatabaseMigrations;
    /** @test */
    public function a_user_can_associate_courses_as_a_multilingual_course()
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
}

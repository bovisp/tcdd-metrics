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
    public function a_user_can_associate_courses_as_a_multilingual_course() {
        $this->withoutExceptionHandling();
        //create a multilingual course group
        $mlangcoursegroup = new \stdClass;
        $mlangcoursegroup->id = 1;
        $mlangcoursegroup->name = 'test group';
        DB::connection('mysql')->table('multilingual_course_group')->insert([
            'id' => $mlangcoursegroup->id,
            'name' => $mlangcoursegroup->name
        ]);
        //create a course
        $course = new \stdClass;
        $course->id = 1;
        $course->name = 'test course';

        $attributes = [
            'multilingual_course_group_id' => $mlangcoursegroup->id,
            'course_id' => $course->id,
        ];

        $this->post('/multilingual-courses', $attributes);
        $this->assertDatabaseHas('multilingual_course', $attributes);
    }

    /** @test */
    public function a_user_can_view_their_multilingual_courses() {
        $this->withoutExceptionHandling();
        //create a multilingual course group
        $mlangcoursegroup = new \stdClass;
        $mlangcoursegroup->id = 1;
        $mlangcoursegroup->name = 'test group';
        DB::connection('mysql')->table('multilingual_course_group')->insert([
            'id' => $mlangcoursegroup->id,
            'name' => $mlangcoursegroup->name
        ]);
        //create a course
        $course = new \stdClass;
        $course->id = 1;
        $course->name = 'test course';

        $attributes = [
            'multilingual_course_group_id' => $mlangcoursegroup->id,
            'course_id' => $course->id,
        ];

        $this->post('/multilingual-courses', $attributes);

        $this->get('/multilingual-courses')->assertJsonFragment([
            'multilingual_course_group_id' => $mlangcoursegroup->id,
            'course_id' => $course->id]
        );
    }

    /** @test */
    public function it_requires_the_multilingual_course_group_id_to_exist_in_the_database()
    {
        $mlangcoursegroup = new \stdClass;
        $mlangcoursegroup->id = 1;
        $mlangcoursegroup->name = 'test group';
        DB::connection('mysql')->table('multilingual_course_group')->insert([
            'id' => $mlangcoursegroup->id,
            'name' => $mlangcoursegroup->name
        ]);
        //create a course
        $course = new \stdClass;
        $course->id = 1;
        $course->name = 'test course';

        $attributes = [
            'multilingual_course_group_id' => 999999999,
            'course_id' => $course->id,
        ];

        $this->post('/multilingual-courses', $attributes)->assertSessionHasErrors(['multilingual_course_group_id']);
    }

    /** @test */
    public function it_requires_the_course_id_to_exist_in_the_database()
    {
        $mlangcoursegroup = new \stdClass;
        $mlangcoursegroup->id = 1;
        $mlangcoursegroup->name = 'test group';
        DB::connection('mysql')->table('multilingual_course_group')->insert([
            'id' => $mlangcoursegroup->id,
            'name' => $mlangcoursegroup->name
        ]);
        //create a course
        $course = new \stdClass;
        $course->id = 1;
        $course->name = 'test course';

        $attributes = [
            'multilingual_course_group_id' => $mlangcoursegroup->id,
            'course_id' => 999999999,
        ];

        $this->post('/multilingual-courses', $attributes)->assertSessionHasErrors(['course_id']);
    }

    /** @test */
    public function a_user_can_remove_a_multilingual_course()
    {
        $this->withoutExceptionHandling();
        //create a multilingual course group
        $mlangcoursegroup = new \stdClass;
        $mlangcoursegroup->id = 1;
        $mlangcoursegroup->name = 'test group';
        DB::connection('mysql')->table('multilingual_course_group')->insert([
            'id' => $mlangcoursegroup->id,
            'name' => $mlangcoursegroup->name
        ]);
        //create a course
        $course = new \stdClass;
        $course->id = 1;
        $course->name = 'test course';

        $attributes = [
            'multilingual_course_group_id' => $mlangcoursegroup->id,
            'course_id' => $course->id,
        ];

        $multilingualCourseId = DB::connection('mysql')->table('multilingual_course')->insertGetId($attributes);
        $this->assertDatabaseHas('multilingual_course', $attributes);

        $this->delete("/multilingual-courses/{$multilingualCourseId}");
        $this->assertDatabaseMissing('multilingual_course', $attributes);
    }
}

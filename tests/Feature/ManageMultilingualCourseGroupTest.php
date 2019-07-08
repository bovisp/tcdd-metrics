<?php

namespace Tests\Feature;

use App\Language;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ManageMultilingualCourseGroupTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_create_a_multilingual_course_group() {
        $this->withoutExceptionHandling();

        $name = 'test';
        $attributes = [
            'course_group_name' => $name
        ];

        $this->post('/multilingual-course-groups', $attributes);
        $this->assertDatabaseHas('multilingual_course_group', ['name' => $name]);
    }

    /** @test */
    public function a_user_can_view_their_multilingual_course_groups() {
        $this->withoutExceptionHandling();
        
        $name = 'test';
        $attributes = [
            'course_group_name' => $name
        ];

        $this->post('/multilingual-course-groups', $attributes);
        $this->get('/multilingual-course-groups')->assertJsonFragment([
            'name' => $name
        ]);
    }

    /** @test */
    public function it_requires_the_course_group_name_to_be_unique()
    {   
        $name = 'test';
        $attributes = [
            'course_group_name' => $name
        ];

        DB::connection('mysql')->table('multilingual_course_group')->insert(['name' => $name]);
        $this->assertDatabaseHas('multilingual_course_group', ['name' => $name]);

        $this->post('/multilingual-course-groups', $attributes)->assertSessionHasErrors(['course_group_name']);
    }

    /** @test */
    public function a_user_can_remove_a_multilingual_course_group()
    {
        $this->withoutExceptionHandling();

        $name = 'test';

        $id = DB::connection('mysql')->table('multilingual_course_group')->insertGetId(['name' => $name]);
        $this->assertDatabaseHas('multilingual_course_group', ['name' => $name]);

        $this->delete("/multilingual-course-groups/{$id}");
        $this->assertDatabaseMissing('multilingual_course_group', ['name' => $name]);
    }

    /** @test */
    public function a_user_can_update_a_multilingual_course_group()
    {
        $this->withoutExceptionHandling();

        $name = 'test';

        $id = DB::connection('mysql')->table('multilingual_course_group')->insertGetId(['name' => $name]);
        $this->assertDatabaseHas('multilingual_course_group', ['name' => $name]);

        $name2 = 'test2';

        $this->put("/multilingual-course-groups/{$id}", [
            'course_group_name' => $name2
        ]);

        $this->assertDatabaseHas('multilingual_course_group', [
            'name' => $name2
        ]);
    }
}

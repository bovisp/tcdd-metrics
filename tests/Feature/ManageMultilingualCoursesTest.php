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
    public function a_user_can_view_their_()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}

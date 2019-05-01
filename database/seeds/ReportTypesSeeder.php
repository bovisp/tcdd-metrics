<?php

use Illuminate\Database\Seeder;

class ReportTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::connection('mysql')->table('report_types')->insert([
            ['name' => 'Course Completions'],
            ['name' => 'Course Views']
        ]);
    }
}

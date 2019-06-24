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
            [
                'id' => 1,
                'name' => 'Training Portal Course Completions'
            ],
            [
                'id' => 2,
                'name' => 'Training Portal Course Views'
            ],
            [
                'id' => 3,
                'name' => 'COMET Completions'
            ],
            [
                'id' => 4,
                'name' => 'COMET Accesses'
            ],
        ]);
    }
}

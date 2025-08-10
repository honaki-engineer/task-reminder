<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $taskCategories = [
            [
                'name' => '重要 & 緊急',
                'slug' => 'important-urgent'
            ],
            [
                'name' => '重要',
                'slug' => 'important'
            ],
            [
                'name' => '緊急',
                'slug' => 'urgent'
            ],
            [
                'name' => 'その他',
                'slug' => 'other'
            ],
        ];

        foreach($taskCategories as $taskCategory) {
            DB::table('task_categories')->insert([
                'name' => $taskCategory['name'],
                'slug' => $taskCategory['slug'],
            ]);
        }
    }
}

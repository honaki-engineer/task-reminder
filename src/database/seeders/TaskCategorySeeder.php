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
                'id'   => 1,
                'name' => '重要 & 緊急',
                'slug' => 'important-urgent'
            ],
            [
                'id'   => 2,
                'name' => '重要',
                'slug' => 'important'
            ],
            [
                'id'   => 3,
                'name' => '緊急',
                'slug' => 'urgent'
            ],
            [
                'id'   => 4,
                'name' => 'その他',
                'slug' => 'other'
            ],
        ];

        foreach($taskCategories as $taskCategory) {
            DB::table('task_categories')->insert([
                'id'   => $taskCategory['id'],
                'name' => $taskCategory['name'],
                'slug' => $taskCategory['slug'],
            ]);
        }
    }
}

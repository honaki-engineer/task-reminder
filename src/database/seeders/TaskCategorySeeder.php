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
        $taskCategories = ['１：緊急*重要', '２：重要', '３：緊急', '４：その他'];

        foreach($taskCategories as $taskCategory) {
            DB::table('task_categories')->insert([
                'name' => $taskCategory,
            ]);
        }
    }
}

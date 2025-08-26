<?php 

namespace App\Services;

use App\Models\Task;
use App\Models\TaskCategory;
use App\Support\BackUrlSanitizer;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    // ----- index - store - show - edit - update - destroy - oneDay - complete -----------------------------------------------------
    // ----- ユーザー情報
    public static function getUser() {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $user;
    }


    // ----- index - edit ---------------------------------------------------------------------------------------------------
    // ----- フォーカスマトリックス情報
    public static function getTaskCategories() {
        $taskCategories = TaskCategory::orderBy('id')->get();

        return $taskCategories;
    }     


    // ----- store ---------------------------------------------------------------------------------------------------
    // ----- 保存
    public static function storeTask($user, $request) {
        Task::create([
            'user_id' => $user->id,
            'task_category_id' => $request->task_category,
            'title' => $request->title,
            'description' => $request->description,
            'start_at' => $request->start_at, // TaskRequestで結合済み
            'end_at' => $request->end_at, // TaskRequestで結合済み
            'is_completed' => false,
        ]);
    }
    

    // ----- show - edit -------------------------------------------------------------------------------------------------
    // ----- タスク情報取得withフォーカスマトリックス
    public static function findTaskWithTaskCategory($user, $id) {
        $task = $user->tasks()
            ->with('taskCategory')  
            ->findOrFail($id);

        return $task;
    }  

    // ---- 外部URLならデフォルトに置き換えて、安全なback_urlを返す関数(query)
    public static function getSafeBackUrlFromQuery($request) {
        $default = route('tasks.index');
        $backUrl = $request->query('back_url', '');
        return BackUrlSanitizer::sanitize($backUrl, $default, config('app.url'));
    }


    // ----- update - destroy - complete ------------------------------------------------------------------------------------------------
    // ----- タスク情報取得
    public static function getTask($user, $id) {
        $task = $user->tasks()->findOrFail($id);

        return $task;
    }

    // ---- 外部URLならデフォルトに置き換えて、安全なback_urlを返す関数(input)
    public static function getSafeBackUrlFromInput($request) {
        $default = route('tasks.index');
        $backUrl = $request->input('back_url', '');
        return BackUrlSanitizer::sanitize($backUrl, $default, config('app.url'));
    }



    // ----- update ------------------------------------------------------------------------------------------------
    // ----- 保存
    public static function updateTask($task, $user, $request) {
        $task->update([
            'user_id' => $user->id,
            'task_category_id' => $request->task_category,
            'title' => $request->title,
            'description' => $request->description,
            'start_at' => $request->start_at, // TaskRequestで結合済み
            'end_at' => $request->end_at, // TaskRequestで結合済み
            'is_completed' => $task->is_completed,
        ]);
    }
}
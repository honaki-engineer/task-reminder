<?php 

namespace App\Services;

use App\Models\Task;
use App\Models\TaskCategory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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


    // ----- index - update ---------------------------------------------------------------------------------------------------
    // ----- フォームの date + time を結合
    public static function combineStartDateTime($request) {
        $startAt = Carbon::parse(
            $request->start_date.' '.($request->start_time),
        );

        return $startAt;
    }
    public static function combineEndDateTime($request) {
        $endAt = Carbon::parse(
            $request->end_date.' '.($request->end_time),
        );

        return $endAt;
    }


    // ----- store ---------------------------------------------------------------------------------------------------
    // ----- 保存
    public static function storeTask($user, $request, $startAt, $endAt) {
        Task::create([
            'user_id' => $user->id,
            'task_category_id' => $request->task_category,
            'title' => $request->title,
            'description' => $request->description,
            'start_at' => $startAt,
            'end_at' => $endAt,
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
        $defaultUrl = route('tasks.index');
        $backUrl = $request->query('back_url', $defaultUrl);

        // 外部URLブロック
        if(!Str::startsWith($backUrl, config('app.url'))) {
            $backUrl = $defaultUrl;
        }

        return $backUrl;
    }
    

    // ----- update - destroy - complete ------------------------------------------------------------------------------------------------
    // ---- 外部URLならデフォルトに置き換えて、安全なback_urlを返す関数(query)
    public static function getSafeBackUrlFromInput($request) {
        $defaultUrl = route('tasks.index');
        $backUrl = $request->input('back_url', $defaultUrl);

        // 外部URLブロック
        if(!Str::startsWith($backUrl, config('app.url'))) {
            $backUrl = $defaultUrl;
        }

        return $backUrl;
    }



    // ----- update - destroy - complete ------------------------------------------------------------------------------------------------
    // ----- タスク情報取得
    public static function getTask($user, $id) {
        $task = $user->tasks()->findOrFail($id);

        return $task;
    }


    // ----- update ------------------------------------------------------------------------------------------------
    // ----- 保存
    public static function updateTask($task, $user, $request, $startAt, $endAt) {
        $task->update([
            'user_id' => $user->id,
            'task_category_id' => $request->task_category,
            'title' => $request->title,
            'description' => $request->description,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'is_completed' => $task->is_completed,
        ]);
    }
}
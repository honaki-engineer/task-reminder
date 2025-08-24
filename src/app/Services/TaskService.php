<?php 

namespace App\Services;

use App\Models\Task;
use App\Models\TaskCategory;
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
    public static function getSafeBackUrlFromQuery($request): string {
        return self::sanitizeBackUrl($request->query('back_url', ''), route('tasks.index'));
    }


    // ----- update - destroy - complete ------------------------------------------------------------------------------------------------
    // ----- タスク情報取得
    public static function getTask($user, $id) {
        $task = $user->tasks()->findOrFail($id);

        return $task;
    }

    // ---- 外部URLならデフォルトに置き換えて、安全なback_urlを返す関数(input)
    public static function getSafeBackUrlFromInput($request): string {
        return self::sanitizeBackUrl($request->input('back_url', ''), route('tasks.index'));
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


    // ----- getSafeBackUrlFromQuery - getSafeBackUrlFromInput ------------------------------------------------------
    // ----- 画面から「戻り先URL（back_url）」が送られてきたときに「安全なURL」だけを許可する
    private static function sanitizeBackUrl(string $backUrl, string $default): string
    {
        $backUrl = trim($backUrl);

        // ① 空ならデフォルト
        if($backUrl === '') return $default;

        // ② //evil.com みたいに書くと「今の通信方法(http/https)を勝手に引き継いで外部サイトへ飛ばす」技が使える。
        //    → これを防ぐ
        if(str_starts_with($backUrl, '//')) return $default;

        // ③ 「https://◯◯◯ のような絶対URLは全部ブロック」(自分のサイトは除外)
        if(str_contains($backUrl, '://')) {
            $host    = parse_url($backUrl, PHP_URL_HOST);
            $appHost = parse_url(config('app.url'), PHP_URL_HOST);

            if($host !== $appHost) {
                return $default; // 外部 → ブロック
            }

            return $backUrl;     // ★ 同一オリジンの絶対URLはここで確定して返す
        }

        // ④ 相対URLだけ許可。先頭に "/" がなければ付ける
        if(!str_starts_with($backUrl, '/')) $backUrl = '/'.$backUrl;

        // ⑤ 自ドメインの絶対URLに正規化して返す
        return url($backUrl);
    }
}
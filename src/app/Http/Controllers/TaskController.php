<?php

namespace App\Http\Controllers;

use App\Models\TaskCategory;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // ----- ユーザー情報
        $user = TaskService::getUser();
        
        // ----- フォーカスマトリックス情報
        $taskCategories = TaskService::getTaskCategories();

        // ----- タスク情報
        $tasksByCategory = $user->tasks()
            ->orderBy('end_at')
            ->get()
            ->groupBy(fn($t) => (int) $t->task_category_id);

        return view('tasks.index', compact('taskCategories','tasksByCategory'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $taskCategories = TaskCategory::get();

        return view('tasks.create', compact('taskCategories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // ----- ユーザー情報
        $user = TaskService::getUser();
        
        // ----- 時間
        // フォームの date + time を結合
        $startAt = TaskService::combineStartDateTime($request);
        $endAt = TaskService::combineEndDateTime($request);

        // 締切が開始より前ならエラー（after_or_equal相当）
        if($endAt->lt($startAt)) {
            return back()
                ->withErrors(['end_date' => '締切は開始以降にしてください。'])
                ->withInput();
        }

        // ----- 保存
        TaskService::storeTask($user, $request, $startAt, $endAt);

        // ----- リダイレクトの分岐
        if($request->action === 'store_and_create') {
            return to_route('tasks.create')->with('success', 'タスクの登録完了しました。続けて作成可能です。');
        } elseif($request->action === 'store_and_index') {
            return to_route('tasks.index')->with('success', 'タスクの登録完了しました。');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        // ----- ユーザー情報
        $user = TaskService::getUser();
        
        // ----- タスク情報取得withフォーカスマトリックス
        $task = TaskService::findTaskWithTaskCategory($user, $id);

        // ---- 外部URLならデフォルトに置き換えて、安全なback_urlを返す関数(query)
        $backUrl = TaskService::getSafeBackUrlFromQuery($request);

        return view('tasks.show', compact('task', 'backUrl'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        // ----- ユーザー情報
        $user = TaskService::getUser();

        // ----- タスク情報取得withフォーカスマトリックス
        $task = TaskService::findTaskWithTaskCategory($user, $id);

        // ----- フォーカスマトリックス情報取得
        $taskCategories = TaskService::getTaskCategories();
        
        // ---- 外部URLならデフォルトに置き換えて、安全なback_urlを返す関数(query)
        $backUrl = TaskService::getSafeBackUrlFromQuery($request);
        
        // ----- showへ戻る専用URL(一覧URLを持ち回り)
        $showUrl = route('tasks.show', ['task' => $task->id, 'back_url' => $backUrl]);

        return view('tasks.edit', compact('task', 'taskCategories', 'backUrl', 'showUrl'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // ----- ユーザー情報
        $user = TaskService::getUser();

        // ----- 時間
        // フォームの date + time を結合
        $startAt = TaskService::combineStartDateTime($request);
        $endAt = TaskService::combineEndDateTime($request);
        
        // 締切が開始より前ならエラー（after_or_equal相当）
        if($endAt->lt($startAt)) {
            return back()
            ->withErrors(['end_date' => '締切は開始以降にしてください。'])
            ->withInput();
        }
        
        // ----- タスク情報取得
        $task = TaskService::getTask($user, $id);
        
        // ----- 保存
        TaskService::updateTask($task, $user, $request, $startAt, $endAt);

        // ----- 外部URLならデフォルトに置き換えて、安全なback_urlを返す関数(input)
        $backUrl = TaskService::getSafeBackUrlFromInput($request);

        // ----- リダイレクト
        return to_route('tasks.show', ['task' => $task->id, 'back_url' => $backUrl])->with('success', 'タスクの更新完了しました。');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // ----- ユーザー情報
        $user = TaskService::getUser();
        
        // ----- タスク情報取得
        $task = TaskService::getTask($user, $id);

        // ----- 削除
        $task->delete();

        // ----- 外部URLならデフォルトに置き換えて、安全なback_urlを返す関数(input)
        $backUrl = TaskService::getSafeBackUrlFromInput($request);

        return redirect($backUrl)->with('success', 'タスクの削除完了しました。');
    }

    // onedayページへ遷移
    public function oneDay()
    {
        // ----- ユーザー情報
        $user = TaskService::getUser();

        // ----- フォーカスマトリックス情報
        $taskCategories = TaskCategory::orderBy('id')->get();

        // ----- タスク情報
        $tasksByCategory = $user->tasks()
            ->whereDate('start_at', '<=', Carbon::today()) // 開始日が今日以前
            ->orderBy('end_at')
            ->get()
            ->groupBy(fn($t) => (int) $t->task_category_id);

        //  ----本日の年月日を取得
        Carbon::setLocale('ja'); // 日本語ロケール
        $now = Carbon::now()->translatedFormat('Y年n月j日(D)');

        return view('tasks.one_day', compact('taskCategories','tasksByCategory', 'now'));
    }

    // 完了処理
    public function complete(Request $request, $id)
    {
        // ----- ユーザー情報
        $user = TaskService::getUser();

        // ----- タスク情報取得
        $task = TaskService::getTask($user, $id);

        // ----- 完了処理
        $task->update([
            'is_completed' => !$task->is_completed
        ]);

        // ----- 外部URLならデフォルトに置き換えて、安全なback_urlを返す関数(input)
        $backUrl = TaskService::getSafeBackUrlFromInput($request);

        return to_route('tasks.show', ['task' => $task->id, 'back_url' => $backUrl]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

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
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ----- フォーカスマトリックス情報
        $taskCategories = TaskCategory::orderBy('id')->get();

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
        // ----- ユーザー情報取得
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ----- 時間
        // フォームの date + time を結合
        $startAt = Carbon::parse(
            $request->start_date.' '.($request->start_time),
        );

        $endAt = Carbon::parse(
            $request->end_date.' '.($request->end_time),
        );

        // 締切が開始より前ならエラー（after_or_equal相当）
        if($endAt->lt($startAt)) {
            return back()
                ->withErrors(['end_date' => '締切は開始以降にしてください。'])
                ->withInput();
        }

        // ----- 保存
        Task::create([
            'user_id' => $user->id,
            'task_category_id' => $request->task_category,
            'title' => $request->title,
            'description' => $request->description,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'is_completed' => false,
        ]);

        // ----- リダイレクトの分岐
        if($request->action === 'store_and_create') {
            return redirect()->route('tasks.create')->with('success', 'タスクの登録完了しました。続けて作成可能です。');
        } elseif($request->action === 'store_and_index') {
            return redirect()->route('tasks.index')->with('success', 'タスクの登録完了しました。');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // ----- ユーザー情報取得
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ----- タスク情報取得
        $task = $user->tasks()
            ->with('taskCategory')  
            ->findOrFail($id);

        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // ----- ユーザー情報取得
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ----- タスク情報取得
        $task = $user->tasks()
            ->findOrFail($id);

        // ----- 削除
        $task->delete();

        return view('tasks.index');
    }

    // onedayページへ遷移
    public function oneDay()
    {
        return view('tasks.one_day');
    }

    // 完了処理
    public function complete($id)
    {
        // ----- ユーザー情報取得
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ----- タスク情報取得
        $task = $user->tasks()
            ->with('taskCategory')  
            ->findOrFail($id);

        // ----- 完了処理
        $task->update([
            'is_completed' => !$task->is_completed
        ]);

        return to_route('tasks.show', ['task' => $task->id]);
    }
}

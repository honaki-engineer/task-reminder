<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    // リレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function taskCategory()
    {
        return $this->belongsTo(TaskCategory::class);
    }

    // すべてのカラムを一括代入OK
    protected $fillable = [
        'user_id',
        'task_category_id',
        'title',
        'description',
        'start_at',
        'end_at',
        'is_completed',
    ];

    // キャスト
    protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
    ];
}

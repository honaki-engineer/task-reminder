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
        'is_completed' => 'boolean',
    ];

    // 検索
    public function scopeSearch($query, $search)
    {
        if($search !== null){
            $normalizedSearch = mb_convert_kana($search, 's'); // 全角スペースを半角
            $keywords = preg_split('/[\s]+/', $normalizedSearch); //空白で区切る

            foreach($keywords as $value){
                $query->where('title', 'like', '%' .$value. '%')
                  ->orWhere('description', 'like', '%' . $value . '%'); 
            }
        }

        return $query;
    }
}

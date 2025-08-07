<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlackNotification extends Model
{
    use HasFactory;

    // リレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // すべてのカラムを一括代入OK
    protected $fillable = [
        'user_id',
        'slack_user_id',
        'slack_team_id',
        'bot_access_token',
        'is_enabled',
        'last_sent_at',
    ];
}

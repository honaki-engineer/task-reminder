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
    ];

    // Slack連携の確認
    public function isLinked()
    {
        return $this->slack_user_id && $this->slack_team_id && $this->bot_access_token;
    }
}

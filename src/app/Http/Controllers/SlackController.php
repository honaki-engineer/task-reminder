<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TaskService;

class SlackController extends Controller
{
    public function index()
    {
        // ----- ユーザー情報
        $user = TaskService::getUser();

        return view('slacks.index');
    }
}

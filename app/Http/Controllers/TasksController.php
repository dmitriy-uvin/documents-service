<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TasksController extends Controller
{
    public function index()
    {
        return view('tasks');
    }

    public function getAllTasks()
    {
        $tasks = Task::orderBy('created_at', 'desc')
            ->get()
            ->all();

        return response()->json($tasks);
    }
}

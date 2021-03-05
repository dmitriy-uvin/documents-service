<?php

namespace App\Http\Controllers;

use App\Actions\Task\GetTasksCollectionAction;

class TasksController extends Controller
{
    private GetTasksCollectionAction $getTasksCollectionAction;

    public function __construct(GetTasksCollectionAction $getTasksCollectionAction)
    {
        $this->getTasksCollectionAction = $getTasksCollectionAction;
    }

    public function index()
    {
        return view('tasks');
    }

    public function getAllTasks()
    {
        $tasks = $this->getTasksCollectionAction->execute()->getTasks();

        return response()->json($tasks);
    }
}

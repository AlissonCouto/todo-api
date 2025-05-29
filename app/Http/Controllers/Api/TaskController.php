<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\TaskService;
use App\Http\Requests\StoreTaskRequest;

use App\Traits\HandlesApiException;

class TaskController extends Controller
{

    use HandlesApiException;

    protected $service;

    public function __construct(TaskService $taskService)
    {
        $this->service = $taskService;
    }

    public function index(Request $request)
    {
        try {
            $tasks = $this->service->index($request->user());
            return response()->json(['ok' => true, 'data' => $tasks], 200);
        } catch (\Throwable $e) {
            return $this->handleException($e, 'TaskController@index');
        }
    }


    public function store(StoreTaskRequest $request)
    {
        try {
            $task = $this->service->store($request->user(), $request->all());
            return response()->json(['ok' => true, 'data' => $task], 201);
        } catch (\Throwable $e) {
            return $this->handleException($e, 'TaskController@store');
        }
    }

}

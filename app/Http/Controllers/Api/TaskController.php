<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\TaskService;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;

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

    public function filterByStatus(Request $request, $status)
    {
        try {
            $tasks = $this->service->filterByStatus($request->user(), $status);
            return response()->json(['ok' => true, 'data' => $tasks], 200);
        } catch (\Throwable $e) {
            return $this->handleException($e, 'TaskController@filterByStatus');
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

    public function updateStatus(UpdateTaskStatusRequest $request, $id)
    {
        try {
            $task = $this->service->updateStatus($request->user(), $id, $request->status);
            if (!$task) {
                return response()->json(['ok' => false, 'message' => 'Tarefa não encontrada'], 404);
            }

            return response()->json(['data' => $task], 200);
        } catch (\Throwable $e) {
            return $this->handleException($e, 'TaskController@updateStatus');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $deleted = $this->service->delete($request->user(), $id);
            if (!$deleted) {
                return response()->json(['ok' => true, 'message' => 'Tarefa não encontrada'], 404);
            }

            return response()->json(['ok' => true, 'message' => 'Tarefa deletada com sucesso'], 200);
        } catch (\Throwable $e) {
            return $this->handleException($e, 'TaskController@destroy');
        }
    }
}

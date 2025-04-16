<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\TaskResource;
use App\Http\Requests\TaskRequest;
use App\Models\Task;

class TaskController extends Controller
{
    /**
     * Display a listing of the authenticated user's tasks.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $tasks = $request->user()->tasks;
        return TaskResource::collection($tasks);
    }

    /**
     * Display the specified task belonging to the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|\App\Http\Resources\TaskResource
     */
    public function show(Request $request, $id)
    {
        $task = $request->user()->tasks()->find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        return new TaskResource($task);
    }

    /**
     * Store a newly created task for the authenticated user.
     *
     * @param  \App\Http\Requests\TaskRequest  $request
     * @return \App\Http\Resources\TaskResource|\Illuminate\Http\JsonResponse
     */
    public function store(TaskRequest $request)
    {
        $validated = $request->validated();
        $task = $request->user()->tasks()->create($validated);

        return new TaskResource($task);
    }

    /**
     * Update a task for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TaskRequest $request, Task $task)
    {
        // Check that the task belongs to the authenticated user
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validated();
        $task->update($validated);

        return new TaskResource($task);
    }

    /**
     * Delete a task belonging to the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $task = $request->user()->tasks()->find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted']);
    }
}

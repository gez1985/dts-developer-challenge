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
     * @OA\Get(
     *     path="/api/tasks",
     *     summary="Get a list of tasks for the authenticated user",
     *     operationId="getTasks",
     *     tags={"Tasks"},
     *     @OA\Response(
     *         response=200,
     *         description="A list of tasks",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 properties={
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="description", type="string"),
     *                     @OA\Property(property="priority", type="string", enum={"low", "medium", "high"}),
     *                     @OA\Property(property="status", type="string", enum={"pending", "in_progress", "completed"}),
     *                     @OA\Property(property="due_date", type="string", format="date-time"),
     *                     @OA\Property(property="user_id", type="integer"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 }
     *             )
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function index(Request $request)
    {
        $tasks = $request->user()->tasks;
        return TaskResource::collection($tasks);
    }

    /**
     * Display the specified task belonging to the authenticated user.
     *
     * @OA\Get(
     *     path="/api/tasks/{id}",
     *     summary="Get a specific task by ID",
     *     operationId="getTaskById",
     *     tags={"Tasks"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the task",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="The specified task",
     *         @OA\JsonContent(
     *             type="object",
     *             properties={
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="priority", type="string", enum={"low", "medium", "high"}),
     *                 @OA\Property(property="status", type="string", enum={"pending", "in_progress", "completed"}),
     *                 @OA\Property(property="due_date", type="string", format="date-time"),
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found"
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
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
     * @OA\Post(
     *     path="/api/tasks",
     *     summary="Create a new task for the authenticated user",
     *     operationId="createTask",
     *     tags={"Tasks"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Task object to create",
     *         @OA\JsonContent(
     *             type="object",
     *             properties={
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="priority", type="string", enum={"low", "medium", "high"}),
     *                 @OA\Property(property="status", type="string", enum={"pending", "in_progress", "completed"}),
     *                 @OA\Property(property="due_date", type="string", format="date-time")
     *             },
     *             required={"title", "description", "priority", "status", "due_date"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Task created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             properties={
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="priority", type="string", enum={"low", "medium", "high"}),
     *                 @OA\Property(property="status", type="string", enum={"pending", "in_progress", "completed"}),
     *                 @OA\Property(property="due_date", type="string", format="date-time"),
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             }
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
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
     * @OA\Put(
     *     path="/api/tasks/{id}",
     *     summary="Update a specific task for the authenticated user",
     *     operationId="updateTask",
     *     tags={"Tasks"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the task to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated task data",
     *         @OA\JsonContent(
     *             type="object",
     *             properties={
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="priority", type="string", enum={"low", "medium", "high"}),
     *                 @OA\Property(property="status", type="string", enum={"pending", "in_progress", "completed"}),
     *                 @OA\Property(property="due_date", type="string", format="date-time")
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             properties={
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="priority", type="string", enum={"low", "medium", "high"}),
     *                 @OA\Property(property="status", type="string", enum={"pending", "in_progress", "completed"}),
     *                 @OA\Property(property="due_date", type="string", format="date-time"),
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized to update task"
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
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
     * @OA\Delete(
     *     path="/api/tasks/{id}",
     *     summary="Delete a specific task for the authenticated user",
     *     operationId="deleteTask",
     *     tags={"Tasks"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the task to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found"
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
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

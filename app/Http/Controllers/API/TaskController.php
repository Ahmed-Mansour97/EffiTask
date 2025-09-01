<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignTaskRequest;
use App\Http\Requests\ChangeStatusRequest;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tasks",
     *     summary="Get a list of tasks with optional filters.",
     *     tags={"Tasks"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter tasks by status",
     *         required=false,
     *         @OA\Schema(type="string", example="pending")
     *     ),
     *     @OA\Parameter(
     *         name="assigned_to",
     *         in="query",
     *         description="Filter tasks by user ID assigned",
     *         required=false,
     *         @OA\Schema(type="integer", example=3)
     *     ),
     *     @OA\Parameter(
     *         name="from",
     *         in="query",
     *         description="Filter tasks with due_date starting from this date",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2025-09-01")
     *     ),
     *     @OA\Parameter(
     *         name="to",
     *         in="query",
     *         description="Filter tasks with due_date up to this date",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2025-09-30")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of tasks per page (pagination limit)",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of tasks.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Tasks Retrieved Successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/TaskResource")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $limit = request()->query('limit', 10);

        $query = Task::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->has('from') && $request->has('to')) {
            $query->whereBetween('due_date', [$request->from, $request->to]);
        }

        $tasks = $query->paginate($limit);

        return $this->success(TaskResource::collection($tasks), 'Tasks Retrieved Successfully');
    }

     /**
     * @OA\Post(
     *     path="/api/tasks",
     *     summary="Create a new task",
     *     tags={"Tasks"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CreateTaskRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Task created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/TaskResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
    public function create(CreateTaskRequest $request)
    {
        $request->user()->cannot('create', Task::class) && throw new AuthorizationException('This action is unauthorized.');

        $data = $request->validated();

        $task = Task::create($data);

        if (isset($data['dependency_ids'])) {
            $task->dependencies()->attach($data['dependency_ids']);
        }

        return $this->success(new TaskResource($task), 'Task Created Successfuly' , Response::HTTP_CREATED);
    }


    /**
     * @OA\Get(
     *     path="/api/tasks/{task}",
     *     summary="Get a single task by ID",
     *     tags={"Tasks"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         required=true,
     *         description="ID of the task to retrieve",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task showed successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Task showed successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/TaskResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found"
     *     )
     * )
     */
    public function show(Request $request, Task $task)
    {
        $request->user()->cannot('show', $task) && throw new AuthorizationException('This action is unauthorized.');

        return $this->success(new TaskResource($task), 'Task Retrieved Successfully');
    }

    /**
     * @OA\Put(
     *     path="/api/tasks/{task}",
     *     summary="Update a task",
     *     tags={"Tasks"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         required=true,
     *         description="ID of the task to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateTaskRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/TaskResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found"
     *     )
     * )
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $request->user()->cannot('update', Task::class) && throw new AuthorizationException('This action is unauthorized.');

        $data = $request->validated();

        $task->update($data);

        if (isset($data['dependency_ids'])) {
            $task->dependencies()->sync($data['dependency_ids']);
        }

        $task->refresh();

        return $this->success(new TaskResource($task), 'Task Updated Successfully');
    }

    /**
     * @OA\Delete(
     *     path="/api/tasks/{task}",
     *     summary="Delete a task",
     *     description="Deletes a task and its associated dependencies if applicable.",
     *     tags={"Tasks"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         required=true,
     *         description="ID of the task to delete",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function delete(Task $task, Request $request)
    {
        $request->user()->cannot('delete', $task) && throw new AuthorizationException('This action is unauthorized.');

        $task->delete();

        return $this->success(null, 'Task Deleted Successfully', Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Put(
     *     path="/api/tasks/{task}/change-status",
     *     operationId="changeStatus",
     *     tags={"Tasks"},
     *     security={{"bearerAuth": {}}},
     *     summary="Change the status of a task",
     *     description="Updates the status of a task to one of the allowed values",
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         required=true,
     *         description="ID of the task to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="The status data",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/ChangeStatusRequest")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Status updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Task status updated successfully"),
     *             @OA\Property(property="data", type="object", description="Updated task data")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid status value",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Invalid status provided")
     *         )
     *     )
     * )
     */
    public function changeStatus(ChangeStatusRequest $request , Task $task)
    {
        $request->user()->cannot('changeStatus', $task) && throw new AuthorizationException('This action is unauthorized.');

        $data = $request->validated();

        if ($data['status'] === 'completed' && $this->taskHasPendingDependencies($task)) {
            return $this->error('Cannot mark task as completed due to pending dependencies', null, Response::HTTP_BAD_REQUEST);
        }

        $task->update($data);

        $task->refresh();

        return $this->success(new TaskResource($task), 'Task Status Updated Successfully');
    }

    /**
     * @OA\Put(
     *     path="/api/tasks/{task}/assign",
     *     summary="Assign a task to a user",
     *     tags={"Tasks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         required=true,
     *         description="The ID of the task to assign",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AssignTaskRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task successfully assigned",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Task assigned successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function assign(AssignTaskRequest $request, Task $task)
    {
        $request->user()->cannot('assign', $task) && throw new AuthorizationException('This action is unauthorized.');
        
        $data = $request->validated();

        $task->update($data);

        $task->refresh();

        return $this->success(new TaskResource($task), 'Task Assigned Successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/tasks/my-tasks",
     *     summary="Retrieve tasks assigned to the authenticated user",
     *     tags={"Tasks"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of tasks assigned to the user",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="My Tasks"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/TaskResource"))
     *         )
     *     )
     * )
     */
    public function myTasks()
    {
        $user = auth()->user();

        $myTasks = $user->tasks;

        return $this->success(TaskResource::collection($myTasks), 'My Tasks');
    }

    protected function taskHasPendingDependencies(Task $task)
    {
        $incompleteDependencies = $task->dependencies()
            ->where('status', '!=', 'completed')
            ->count();

        if ($incompleteDependencies > 0) {
            return true;
        }

        return false;
    }
    
}

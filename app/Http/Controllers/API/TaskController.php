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
    public function index()
    {
        $limit = request()->query('limit', 10);

        $tasks = Task::paginate($limit);

        return $this->success(TaskResource::collection($tasks), 'Tasks Retrieved Successfully');
    }

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


    public function show(Request $request, Task $task)
    {
        $request->user()->cannot('show', $task) && throw new AuthorizationException('This action is unauthorized.');

        return $this->success(new TaskResource($task), 'Task Retrieved Successfully');
    }

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

    public function delete(Task $task, Request $request)
    {
        $request->user()->cannot('delete', $task) && throw new AuthorizationException('This action is unauthorized.');

        $task->delete();

        return $this->success(null, 'Task Deleted Successfully', Response::HTTP_NO_CONTENT);
    }


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

    public function assign(AssignTaskRequest $request, Task $task)
    {
        $request->user()->cannot('assign', $task) && throw new AuthorizationException('This action is unauthorized.');
        
        $data = $request->validated();

        $task->update($data);

        $task->refresh();

        return $this->success(new TaskResource($task), 'Task Assigned Successfully');
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

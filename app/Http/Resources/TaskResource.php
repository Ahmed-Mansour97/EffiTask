<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="TaskResource",
 *     required={"id", "title", "status", "due_date", "assignee_id"},
 *     @OA\Property(property="id", type="integer", description="Task ID", example=1),
 *     @OA\Property(property="title", type="string", description="Task title", example="Finish project"),
 *     @OA\Property(property="description", type="string", description="Task description", example="Complete the task management project"),
 *     @OA\Property(property="status", type="string", description="Task status", enum={"pending", "completed", "canceled"}, example="pending"),
 *     @OA\Property(property="due_date", type="string", format="date", description="Task due date", example="2025-12-31"),
 *     @OA\Property(property="assigned_to", type="integer", description="ID of the user assigned to the task", example=1),
 *     @OA\Property(property="dependency_id", type="integer", description="ID of the dependent task", example=2)
 * )
 */
class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'due_date' => $this->due_date,
            'assigned_to' => new UserResource($this->assignedUser),
            'dependencies' => $this->dependencies->map(function ($dependency) {
                    return [
                        'id' => $dependency->id,
                        'title' => $dependency->title,
                        'status' => $dependency->status,
                        'due_date' => $dependency->due_date,
                    ];
                }), 
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

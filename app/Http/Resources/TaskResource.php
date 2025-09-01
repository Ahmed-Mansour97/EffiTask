<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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

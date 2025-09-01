<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="CreateTaskRequest",
 *     type="object",
 *     title="Create Task Request",
 *     description="Data required to create a new task",
 *     required={"title", "status"},
 *     @OA\Property(property="title", type="string", example="Create API documentation"),
 *     @OA\Property(property="description", type="string", example="Write Swagger docs for task creation"),
 *     @OA\Property(property="due_date", type="string", format="date", example="2025-05-20"),
 *     @OA\Property(property="assigned_to", type="integer", example=1),
 *     @OA\Property(
 *         property="dependency_ids",
 *         type="array",
 *         @OA\Items(type="integer", example=2),
 *         description="Array of task IDs this task depends on"
 *     )
 * )
 */
class CreateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'assigned_to' => 'sometimes|exists:users,id',
            'dependency_ids' => 'nullable|array',
            'dependency_ids.*' => 'numeric|exists:tasks,id',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="UpdateTaskRequest",
 *     type="object",
 *     title="Update Task Request",
 *     description="Data allowed for updating a task",
 *     @OA\Property(property="title", type="string", example="Updated task title"),
 *     @OA\Property(property="description", type="string", example="Updated task description"),
 *     @OA\Property(property="due_date", type="string", format="date", example="2025-06-01"),
 *     @OA\Property(property="assigned_to", type="integer", example=2),
 *     @OA\Property(
 *         property="dependency_ids",
 *         type="array",
 *         @OA\Items(type="integer", example=2),
 *         description="Array of task IDs this task depends on"
 *     )
 *  )
 */
class UpdateTaskRequest extends FormRequest
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
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'sometimes|date',
            'assigned_to' => 'sometimes|exists:users,id',
            'dependency_ids' => 'nullable|array',
            'dependency_ids.*' => 'numeric|exists:tasks,id',
        ];
    }
}

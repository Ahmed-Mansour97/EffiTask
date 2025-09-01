<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="AssignTaskRequest",
 *     title="Assign Task Request",
 *     description="Payload to assign a task to a user",
 *     type="object",
 *     required={"assignee_id"},
 *     @OA\Property(
 *         property="assigned_to",
 *         type="integer",
 *         example=3,
 *         description="The ID of the user to assign the task to"
 *     )
 * )
 */
class AssignTaskRequest extends FormRequest
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
            'assigned_to' => 'required|exists:users,id',
        ];
    }
}

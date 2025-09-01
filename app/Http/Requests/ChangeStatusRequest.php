<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="ChangeStatusRequest",
 *     type="object",
 *     title="Change Status Request",
 *     description="Data required to change the status of an existing task",
 *     required={"status"},
 *     @OA\Property(property="status", type="string", enum={"pending", "completed", "canceled"}, example="completed"),
 * )
 */
class ChangeStatusRequest extends FormRequest
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
            'status' => 'required|in:pending,in_progress,completed',
        ];
    }
}

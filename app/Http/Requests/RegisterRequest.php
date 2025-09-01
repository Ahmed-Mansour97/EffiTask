<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="RegisterUserRequestSchema",
 *     type="object",
 *     @OA\Property(property="email", type="string", example="hello@gmail.com", nullable=true),
 *     @OA\Property(property="password", type="string", example="123456"),
 *     @OA\Property(property="password_confirmation", type="string", example="123456"),
 *     @OA\Property(property="name", type="string", example="test"),
 * )
 */
class RegisterRequest extends FormRequest
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
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|string|email|unique:users',
            'password'              => 'required|string|min:6|confirmed',
        ];
    }
}

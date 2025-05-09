<?php

namespace App\Http\Requests\v1\Log;

use Illuminate\Foundation\Http\FormRequest;

class LogStoreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'action' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}

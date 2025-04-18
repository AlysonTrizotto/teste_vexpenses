<?php

namespace App\Http\Requests;

use App\Models\UserBranch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class BaseFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    /**
     * Removendo campos ou valores nulos
    */
    protected function prepareForValidation()
    {
        $this->merge(
            collect($this->all())
                ->filter(function ($value) {
                    return !is_null($value); 
                })
                ->toArray()
        );
    }

    protected function failedAuthorization()
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'Unauthorized action.',
        ], 403));
    }
}

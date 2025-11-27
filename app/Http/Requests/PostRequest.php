<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ];

        // For PATCH, make fields optional
        if ($this->isMethod('PATCH')) {
            $rules = [
                'title' => 'sometimes|string|max:255',
                'content' => 'sometimes|string',
            ];
        }

        return $rules;
    }
}
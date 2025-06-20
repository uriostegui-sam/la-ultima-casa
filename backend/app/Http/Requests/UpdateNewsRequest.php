<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNewsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title.en' => 'sometimes|string|max:255',
            'title.es' => 'sometimes|string|max:255',
            'content.en' => 'sometimes|string',
            'content.es' => 'sometimes|string',
            'cover_image' => 'nullable|image|max:2048',
            'published' => 'nullable|boolean',
        ];
    }
}

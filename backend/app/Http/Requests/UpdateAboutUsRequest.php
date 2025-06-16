<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAboutUsRequest extends FormRequest
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
            'number' => 'required|string|regex:/^\d[\d\s]*$/|max:16',
            'cover_image' => 'sometimes|image|max:2048',
            'mail' => 'required|email',
            // 'address' => 'required|array',
            'address.text' => 'required|string',
            'address.map' => 'required|string',
            'description.es' => 'required|string',
            'description.en' => 'required|string',
        ];
    }
}

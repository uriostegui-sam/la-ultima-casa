<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSkillRequest extends FormRequest
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
    public function rules()
    {
        return [
            'name.es' => 'required|string|max:255',
            'name.en' => 'nullable|string|max:255',
            'profile_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'published' => 'sometimes|boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.es.required' => 'nameRequired',
            'profile_image.image' => 'profileImageInvalid',
            'profile_image.mimes' => 'profileImageMimes',
            'profile_image.max' => 'profileImageMaxSize',
        ];
    }
}

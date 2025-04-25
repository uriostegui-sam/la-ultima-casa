<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArtworkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isArtist();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'images' => 'sometimes|array|min:1|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'dimensions.width' => 'nullable|numeric',
            'dimensions.height' => 'nullable|numeric',
            'dimensions.depth' => 'nullable|numeric',
            'creation_date' => 'sometimes|string|max:255',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArtworkRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description.en' => 'nullable|string',
            'description.es' => 'required|string',
            'images' => 'required|array|min:1|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'dimensions.width' => 'nullable|numeric',
            'dimensions.height' => 'nullable|numeric',
            'dimensions.depth' => 'nullable|numeric',
            'creation_date' => 'required|string|max:255',
        ];
    }

        public function messages()
    {
        return [
            'title.required' => 'divers.titleRequired',
            'description.es.required' => 'divers.descriptionRequired',
            'images.required' => 'divers.imagesRequired',
            'creation_date.required' => 'divers.creationDateRequired',
        ];
    }
}

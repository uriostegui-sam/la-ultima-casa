<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkshopRequest extends FormRequest
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
            'artist_id' => 'required', 'exists:artists,id',
            'title.en' => 'required|string|max:255',
            'title.es' => 'sometimes|string|max:255',
            'description.en' => 'sometimes|string',
            'description.es' => 'required|string',
            'type' => 'sometimes|string|max:255',
            'start_date' => 'sometimes|string|max:255',
            'end_date' => 'nullable|string|max:255',
            'price' => 'sometimes|string|max:255',
            'max_students' => 'sometimes|string|max:255',
            'cover_image' => 'sometimes|image|max:2048',
            'skills' => 'required|array',
            'skills.*' => 'exists:skills,id',
            'featured_position' => 'nullable|in:1,2',
        ];
    }

    public function messages()
    {
        return [
            'artist_id.required' => 'divers.artist_idRequired',
            'title.es.required' => 'divers.titleRequired',
            'description.es.required' => 'divers.descriptionRequired',
            'skills.required' => 'divers.selectRequired',
        ];
    }
}

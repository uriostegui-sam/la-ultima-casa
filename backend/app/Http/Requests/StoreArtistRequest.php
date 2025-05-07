<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArtistRequest extends FormRequest
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
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'user_id' => 'required|exists:users,id',
            'minibio.en' => 'required|string|min:20|max:160',
            'minibio.es' => 'required|string|min:20|max:160',
            'bio.en' => 'required|string|min:100',
            'bio.es' => 'required|string|min:100',
            'social_links.website' => 'nullable|url',
            'social_links.instagram' => 'nullable|string|max:255',
            'social_links.twitter' => 'nullable|string|max:255',
            'social_links.flickr' => 'nullable|url',
            'skills' => 'sometimes|array',
            'skills.*' => 'exists:skills,id'
        ];
    }
}

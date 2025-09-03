<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAboutUsRequest extends FormRequest
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
            'cover_image' => 'required|image|max:2048',
            'mail' => 'required|email',
            // 'address' => 'required|array',
            'address.text' => 'required|string',
            'address.map' => 'required|string',
            'description.es' => 'required|string',
            'description.en' => 'nullable|string',
            'logo_header' => 'required|image|max:2048',
            'logo_footer' => 'required|image|max:2048',
            'logo_hero' => 'required|image|max:2048',
            'logo_favicon' => 'required|image|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'number.required' => 'divers.numberRequired',
            'cover_image.required' => 'divers.coverImageRequired',
            'mail.required' => 'authentication.emailRequired',
            'address.text.required' => 'divers.addressTextRequired',
            'address.map.required' => 'divers.addressMapRequired',
            'description.es.required' => 'divers.descriptionRequired',
            'logo_header.required' => 'divers.logoRequired',
            'logo_footer.required' => 'divers.logoRequired',
            'logo_hero.required' => 'divers.logoRequired',
            'logo_favicon.required' => 'divers.logoRequired',
        ];
    }
}
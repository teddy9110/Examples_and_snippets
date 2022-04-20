<?php

namespace Rhf\Modules\Competition\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class EntryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if (Str::contains($this->route()->uri, 'edit')) {
            return [
                'description' => 'required|string',
            ];
        }
        return [
            'image' => 'required|mimes:jpeg,jpg,png,gif,bmp,svg,webp|max:4096',
            'description' => 'required|string',
        ];
    }
}

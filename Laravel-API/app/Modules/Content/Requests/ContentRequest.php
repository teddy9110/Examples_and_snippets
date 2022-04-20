<?php

namespace Rhf\Modules\Admin\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest as Request;

class ContentRequest extends Request
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

    // OPTIONAL OVERRIDE
    public function forbiddenResponse()
    {
    }

    /**
     * Get the validation rules that apply to the request.

     * @return array
     */
    public function rules()
    {
        if (isset($this->route()->parameters()['id'])) {
            $id = $this->route()->parameters()['id'];
        } else {
            $id = 0;
        }

        switch ($this->method()) {
            case 'POST':
                $validation = [
//                    'title'         => 'required|max:255|unique:content,title,' . $id,
                    'title'         => ['required', 'max:255', Rule::unique('content')->ignore($id)],
                    'category_id'   => 'required|exists:content_category,id',
                    'facebook_id'   => '',
                    'type'          => 'required|in:Text,Video',
                    'content'       => 'string',
                    'description'   => 'string',
                    'image'         => [
                        'mimes:jpeg,jpg,png,gif,bmp,svg,webp',
                        'dimensions:min_width=100,min_height=100,max_width=1000,max_height=1000',
                    ],
                    'video'         => 'mimes:mp4|mimetypes:video/mp4',
                    'status'        => ''
                ];

                // New content
                if ($id == 0) {
                    // Make the video required on new content if the content is new and type is Video
                    $validation['video'] = 'required_if:type,Video|mimes:mp4|mimetypes:video/mp4';
                }

                return $validation;
            case 'GET':
            case 'DELETE':
            case 'PUT':
            case 'PATCH':
                return [];
            default:
                break;
        }
    }
}

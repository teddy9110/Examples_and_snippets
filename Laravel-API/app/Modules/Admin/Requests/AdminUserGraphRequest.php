<?php

namespace Rhf\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;
use Illuminate\Validation\Rule;
use Rhf\Modules\User\Enums\UserGraph;

class AdminUserGraphRequest extends Request
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

     * @return array
     */
    public function rules()
    {
        return [
            'type' => [
                'required',
                Rule::in(UserGraph::getValues())
            ],
            'from' => 'nullable|date',
            'to' => 'nullable|date'
        ];
    }

    /** @inheritDoc */
    public function all($keys = null)
    {
        $data = parent::all();
        $data['type'] = $this->route('type');

        return $data;
    }
}

<?php

namespace Rhf\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Rhf\Modules\Subscription\Services\DirectDebitApiService;

class AdminCreateDirectDebitSignupRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $signupTypes = [
            DirectDebitApiService::TYPE_NEW_CONTRACT_SIGNUP,
            DirectDebitApiService::TYPE_DEFAULTED_CONTRACT_SIGNUP,
        ];
        return [
            'type' => 'required|string|in:' . implode(',', $signupTypes),
            'email' => 'required_if:type,' . $signupTypes[0] . '|nullable|string|regex:/(.+)@(.+)\.(.+)/i',
            'user_id' => 'required_if:type,' . $signupTypes[1] . '|nullable|numeric|exists:users,id',
            'send' => 'required|boolean',
        ];
    }
}

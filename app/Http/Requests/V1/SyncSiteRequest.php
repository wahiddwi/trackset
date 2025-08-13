<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class SyncSiteRequest extends FormRequest
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
        return [
            '*.si_site' => ['required'],
            '*.si_name' => ['required'],
            '*.si_active' => ['required'],
            '*.si_company' => ['required'],
            '*.si_company_site' => ['required'],
        ];
    }
}

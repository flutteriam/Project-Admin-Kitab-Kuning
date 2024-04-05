<?php

namespace Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BaitCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'post_id' => 'required',
            'bab_id' => 'required',
            'translate_bait' => 'required',
            // 'description' => 'required',
            // 'full_bait' => 'required',
            // 'full_bait_harokat' => 'required'
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}

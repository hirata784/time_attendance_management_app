<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceRequest extends FormRequest
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
            'attendance_time' => ['required','date_format:H:i', 'before:leaving_time'],
            'leaving_time' => ['date_format:H:i'],
            // 'rest_start' => ['date_format:H:i'],
            // 'rest_finish' => ['date_format:H:i'],
            'remarks' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'attendance_time.required' => '出勤時間を記入してください',
            'attendance_time.date_format' => 'H:iの形式で記入してください',
            'attendance_time.before' => '出勤時間もしくは退勤時間が不適切な値です',
            'leaving_time.date_format' => 'H:iの形式で記入してください',
            // 'rest_start.date_format' => 'H:iの形式で記入してください',
            // 'rest_finish.date_format' => 'H:iの形式で記入してください',
            'remarks.required' => '備考を記入してください',
        ];
    }
}

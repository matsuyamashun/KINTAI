<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DetailAttendanceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'clock_in'  => ['required', 'date_format:H:i'],
            'clock_out' => ['nullable', 'date_format:H:i'],

            // 休憩欄（複数対応）
            'breaks'                  => ['array'],
            'breaks.*.start_time'     => ['nullable', 'date_format:H:i'],
            'breaks.*.end_time'       => ['nullable', 'date_format:H:i'],

            'note' => ['required', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $clockIn  = $this->clock_in;
            $clockOut = $this->clock_out;

            if ($clockIn && $clockOut) {
                if (strtotime($clockIn) >= strtotime($clockOut)) {
                    $validator->errors()->add(
                        'clock_out', '退勤時間が出勤より前です',
                    );
                }
            }

            // 全ての休憩行をチェック
            foreach ($this->breaks ?? [] as $index => $break) {

                $start = $break['start_time'] ?? null;
                $end   = $break['end_time']   ?? null;

                if ($start && $clockIn && strtotime($start) < strtotime($clockIn)) {
                    $validator->errors()->add("breaks.$index.start_time", '休憩開始時間が不適切な値です');
                }

                if ($start && $clockOut && strtotime($start) >= strtotime($clockOut)) {
                    $validator->errors()->add("breaks.$index.start_time", '休憩開始時間が不適切な値です');
                }

                if ($end && $clockOut && strtotime($end) >= strtotime($clockOut)) {
                    $validator->errors()->add("breaks.$index.end_time", '休憩終了時間が不適切な値です');
                }

                if ($start && $end && strtotime($start) >= strtotime($end)) {
                    $validator->errors()->add("breaks.$index.start_time", '休憩開始時間が不適切な値です');
                }
            }
        });
    }

    public function messages()
    {
        return [
            'clock_in.required'    => '出勤時間が不適切な値です',
            'clock_in.date_format' => '出勤時間が不適切な値です',
            
            'clock_out.date_format' => '退勤時間が不適切な値です',

            'breaks.*.start_time.date_format' => '休憩開始時間が不適切な値です',
            'breaks.*.end_time.date_format'   => '休憩終了時間が不適切な値です',

            'note.required' => '備考を記入してください',
            'note.max' => '備考は:max文字以内で記入してください',
        ];
    }
}

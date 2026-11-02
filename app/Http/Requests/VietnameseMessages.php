<?php

namespace App\Http\Requests;

// Thông báo lỗi tiếng Việt dùng chung cho các Form Request.
trait VietnameseMessages
{
    public function messages(): array
    {
        return [
            'required' => ':attribute không được để trống.',
            'email' => ':attribute không đúng định dạng.',
            'min' => ':attribute phải có ít nhất :min ký tự.',
            'max' => ':attribute không được vượt quá :max ký tự.',
            'unique' => ':attribute đã được sử dụng.',
            'confirmed' => 'Xác nhận mật khẩu không khớp.',
            'in' => ':attribute không hợp lệ.',
            'date_format' => ':attribute không đúng định dạng ngày.',
            'string' => ':attribute không hợp lệ.',
        ];
    }
}

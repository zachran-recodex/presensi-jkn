<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user() && auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'employee_id' => 'required|string|max:50|unique:employees',
            'phone' => 'nullable|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'position' => 'required|string|max:100',
            'department' => 'nullable|string|max:100',
            'location_id' => 'required|exists:locations,id',
            'join_date' => 'required|date|before_or_equal:today',
            'work_start_time' => 'required|date_format:H:i',
            'work_end_time' => 'required|date_format:H:i|after:work_start_time',
            'is_flexible_time' => 'boolean',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string|max:1000'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'employee_id.required' => 'ID Karyawan wajib diisi.',
            'employee_id.unique' => 'ID Karyawan sudah digunakan.',
            'phone.regex' => 'Format nomor telepon tidak valid.',
            'position.required' => 'Jabatan wajib diisi.',
            'location_id.required' => 'Lokasi kerja wajib dipilih.',
            'location_id.exists' => 'Lokasi kerja tidak valid.',
            'join_date.required' => 'Tanggal bergabung wajib diisi.',
            'join_date.before_or_equal' => 'Tanggal bergabung tidak boleh melebihi hari ini.',
            'work_start_time.required' => 'Jam mulai kerja wajib diisi.',
            'work_start_time.date_format' => 'Format jam mulai kerja tidak valid (HH:MM).',
            'work_end_time.required' => 'Jam selesai kerja wajib diisi.',
            'work_end_time.date_format' => 'Format jam selesai kerja tidak valid (HH:MM).',
            'work_end_time.after' => 'Jam selesai kerja harus setelah jam mulai kerja.',
            'status.required' => 'Status karyawan wajib dipilih.',
            'status.in' => 'Status karyawan tidak valid.',
            'notes.max' => 'Catatan maksimal 1000 karakter.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'employee_id' => 'ID Karyawan',
            'location_id' => 'Lokasi Kerja',
            'join_date' => 'Tanggal Bergabung',
            'work_start_time' => 'Jam Mulai Kerja',
            'work_end_time' => 'Jam Selesai Kerja',
            'is_flexible_time' => 'Jam Kerja Fleksibel'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean and format phone number
        if ($this->phone) {
            $phone = preg_replace('/[^0-9+]/', '', $this->phone);
            $this->merge(['phone' => $phone]);
        }

        // Set default values
        $this->merge([
            'is_flexible_time' => $this->boolean('is_flexible_time'),
            'status' => $this->status ?? 'active'
        ]);
    }
}

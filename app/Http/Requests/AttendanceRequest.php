<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();

        // User must be authenticated and have employee profile
        if (!$user || !$user->employee) {
            return false;
        }

        // Employee must be active
        return $user->employee->status === 'active' && $user->is_active;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'photo' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'notes' => 'nullable|string|max:500'
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
            'photo.required' => 'Foto selfie wajib diambil untuk presensi.',
            'photo.string' => 'Format foto tidak valid.',
            'latitude.required' => 'Lokasi GPS tidak terdeteksi. Aktifkan GPS dan coba lagi.',
            'latitude.numeric' => 'Koordinat latitude tidak valid.',
            'latitude.between' => 'Koordinat latitude harus antara -90 sampai 90.',
            'longitude.required' => 'Lokasi GPS tidak terdeteksi. Aktifkan GPS dan coba lagi.',
            'longitude.numeric' => 'Koordinat longitude tidak valid.',
            'longitude.between' => 'Koordinat longitude harus antara -180 sampai 180.',
            'notes.max' => 'Catatan maksimal 500 karakter.'
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
            'photo' => 'Foto Selfie',
            'latitude' => 'Latitude GPS',
            'longitude' => 'Longitude GPS',
            'notes' => 'Catatan'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert string coordinates to float
        if ($this->latitude) {
            $this->merge(['latitude' => (float) $this->latitude]);
        }

        if ($this->longitude) {
            $this->merge(['longitude' => (float) $this->longitude]);
        }

        // Clean notes
        if ($this->notes) {
            $this->merge(['notes' => trim($this->notes)]);
        }
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Additional validation for photo format
            if ($this->photo) {
                // Check if photo is base64 encoded
                $base64Pattern = '/^data:image\/(jpeg|jpg|png);base64,/';
                if (!preg_match($base64Pattern, $this->photo)) {
                    $validator->errors()->add('photo', 'Format foto tidak valid. Gunakan JPG atau PNG.');
                }

                // Check base64 string length (rough size check)
                $base64String = preg_replace('/^data:image\/[a-z]+;base64,/', '', $this->photo);
                $size = strlen($base64String) * 0.75; // Approximate file size in bytes

                if ($size > 5 * 1024 * 1024) { // 5MB limit
                    $validator->errors()->add('photo', 'Ukuran foto terlalu besar. Maksimal 5MB.');
                }
            }
        });
    }
}

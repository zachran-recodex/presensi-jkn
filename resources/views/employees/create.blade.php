<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Karyawan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('employees.store') }}" class="space-y-6">
                        @csrf

                        <!-- Session Status -->
                        <x-auth-session-status class="mb-4" :status="session('status')" />

                        <!-- Validation Errors -->
                        @if ($errors->any())
                            <div class="mb-4">
                                <div class="font-medium text-red-600">
                                    {{ __('Whoops! Something went wrong.') }}
                                </div>

                                <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="bg-blue-50 p-4 rounded-md mb-6">
                            <h3 class="text-lg font-medium text-blue-800 mb-2">Informasi Akun</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Username -->
                                <div>
                                    <x-input-label for="username" :value="__('Username')" />
                                    <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required autofocus />
                                </div>

                                <!-- Name -->
                                <div>
                                    <x-input-label for="name" :value="__('Nama Lengkap')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required />
                                </div>

                                <!-- Email -->
                                <div>
                                    <x-input-label for="email" :value="__('Email')" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                                </div>

                                <!-- Password -->
                                <div>
                                    <x-input-label for="password" :value="__('Password')" />
                                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                                </div>
                            </div>
                        </div>

                        <div class="bg-green-50 p-4 rounded-md mb-6">
                            <h3 class="text-lg font-medium text-green-800 mb-2">Informasi Karyawan</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Employee ID -->
                                <div>
                                    <x-input-label for="employee_id" :value="__('ID Karyawan')" />
                                    <x-text-input id="employee_id" class="block mt-1 w-full" type="text" name="employee_id" :value="old('employee_id')" required />
                                </div>

                                <!-- Phone -->
                                <div>
                                    <x-input-label for="phone" :value="__('Nomor Telepon')" />
                                    <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" />
                                </div>

                                <!-- Position -->
                                <div>
                                    <x-input-label for="position" :value="__('Jabatan')" />
                                    <x-text-input id="position" class="block mt-1 w-full" type="text" name="position" :value="old('position')" required />
                                </div>

                                <!-- Department -->
                                <div>
                                    <x-input-label for="department" :value="__('Departemen')" />
                                    <x-text-input id="department" class="block mt-1 w-full" type="text" name="department" :value="old('department')" />
                                </div>

                                <!-- Location -->
                                <div>
                                    <x-input-label for="location_id" :value="__('Lokasi Kantor')" />
                                    <select id="location_id" name="location_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>
                                        <option value="">Pilih Lokasi</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Join Date -->
                                <div>
                                    <x-input-label for="join_date" :value="__('Tanggal Bergabung')" />
                                    <x-text-input id="join_date" class="block mt-1 w-full" type="date" name="join_date" :value="old('join_date')" required />
                                </div>
                            </div>
                        </div>

                        <div class="bg-purple-50 p-4 rounded-md mb-6">
                            <h3 class="text-lg font-medium text-purple-800 mb-2">Jadwal Kerja</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Work Start Time -->
                                <div>
                                    <x-input-label for="work_start_time" :value="__('Jam Masuk')" />
                                    <x-text-input id="work_start_time" class="block mt-1 w-full" type="time" name="work_start_time" :value="old('work_start_time')" required />
                                </div>

                                <!-- Work End Time -->
                                <div>
                                    <x-input-label for="work_end_time" :value="__('Jam Pulang')" />
                                    <x-text-input id="work_end_time" class="block mt-1 w-full" type="time" name="work_end_time" :value="old('work_end_time')" required />
                                </div>

                                <!-- Flexible Time -->
                                <div class="flex items-center mt-6">
                                    <input id="is_flexible_time" type="checkbox" name="is_flexible_time" value="1" {{ old('is_flexible_time') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <label for="is_flexible_time" class="ml-2 text-sm text-gray-600">{{ __('Jam Kerja Fleksibel') }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-md mb-6">
                            <h3 class="text-lg font-medium text-gray-800 mb-2">Status & Catatan</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Status -->
                                <div>
                                    <x-input-label for="status" :value="__('Status')" />
                                    <select id="status" name="status" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                    </select>
                                </div>

                                <!-- Notes -->
                                <div class="md:col-span-2">
                                    <x-input-label for="notes" :value="__('Catatan')" />
                                    <textarea id="notes" name="notes" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end">
                            <a href="{{ route('employees.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mr-3">
                                {{ __('Batal') }}
                            </a>
                            <x-primary-button>
                                {{ __('Simpan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

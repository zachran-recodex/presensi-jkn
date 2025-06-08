<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <!-- Breadcrumb -->
            <div class="mb-6">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('employees.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                                <i class="fas fa-users mr-2"></i>
                                Karyawan
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                                <a href="{{ route('employees.show', $employee) }}" class="text-sm font-medium text-gray-700 hover:text-blue-600">
                                    {{ $employee->user->name }}
                                </a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                                <span class="text-sm font-medium text-gray-500">Edit</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('employees.update', $employee) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Account Information -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Akun</h3>
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                                    <input type="text"
                                           name="username"
                                           id="username"
                                           value="{{ old('username', $employee->user->username) }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('username') border-red-300 @enderror"
                                           required>
                                    @error('username')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                    <input type="text"
                                           name="name"
                                           id="name"
                                           value="{{ old('name', $employee->user->name) }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-300 @enderror"
                                           required>
                                    @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email"
                                           name="email"
                                           id="email"
                                           value="{{ old('email', $employee->user->email) }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-300 @enderror"
                                           required>
                                    @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700">Password Baru (Opsional)</label>
                                    <input type="password"
                                           name="password"
                                           id="password"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-300 @enderror"
                                           placeholder="Kosongkan jika tidak ingin mengubah password">
                                    @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="sm:col-span-1">
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                                    <input type="password"
                                           name="password_confirmation"
                                           id="password_confirmation"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="Konfirmasi password baru">
                                </div>
                            </div>
                        </div>

                        <!-- Employee Information -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Karyawan</h3>
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <label for="employee_id" class="block text-sm font-medium text-gray-700">ID Karyawan</label>
                                    <input type="text"
                                           name="employee_id"
                                           id="employee_id"
                                           value="{{ old('employee_id', $employee->employee_id) }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('employee_id') border-red-300 @enderror"
                                           required>
                                    @error('employee_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                                    <input type="text"
                                           name="phone"
                                           id="phone"
                                           value="{{ old('phone', $employee->phone) }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-300 @enderror">
                                    @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="position" class="block text-sm font-medium text-gray-700">Jabatan</label>
                                    <input type="text"
                                           name="position"
                                           id="position"
                                           value="{{ old('position', $employee->position) }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('position') border-red-300 @enderror"
                                           required>
                                    @error('position')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="department" class="block text-sm font-medium text-gray-700">Departemen</label>
                                    <input type="text"
                                           name="department"
                                           id="department"
                                           value="{{ old('department', $employee->department) }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('department') border-red-300 @enderror">
                                    @error('department')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="location_id" class="block text-sm font-medium text-gray-700">Lokasi Kerja</label>
                                    <select name="location_id"
                                            id="location_id"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('location_id') border-red-300 @enderror"
                                            required>
                                        <option value="">Pilih Lokasi</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}" {{ old('location_id', $employee->location_id) == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('location_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="join_date" class="block text-sm font-medium text-gray-700">Tanggal Bergabung</label>
                                    <input type="date"
                                           name="join_date"
                                           id="join_date"
                                           value="{{ old('join_date', $employee->join_date->format('Y-m-d')) }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('join_date') border-red-300 @enderror"
                                           required>
                                    @error('join_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Work Schedule -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Jadwal Kerja</h3>
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                                <div>
                                    <label for="work_start_time" class="block text-sm font-medium text-gray-700">Jam Masuk</label>
                                    <input type="time"
                                           name="work_start_time"
                                           id="work_start_time"
                                           value="{{ old('work_start_time', $employee->work_start_time->format('H:i')) }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('work_start_time') border-red-300 @enderror"
                                           required>
                                    @error('work_start_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="work_end_time" class="block text-sm font-medium text-gray-700">Jam Pulang</label>
                                    <input type="time"
                                           name="work_end_time"
                                           id="work_end_time"
                                           value="{{ old('work_end_time', $employee->work_end_time->format('H:i')) }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('work_end_time') border-red-300 @enderror"
                                           required>
                                    @error('work_end_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex items-center">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox"
                                               name="is_flexible_time"
                                               id="is_flexible_time"
                                               value="1"
                                               {{ old('is_flexible_time', $employee->is_flexible_time) ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="is_flexible_time" class="font-medium text-gray-700">Jam Kerja Fleksibel</label>
                                        <p class="text-gray-500">Karyawan dapat bekerja dengan jam fleksibel</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status and Notes -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Status dan Catatan</h3>
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                    <select name="status"
                                            id="status"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-300 @enderror"
                                            required>
                                        <option value="active" {{ old('status', $employee->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                                        <option value="inactive" {{ old('status', $employee->status) === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                        <option value="terminated" {{ old('status', $employee->status) === 'terminated' ? 'selected' : '' }}>Terminated</option>
                                    </select>
                                    @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="notes" class="block text-sm font-medium text-gray-700">Catatan</label>
                                    <textarea name="notes"
                                              id="notes"
                                              rows="3"
                                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-300 @enderror"
                                              placeholder="Catatan tambahan tentang karyawan...">{{ old('notes', $employee->notes) }}</textarea>
                                    @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3 pt-6">
                            <a href="{{ route('employees.show', $employee) }}"
                               class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Batal
                            </a>
                            <button type="submit"
                                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-save mr-2"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

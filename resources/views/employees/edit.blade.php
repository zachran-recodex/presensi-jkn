<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-user-edit mr-2 text-green-600"></i>
                    Edit Karyawan - {{ $employee->user->name }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Perbarui informasi karyawan</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('employees.show', $employee) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-eye mr-2"></i>
                    Lihat Detail
                </a>
                <a href="{{ route('employees.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form method="POST"
              action="{{ route('employees.update', $employee) }}"
              x-data="employeeEditForm()"
              @submit="showLoading()">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Personal Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-user mr-2 text-blue-600"></i>
                            Informasi Personal
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Data pribadi karyawan</p>
                    </div>

                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Full Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="name"
                                       name="name"
                                       value="{{ old('name', $employee->user->name) }}"
                                       required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                       placeholder="Masukkan nama lengkap">
                                @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email"
                                       id="email"
                                       name="email"
                                       value="{{ old('email', $employee->user->email) }}"
                                       required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                                       placeholder="email@jakakuasanusantara.web.id">
                                @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Employee ID -->
                            <div>
                                <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    ID Karyawan <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="employee_id"
                                       name="employee_id"
                                       value="{{ old('employee_id', $employee->employee_id) }}"
                                       required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('employee_id') border-red-500 @enderror"
                                       placeholder="EMP001, NIK123, dll">
                                @error('employee_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">ID unik untuk karyawan</p>
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nomor Telepon
                                </label>
                                <input type="tel"
                                       id="phone"
                                       name="phone"
                                       value="{{ old('phone', $employee->phone) }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                                       placeholder="081234567890">
                                @error('phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Password Section -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-yellow-800 mb-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                Update Password (Opsional)
                            </h4>
                            <p class="text-sm text-yellow-700 mb-4">
                                Biarkan kosong jika tidak ingin mengubah password
                            </p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                        Password Baru
                                    </label>
                                    <div class="relative">
                                        <input :type="showPassword ? 'text' : 'password'"
                                               id="password"
                                               name="password"
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror"
                                               placeholder="Minimal 8 karakter">
                                        <button type="button"
                                                @click="showPassword = !showPassword"
                                                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700">
                                            <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                        Konfirmasi Password
                                    </label>
                                    <input :type="showPassword ? 'text' : 'password'"
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="Ulangi password baru">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Job Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-briefcase mr-2 text-green-600"></i>
                            Informasi Pekerjaan
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Jabatan dan departemen karyawan</p>
                    </div>

                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Position -->
                            <div>
                                <label for="position" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jabatan <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="position"
                                       name="position"
                                       value="{{ old('position', $employee->position) }}"
                                       required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('position') border-red-500 @enderror"
                                       placeholder="Software Developer, Marketing Manager, dll">
                                @error('position')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Department -->
                            <div>
                                <label for="department" class="block text-sm font-medium text-gray-700 mb-2">
                                    Departemen
                                </label>
                                <input type="text"
                                       id="department"
                                       name="department"
                                       value="{{ old('department', $employee->department) }}"
                                       list="department-suggestions"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('department') border-red-500 @enderror"
                                       placeholder="IT, Marketing, Finance, HR, dll">

                                <datalist id="department-suggestions">
                                    <option value="IT">
                                    <option value="Marketing">
                                    <option value="Finance">
                                    <option value="Human Resources">
                                    <option value="Sales">
                                    <option value="Operations">
                                    <option value="Customer Service">
                                </datalist>

                                @error('department')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Join Date -->
                            <div>
                                <label for="join_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Bergabung <span class="text-red-500">*</span>
                                </label>
                                <input type="date"
                                       id="join_date"
                                       name="join_date"
                                       value="{{ old('join_date', $employee->join_date->format('Y-m-d')) }}"
                                       required
                                       max="{{ date('Y-m-d') }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('join_date') border-red-500 @enderror">
                                @error('join_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Location -->
                            <div>
                                <label for="location_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Lokasi Kerja <span class="text-red-500">*</span>
                                </label>
                                <select id="location_id"
                                        name="location_id"
                                        required
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('location_id') border-red-500 @enderror">
                                    <option value="">Pilih Lokasi Kerja</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}"
                                            {{ old('location_id', $employee->location_id) == $location->id ? 'selected' : '' }}>
                                            {{ $location->name }} - {{ $location->address }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('location_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Work Schedule -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-clock mr-2 text-purple-600"></i>
                            Jadwal Kerja
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Atur jam kerja dan kebijakan presensi</p>
                    </div>

                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Work Start Time -->
                            <div>
                                <label for="work_start_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jam Masuk <span class="text-red-500">*</span>
                                </label>
                                <input type="time"
                                       id="work_start_time"
                                       name="work_start_time"
                                       value="{{ old('work_start_time', $employee->work_start_time->format('H:i')) }}"
                                       required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('work_start_time') border-red-500 @enderror">
                                @error('work_start_time')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Work End Time -->
                            <div>
                                <label for="work_end_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jam Pulang <span class="text-red-500">*</span>
                                </label>
                                <input type="time"
                                       id="work_end_time"
                                       name="work_end_time"
                                       value="{{ old('work_end_time', $employee->work_end_time->format('H:i')) }}"
                                       required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('work_end_time') border-red-500 @enderror">
                                @error('work_end_time')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Flexible Time -->
                        <div class="flex items-start space-x-3">
                            <input type="checkbox"
                                   id="is_flexible_time"
                                   name="is_flexible_time"
                                   value="1"
                                   {{ old('is_flexible_time', $employee->is_flexible_time) ? 'checked' : '' }}
                                   class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <div class="flex-1">
                                <label for="is_flexible_time" class="block text-sm font-medium text-gray-700">
                                    Jam Kerja Fleksibel
                                </label>
                                <p class="text-sm text-gray-600 mt-1">
                                    Jika diaktifkan, karyawan tidak akan dianggap terlambat jika datang setelah jam masuk.
                                    Cocok untuk posisi yang membutuhkan fleksibilitas waktu.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status & Notes -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-cog mr-2 text-gray-600"></i>
                            Status & Catatan
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Pengaturan status dan informasi tambahan</p>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status Karyawan <span class="text-red-500">*</span>
                            </label>
                            <select id="status"
                                    name="status"
                                    required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror">
                                <option value="active" {{ old('status', $employee->status) === 'active' ? 'selected' : '' }}>
                                    Aktif
                                </option>
                                <option value="inactive" {{ old('status', $employee->status) === 'inactive' ? 'selected' : '' }}>
                                    Tidak Aktif
                                </option>
                                <option value="terminated" {{ old('status', $employee->status) === 'terminated' ? 'selected' : '' }}>
                                    Terminated
                                </option>
                            </select>
                            @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">
                                Karyawan dengan status "Tidak Aktif" atau "Terminated" tidak dapat melakukan presensi.
                            </p>
                        </div>

                        <!-- Face Enrollment Status -->
                        @if($employee->user->hasFaceEnrolled())
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-medium text-blue-900">
                                            <i class="fas fa-face-smile mr-2"></i>
                                            Face Recognition Status
                                        </h4>
                                        <p class="text-sm text-blue-800 mt-1">
                                            Wajah karyawan sudah terdaftar dalam sistem.
                                        </p>
                                    </div>
                                    <a href="{{ route('face-enrollment.show', $employee) }}"
                                       class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                        <i class="fas fa-redo mr-2"></i>
                                        Re-enroll
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-medium text-red-900">
                                            <i class="fas fa-face-frown mr-2"></i>
                                            Face Recognition Status
                                        </h4>
                                        <p class="text-sm text-red-800 mt-1">
                                            Wajah karyawan belum terdaftar. Lakukan enrollment setelah menyimpan data.
                                        </p>
                                    </div>
                                    <a href="{{ route('face-enrollment.show', $employee) }}"
                                       class="inline-flex items-center px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm">
                                        <i class="fas fa-camera mr-2"></i>
                                        Enroll Now
                                    </a>
                                </div>
                            </div>
                        @endif

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Catatan
                            </label>
                            <textarea id="notes"
                                      name="notes"
                                      rows="4"
                                      maxlength="1000"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('notes') border-red-500 @enderror"
                                      placeholder="Catatan tambahan tentang karyawan...">{{ old('notes', $employee->notes) }}</textarea>
                            @error('notes')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">
                                Maksimal 1000 karakter. Catatan ini hanya dapat dilihat oleh admin.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-info-circle mr-1"></i>
                        Pastikan semua data sudah benar sebelum menyimpan perubahan.
                    </div>

                    <div class="flex items-center space-x-4">
                        <a href="{{ route('employees.show', $employee) }}"
                           class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-times mr-2"></i>
                            Batal
                        </a>

                        <button type="submit"
                                class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            function employeeEditForm() {
                return {
                    showPassword: false,

                    init() {
                        // Validate password match
                        const passwordInput = document.getElementById('password');
                        const confirmInput = document.getElementById('password_confirmation');

                        confirmInput.addEventListener('input', function() {
                            if (passwordInput.value && passwordInput.value !== this.value) {
                                this.setCustomValidity('Password tidak cocok');
                            } else {
                                this.setCustomValidity('');
                            }
                        });

                        // Validate work time
                        const startTimeInput = document.getElementById('work_start_time');
                        const endTimeInput = document.getElementById('work_end_time');

                        function validateWorkTime() {
                            if (startTimeInput.value && endTimeInput.value) {
                                if (startTimeInput.value >= endTimeInput.value) {
                                    endTimeInput.setCustomValidity('Jam pulang harus setelah jam masuk');
                                } else {
                                    endTimeInput.setCustomValidity('');
                                }
                            }
                        }

                        startTimeInput.addEventListener('change', validateWorkTime);
                        endTimeInput.addEventListener('change', validateWorkTime);
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>

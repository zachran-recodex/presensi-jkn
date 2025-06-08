<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Header Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Daftar Karyawan</h3>
                            <p class="text-sm text-gray-600 mt-1">Total: {{ $employees->total() }} karyawan</p>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('employees.create') }}"
                               class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center space-x-2">
                                <i class="fas fa-plus"></i>
                                <span>Tambah Karyawan</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employee Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white">
                    @if($employees->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'employee_id', 'direction' => $sortField === 'employee_id' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}"
                                           class="flex items-center space-x-1 hover:text-gray-700">
                                            <span>ID Karyawan</span>
                                            @if($sortField === 'employee_id')
                                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                            @else
                                                <i class="fas fa-sort text-gray-400"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'user.name', 'direction' => $sortField === 'user.name' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}"
                                           class="flex items-center space-x-1 hover:text-gray-700">
                                            <span>Nama</span>
                                            @if($sortField === 'user.name')
                                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                            @else
                                                <i class="fas fa-sort text-gray-400"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'position', 'direction' => $sortField === 'position' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}"
                                           class="flex items-center space-x-1 hover:text-gray-700">
                                            <span>Jabatan</span>
                                            @if($sortField === 'position')
                                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                            @else
                                                <i class="fas fa-sort text-gray-400"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'department', 'direction' => $sortField === 'department' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}"
                                           class="flex items-center space-x-1 hover:text-gray-700">
                                            <span>Departemen</span>
                                            @if($sortField === 'department')
                                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                            @else
                                                <i class="fas fa-sort text-gray-400"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'location.name', 'direction' => $sortField === 'location.name' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}"
                                           class="flex items-center space-x-1 hover:text-gray-700">
                                            <span>Lokasi</span>
                                            @if($sortField === 'location.name')
                                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                            @else
                                                <i class="fas fa-sort text-gray-400"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'direction' => $sortField === 'status' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}"
                                           class="flex items-center space-x-1 hover:text-gray-700">
                                            <span>Status</span>
                                            @if($sortField === 'status')
                                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                            @else
                                                <i class="fas fa-sort text-gray-400"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'join_date', 'direction' => $sortField === 'join_date' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}"
                                           class="flex items-center space-x-1 hover:text-gray-700">
                                            <span>Tanggal Bergabung</span>
                                            @if($sortField === 'join_date')
                                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                            @else
                                                <i class="fas fa-sort text-gray-400"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($employees as $employee)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $employee->employee_id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                            <span class="text-sm font-medium text-gray-700">
                                                                {{ strtoupper(substr($employee->user->name, 0, 2)) }}
                                                            </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $employee->user->name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $employee->user->email }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $employee->position }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $employee->department ?: '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $employee->location->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($employee->status === 'active')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i class="fas fa-check-circle mr-1"></i>
                                                        Aktif
                                                    </span>
                                            @elseif($employee->status === 'inactive')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        <i class="fas fa-pause-circle mr-1"></i>
                                                        Tidak Aktif
                                                    </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <i class="fas fa-times-circle mr-1"></i>
                                                        Terminated
                                                    </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $employee->join_date->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <a href="{{ route('employees.show', $employee) }}"
                                               class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-eye mr-1"></i>Detail
                                            </a>
                                            <a href="{{ route('employees.edit', $employee) }}"
                                               class="text-indigo-600 hover:text-indigo-900">
                                                <i class="fas fa-edit mr-1"></i>Edit
                                            </a>
                                            <form action="{{ route('employees.toggle-status', $employee) }}"
                                                  method="POST"
                                                  class="inline"
                                                  onsubmit="return confirm('Yakin ingin mengubah status karyawan ini?')">
                                                @csrf
                                                <button type="submit"
                                                        class="text-{{ $employee->status === 'active' ? 'yellow' : 'green' }}-600 hover:text-{{ $employee->status === 'active' ? 'yellow' : 'green' }}-900">
                                                    <i class="fas fa-{{ $employee->status === 'active' ? 'pause' : 'play' }} mr-1"></i>
                                                    {{ $employee->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                            @if($employee->status !== 'terminated')
                                                <form action="{{ route('employees.destroy', $employee) }}"
                                                      method="POST"
                                                      class="inline"
                                                      onsubmit="return confirm('Yakin ingin menghapus karyawan ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                                        <i class="fas fa-trash mr-1"></i>Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $employees->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-users text-gray-400 text-5xl mb-4"></i>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada karyawan</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Mulai dengan menambahkan karyawan pertama.
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('employees.create') }}"
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    <i class="fas fa-plus mr-2"></i>
                                    Tambah Karyawan
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-layout>
    <x-slot:title>KidWatch | Trash</x-slot>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight">Trash</h1>
            <p class="text-gray-500 mt-1">Manage soft-deleted students</p>
        </div>
        <a href="{{ route('students') }}"
           class="bg-gray-200 text-gray-700 px-6 py-2 rounded-xl font-bold shadow-md hover:bg-gray-300 transition flex items-center">
            <i class="fas fa-arrow-left mr-2 text-sm"></i> Back to Students
        </a>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-[2rem] p-8 shadow-md hover:shadow-lg transition">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-[#f1f5f9]">
                <tr>
                    <th class="text-left text-[#003366] font-bold px-6 py-3 uppercase text-sm tracking-wide">Name of students</th>
                    <th class="text-center text-[#003366] font-bold px-6 py-3 uppercase text-sm tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($students as $student)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-800">
                            {{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }}
                        </td>
                        <td class="px-6 py-4 flex items-center justify-center space-x-3">
                            <!-- Restore -->
                            <form method="POST" action="{{ route('students.restore', $student->id) }}">
                                @csrf
                                <button type="submit"
                                        class="flex items-center space-x-1 px-4 py-1.5 rounded-lg border border-green-400 text-green-500 hover:bg-green-50 transition text-sm font-bold">
                                    <i class="fas fa-undo"></i> <span>Restore</span>
                                </button>
                            </form>

                            <!-- Force Delete -->
                            <form method="POST" action="{{ route('students.forceDelete', $student->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="flex items-center space-x-1 px-4 py-1.5 rounded-lg border border-red-400 text-red-500 hover:bg-red-50 transition text-sm font-bold">
                                    <i class="fas fa-trash"></i> <span>Delete Permanently</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-6 py-20 text-center text-gray-400 italic">
                            No students in trash.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layout>

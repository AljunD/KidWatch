<x-layout>
    <x-slot:title>KidWatch | Trash</x-slot>

    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-4xl font-extrabold text-[#003366] tracking-tight">Trash</h1>
            <p class="text-slate-500 mt-1">Manage soft-deleted students</p>
        </div>
        <a href="{{ route('students') }}"
           class="flex items-center gap-2 bg-white text-[#003366] px-6 py-3 rounded-3xl font-bold border border-slate-200 hover:border-blue-200 transition-all">
            <i class="fas fa-arrow-left"></i> Back to Students
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-8 py-6 text-left text-xs font-black uppercase tracking-widest text-[#003366]">Student Name</th>
                    <th class="px-8 py-6 text-center text-xs font-black uppercase tracking-widest text-[#003366]">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($students as $student)
                    <tr class="hover:bg-blue-50/30 transition-colors">
                        <td class="px-8 py-6 font-semibold text-slate-800">
                            {{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }}
                        </td>
                        <td class="px-8 py-6 text-center">
                            <div class="flex items-center justify-center gap-4">
                                <form method="POST" action="{{ route('students.restore', $student->id) }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-2 px-5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-3xl text-sm font-bold transition-all">
                                        <i class="fas fa-undo"></i> Restore
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('students.forceDelete', $student->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex items-center gap-2 px-5 py-2 bg-red-50 hover:bg-red-100 text-red-700 rounded-3xl text-sm font-bold transition-all">
                                        <i class="fas fa-trash"></i> Delete Permanently
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-8 py-20 text-center text-slate-400 italic">No students in trash.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layout>

<x-layout>
    <x-slot:title>KidWatch | Trash</x-slot>

    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-4xl font-extrabold text-[#003366] tracking-tight">Trash</h1>
            <p class="text-slate-500 mt-1">Manage soft-deleted students and their information</p>
        </div>
        <a href="{{ route('students') }}"
           class="flex items-center gap-2 bg-white text-[#003366] px-6 py-3 rounded-3xl font-bold border border-slate-200 hover:border-blue-200 transition-all">
            <i class="fas fa-arrow-left"></i> Back to Students
        </a>
    </div>

    {{-- Student Trash Table --}}
    <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-widest text-[#003366]">Student Name</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-widest text-[#003366]">Guardian</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-widest text-[#003366]">Contact</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-widest text-[#003366]">Grade Level</th>
                    <th class="px-6 py-4 text-center text-xs font-black uppercase tracking-widest text-[#003366]">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($students as $student)
                    <tr class="hover:bg-blue-50/30 transition-colors">
                        <td class="px-6 py-4 font-semibold text-slate-800">
                            {{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }}
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $student->guardian_name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $student->guardian_contact ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $student->grade_level ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-center">
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
                                    <button type="submit" onclick="return confirm('Delete this student permanently? This action cannot be undone.')"
                                            class="flex items-center gap-2 px-5 py-2 bg-red-50 hover:bg-red-100 text-red-700 rounded-3xl text-sm font-bold transition-all">
                                        <i class="fas fa-trash"></i> Delete Permanently
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    {{-- Progress Records for this student --}}
                    <tr>
                        <td colspan="5" class="bg-slate-50 px-6 py-4">
                            <div class="bg-white border border-slate-200 rounded-xl p-4">
                                <h3 class="text-sm font-black uppercase tracking-widest text-[#003366] mb-3 flex items-center gap-2">
                                    <i class="fas fa-chart-line"></i> Progress Records
                                </h3>
                                <table class="w-full text-sm border border-slate-200 rounded-lg">
                                    <thead class="bg-slate-100">
                                        <tr>
                                            <th class="px-4 py-2 text-left font-bold text-[#003366]">Week</th>
                                            <th class="px-4 py-2 text-left font-bold text-[#003366]">Subject</th>
                                            <th class="px-4 py-2 text-center font-bold text-[#003366]">Rating</th>
                                            <th class="px-4 py-2 text-left font-bold text-[#003366]">Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($student->progressRecords()->withTrashed()->get() as $record)
                                            <tr class="hover:bg-slate-50 transition">
                                                <td class="px-4 py-2">Week {{ $record->week->week_number }}</td>
                                                <td class="px-4 py-2">{{ $record->subject }}</td>
                                                <td class="px-4 py-2 text-center">
                                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase
                                                        @switch($record->rating_level)
                                                            @case(1) bg-red-100 text-red-700 @break
                                                            @case(2) bg-amber-100 text-amber-700 @break
                                                            @case(3) bg-blue-100 text-blue-700 @break
                                                            @case(4) bg-emerald-100 text-emerald-700 @break
                                                            @default bg-gray-100 text-gray-500
                                                        @endswitch">
                                                        {{ \App\Models\ProgressRecord::RATINGS[$record->rating_level] ?? 'Pending' }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-2">{{ $record->remarks ?: 'No remarks' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-4 py-3 text-center text-slate-400 italic">No progress records found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center text-slate-400 italic">No students in trash.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layout>

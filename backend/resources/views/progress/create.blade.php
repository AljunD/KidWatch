<x-layout>
    <x-slot:title>Create Progress Record | KidWatch</x-slot>

    <div class="max-w-3xl mx-auto bg-white rounded-3xl shadow-lg p-10 space-y-8">
        <h1 class="text-3xl font-black text-[#003366] mb-8 border-b pb-4">
            📘 Add Progress for
            <span class="text-emerald-600">
                {{ $student->first_name }} {{ $student->last_name }}
            </span>
        </h1>
                {{-- Back Button --}}
        <div class="mb-6">
            <a href="{{ route('progress') }}"
               class="inline-flex items-center gap-2 bg-gray-200 text-[#003366] px-5 py-2 rounded-lg font-bold hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left"></i> Back to Progress Management
            </a>
        </div>

        <form action="{{ route('progress.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Student (pre-selected, read-only) --}}
            <div class="bg-slate-50 border border-blue-100 rounded-xl p-5">
                <label class="block text-sm font-bold text-[#003366] mb-2">Student</label>
                <input type="hidden" name="student_id" value="{{ $student->id }}">
                <input type="text" value="{{ $student->first_name }} {{ $student->last_name }}"
                       class="border rounded-lg px-4 py-2 w-full bg-gray-100 font-semibold text-[#003366]" readonly>
            </div>

            {{-- Week (pre-selected, read-only) --}}
            <div class="bg-slate-50 border border-blue-100 rounded-xl p-5">
                <label class="block text-sm font-bold text-[#003366] mb-2">Week</label>
                <input type="hidden" name="week_id" value="{{ $week->id }}">
                <input type="text" value="Week {{ $week->week_number }} ({{ $week->start_date }} - {{ $week->end_date }})"
                       class="border rounded-lg px-4 py-2 w-full bg-gray-100 font-semibold text-[#003366]" readonly>
            </div>

            {{-- Subject Selection --}}
            <div class="bg-slate-50 border border-blue-100 rounded-xl p-5">
                <label class="block text-sm font-bold text-[#003366] mb-2">Subject</label>
                <select name="subject" class="border rounded-lg px-4 py-2 w-full focus:ring-2 focus:ring-blue-400" required>
                    <option value="" disabled selected>Select subject</option>
                    @foreach($subjects as $subject)
                        @php
                            $alreadyGraded = $student->progressRecords
                                ->where('week_id', $week->id)
                                ->where('subject', $subject)
                                ->isNotEmpty();
                        @endphp

                        <option value="{{ $subject }}" {{ $alreadyGraded ? 'disabled' : '' }}>
                            {{ $subject }} {{ $alreadyGraded ? '(Already graded)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Rating --}}
            <div class="bg-slate-50 border border-blue-100 rounded-xl p-5">
                <label class="block text-sm font-bold text-[#003366] mb-2">Rating</label>
                <select name="rating_level" class="border rounded-lg px-4 py-2 w-full focus:ring-2 focus:ring-blue-400" required>
                    <option value="" disabled selected>Select rating</option>
                    <option value="0">No Classes</option> {{-- ✅ allow no classes --}}
                    @foreach(\App\Models\ProgressRecord::RATINGS as $level => $label)
                        <option value="{{ $level }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Remarks --}}
            <div class="bg-slate-50 border border-blue-100 rounded-xl p-5">
                <label class="block text-sm font-bold text-[#003366] mb-2">Remarks</label>
                <textarea name="remarks" rows="3"
                          class="border rounded-lg px-4 py-2 w-full focus:ring-2 focus:ring-blue-400"
                          placeholder="Enter remarks for this subject"></textarea>
            </div>

            {{-- Action Buttons --}}
            <div class="flex justify-center items-center mt-8">
                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold shadow hover:bg-blue-700 transition">
                    ✅ Save Progress
                </button>
            </div>
        </form>
    </div>
</x-layout>

<x-layout>
    <x-slot:title>Edit Progress Record | KidWatch</x-slot>

    <div class="max-w-3xl mx-auto bg-white rounded-3xl shadow-lg p-10 space-y-8">
        <h1 class="text-3xl font-black text-[#003366] mb-8 border-b pb-4">
            ✏️ Edit Progress for
            <span class="text-emerald-600">
                {{ $progressRecord->student->first_name }} {{ $progressRecord->student->last_name }}
            </span>
        </h1>

        {{-- Back Button --}}
        <div class="mb-6">
            <a href="{{ route('progress') }}"
               class="inline-flex items-center gap-2 bg-gray-200 text-[#003366] px-5 py-2 rounded-lg font-bold hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left"></i> Back to Progress Management
            </a>
        </div>

        <form action="{{ route('progress.update', $progressRecord->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Subject Dropdown --}}
            <div class="bg-slate-50 border border-blue-100 rounded-xl p-5">
                <label class="block text-sm font-bold text-[#003366] mb-2">Subject</label>
                <select id="subject" name="subject" class="border rounded-lg px-4 py-2 w-full focus:ring-2 focus:ring-blue-400" required>
                    @foreach($subjects as $subject)
                        @php
                            $record = $progressRecord->student->progressRecords
                                ->where('week_id', $progressRecord->week_id)
                                ->where('subject', $subject)
                                ->first();
                        @endphp
                        <option value="{{ $subject }}"
                                data-rating="{{ $record->rating_level ?? '' }}"
                                data-remarks="{{ $record->remarks ?? '' }}"
                                {{ $progressRecord->subject === $subject ? 'selected' : '' }}
                                {{ !$record ? 'disabled' : '' }}>
                            {{ $subject }} {{ !$record ? '(Not graded yet)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Rating Level --}}
            <div class="bg-slate-50 border border-blue-100 rounded-xl p-5">
                <label for="rating_level" class="block text-sm font-bold text-[#003366] mb-2">Rating Level</label>
                <select name="rating_level" id="rating_level" class="border rounded-lg px-4 py-2 w-full focus:ring-2 focus:ring-blue-400" required>
                    <option value="0">No Classes</option>
                    @foreach($ratings as $level => $label)
                        <option value="{{ $level }}">{{ $label }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-slate-500 mt-2">
                    Current rating: <span id="current-rating" class="font-semibold text-blue-700">
                        {{ $ratings[$progressRecord->rating_level] ?? 'No Classes' }}
                    </span>
                </p>
            </div>

            {{-- Remarks --}}
            <div class="bg-slate-50 border border-blue-100 rounded-xl p-5">
                <label for="remarks" class="block text-sm font-bold text-[#003366] mb-2">Remarks</label>
                <textarea name="remarks" id="remarks" rows="3"
                          class="border rounded-lg px-4 py-2 w-full focus:ring-2 focus:ring-blue-400"
                          placeholder="Enter remarks for this subject">{{ $progressRecord->remarks }}</textarea>
                <p class="text-xs text-slate-500 mt-2">
                    Current remarks: <em id="current-remarks" class="text-emerald-700">
                        {{ $progressRecord->remarks ?: 'No remarks yet' }}
                    </em>
                </p>
            </div>

            {{-- Action Buttons --}}
            <div class="flex justify-center items-center mt-8">
                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold shadow hover:bg-blue-700 transition">
                    💾 Save Changes
                </button>
            </div>
        </form>
    </div>
</x-layout>

{{-- Script to update rating and remarks dynamically --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const subjectSelect = document.getElementById('subject');
        const ratingSelect = document.getElementById('rating_level');
        const remarksTextarea = document.getElementById('remarks');
        const currentRatingText = document.getElementById('current-rating');
        const currentRemarksText = document.getElementById('current-remarks');

        subjectSelect.addEventListener('change', function () {
            const selectedOption = subjectSelect.options[subjectSelect.selectedIndex];
            const rating = selectedOption.getAttribute('data-rating');
            const remarks = selectedOption.getAttribute('data-remarks');

            if (rating) {
                ratingSelect.value = rating;
                currentRatingText.textContent = ratingSelect.options[ratingSelect.selectedIndex].text;
            } else {
                ratingSelect.value = '';
                currentRatingText.textContent = 'No Classes';
            }

            remarksTextarea.value = remarks || '';
            currentRemarksText.textContent = remarks || 'No remarks yet';
        });
    });
</script>

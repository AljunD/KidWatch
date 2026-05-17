<x-layout>
    <x-slot:title>Edit Progress Record | KidWatch</x-slot>

    <div class="max-w-3xl mx-auto bg-white rounded-3xl shadow-lg p-10 space-y-8">
        <h1 class="text-3xl font-black text-[#003366] mb-8 border-b pb-4">
            Edit Progress for
            <span class="text-emerald-600">
                {{ $student->first_name }} {{ $student->last_name }}
            </span>
            (Week {{ $week->week_number }})
        </h1>

        <div class="mb-6">
            <a href="{{ route('progress') }}"
               class="inline-flex items-center gap-2 bg-gray-200 text-[#003366] px-5 py-2 rounded-lg font-bold hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left"></i> Back to Progress Management
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-6 px-6 py-4 rounded-xl bg-red-100 text-red-800 font-semibold shadow">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-6 px-6 py-4 rounded-xl bg-green-100 text-green-800 font-semibold shadow">
                {{ session('success') }}
            </div>
        @endif

        <form id="progressForm" action="{{ route('progress.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <input type="hidden" id="record_id" name="record_id" value="">

            {{-- Subject Dropdown --}}
            <div class="bg-slate-50 border border-blue-100 rounded-xl p-5">
                <label class="block text-sm font-bold text-[#003366] mb-2">Subject</label>
                <select id="subject" name="subject" class="border rounded-lg px-4 py-2 w-full focus:ring-2 focus:ring-blue-400" required>
                    <option value="" disabled selected>Select a subject...</option>
                    @foreach($subjects as $subject)
                        @php $record = $records->get($subject); @endphp
                        <option value="{{ $subject }}"
                                data-id="{{ $record->id ?? '' }}"
                                data-rating="{{ $record->rating_level ?? '' }}"
                                data-remarks="{{ $record->remarks ?? '' }}"
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
                    <option value="" disabled selected>Select rating...</option>
                    @foreach($ratings as $level => $label)
                        <option value="{{ $level }}">{{ $label }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-slate-500 mt-2">
                    Current rating: <span id="current-rating" class="font-semibold text-blue-700">—</span>
                </p>
            </div>

            {{-- Remarks --}}
            <div class="bg-slate-50 border border-blue-100 rounded-xl p-5">
                <label for="remarks" class="block text-sm font-bold text-[#003366] mb-2">Remarks</label>
                <textarea name="remarks" id="remarks" rows="3"
                          class="border rounded-lg px-4 py-2 w-full focus:ring-2 focus:ring-blue-400"
                          placeholder="Enter remarks..."></textarea>
                <p class="text-xs text-slate-500 mt-2">
                    Current remarks: <em id="current-remarks" class="text-emerald-700">—</em>
                </p>
            </div>

{{-- Save Button --}}
<div class="flex justify-center items-center mt-8">
    <button type="submit"
            id="saveButton"
            class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold shadow hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
            disabled>
        Save Changes
    </button>
</div>
</form>
</div>
</x-layout>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const subjectSelect = document.getElementById('subject');
        const ratingSelect = document.getElementById('rating_level');
        const remarksTextarea = document.getElementById('remarks');
        const currentRatingText = document.getElementById('current-rating');
        const currentRemarksText = document.getElementById('current-remarks');
        const recordIdInput = document.getElementById('record_id');
        const saveButton = document.getElementById('saveButton');

        // Reset everything to blank initially
        ratingSelect.value = '';
        remarksTextarea.value = '';
        currentRatingText.textContent = '—';
        currentRemarksText.textContent = '—';
        saveButton.disabled = true;

        subjectSelect.addEventListener('change', function () {
            const selectedOption = subjectSelect.options[subjectSelect.selectedIndex];
            const recordId = selectedOption.getAttribute('data-id');
            const rating = selectedOption.getAttribute('data-rating');
            const remarks = selectedOption.getAttribute('data-remarks');

            if (recordId) {
                recordIdInput.value = recordId;
                saveButton.disabled = false; // enable button once subject chosen
            }

            if (rating !== '') {
                ratingSelect.value = rating;
                currentRatingText.textContent = ratingSelect.options[ratingSelect.selectedIndex].text;
            } else {
                ratingSelect.value = '';
                currentRatingText.textContent = '—';
            }

            remarksTextarea.value = remarks || '';
            currentRemarksText.textContent = remarks || '—';
        });
    });
</script>

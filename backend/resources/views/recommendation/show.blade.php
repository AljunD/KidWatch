<x-layout>
    <x-slot:title>Weekly Summary | KidWatch</x-slot>

    <div class="max-w-4xl mx-auto mt-6">
        <!-- Header -->
        <h3 class="text-2xl font-bold text-center text-[#003366] mb-6">
            📖 Weekly Summary for {{ $student->first_name }} {{ $student->last_name }}
        </h3>

        <!-- Weekly Summary Card -->
        <div class="bg-white shadow-lg rounded-xl p-6 mb-6">
            <h4 class="text-lg font-semibold text-[#003366] mb-2">Week {{ $week->week_number }}</h4>

            @if($summary)
                <p class="text-gray-700 leading-relaxed">
                    {{ $summary->summary_text }}
                </p>
            @else
                <p class="text-red-600 font-medium">❌ No summary generated yet.</p>
            @endif
        </div>

        <!-- Recommended Activities -->
        <div class="bg-white shadow-lg rounded-xl p-6">
            <h4 class="text-lg font-semibold text-[#003366] mb-4">🎯 Recommended Activities</h4>

            <div class="space-y-3">
                @if(in_array('English', $weakSubjects))
                    <div class="p-3 bg-yellow-100 border-l-4 border-yellow-500 rounded">
                        📘 <strong>English:</strong> Letter Matching Game
                    </div>
                @endif

                @if(in_array('Filipino', $weakSubjects))
                    <div class="p-3 bg-yellow-100 border-l-4 border-yellow-500 rounded">
                        🇵🇭 <strong>Filipino:</strong> Filipino Storytelling Audio
                    </div>
                @endif

                @if(in_array('Math', $weakSubjects))
                    <div class="p-3 bg-yellow-100 border-l-4 border-yellow-500 rounded">
                        ➗ <strong>Math:</strong> Counting & Pattern Exercises
                    </div>
                @endif

                @if(in_array('Science', $weakSubjects))
                    <div class="p-3 bg-yellow-100 border-l-4 border-yellow-500 rounded">
                        🔬 <strong>Science:</strong> Simple Observation Activities
                    </div>
                @endif

                @if(empty($weakSubjects))
                    <div class="p-3 bg-green-100 border-l-4 border-green-500 rounded">
                        🎉 No intervention needed. Student is performing well!
                    </div>
                @endif
            </div>
        </div>

        <!-- Back Button -->
        <div class="mt-6 text-center">
            <a href="{{ route('recommendation') }}"
               class="px-5 py-2 bg-gray-600 text-white rounded-lg font-semibold hover:bg-gray-700 transition">
                ⬅ Back to Recommendations
            </a>
        </div>
    </div>
</x-layout>

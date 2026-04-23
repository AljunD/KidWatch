<x-layout>
    <x-slot:title>Recommendations | KidWatch</x-slot>

    <div class="max-w-6xl mx-auto mt-6">
        <h2 class="text-2xl font-bold text-center text-[#003366] mb-6">📋 Weekly Recommendations</h2>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($students as $student)
                @foreach($weeks as $week)
                    <div class="bg-white shadow-lg rounded-xl p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-[#003366]">
                                {{ $student->first_name }} {{ $student->last_name }}
                            </h3>
                            <p class="text-sm text-gray-600">
                                Guardian: {{ $student->guardian->first_name }}
                            </p>
                            <span class="inline-block mt-2 px-3 py-1 text-sm font-medium bg-blue-100 text-blue-700 rounded-full">
                                Week {{ $week->week_number }}
                            </span>
                        </div>

                        <div class="mt-4 flex gap-2">
                            <a href="{{ route('recommendation.detail', [$student->id, $week->id]) }}"
                               class="flex-1 px-4 py-2 text-center bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition">
                                View
                            </a>

                            <form action="{{ route('recommendation.generate', [$student->id, $week->id]) }}" method="POST" class="flex-1">
                                @csrf
                                <button class="w-full px-4 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition">
                                    Generate
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
</x-layout>

<x-layout>
    <x-slot:title>KidWatch | Trash Bin</x-slot>

    <div class="max-w-6xl mx-auto mb-6">
        <!-- ✅ Flash Messages -->
        @if(session('success'))
            <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-800 border border-green-300">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-800 border border-red-300">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- LEFT COLUMN: Guardians -->
        <section class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-[#003366] mb-4 flex items-center gap-2">
                <i class="fas fa-user-shield text-blue-600"></i> Trashed Guardians
            </h2>

            @forelse($guardians as $guardian)
                <div class="bg-gray-50 rounded-lg p-4 mb-4 shadow-sm">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-gray-900">
                                {{ $guardian->first_name }} {{ $guardian->last_name }}
                            </p>
                            <p class="text-xs text-gray-500">
                                Trashed at: {{ $guardian->trashed_at->toDateString() }}
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <form action="{{ route('trash.restore',['type'=>'guardian','id'=>$guardian->id]) }}" method="POST">
                                @csrf
                                <button class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 text-xs">
                                    <i class="fas fa-undo"></i> Restore
                                </button>
                            </form>
                            <form action="{{ route('trash.forceDelete',['type'=>'guardian','id'=>$guardian->id]) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-xs">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Linked Students -->
                    <details class="mt-3">
                        <summary class="cursor-pointer text-blue-600 font-medium flex items-center gap-1">
                            <i class="fas fa-users"></i> Linked Students
                        </summary>
                        <ul class="mt-2 space-y-1 text-sm text-gray-700 pl-6">
                            @foreach($guardian->students as $student)
                                <li>
                                    {{ $student->first_name }} {{ $student->last_name }} — {{ ucfirst($student->gender) }},
                                    DOB: {{ $student->date_of_birth->toDateString() }}
                                </li>
                            @endforeach
                        </ul>
                    </details>
                </div>
            @empty
                <p class="text-gray-400 italic">No trashed guardians</p>
            @endforelse
        </section>

        <!-- RIGHT COLUMN: Students -->
        <section class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-[#003366] mb-4 flex items-center gap-2">
                <i class="fas fa-user-graduate text-green-600"></i> Trashed Students
            </h2>

            @forelse($students as $student)
                <div class="bg-gray-50 rounded-lg p-4 mb-4 shadow-sm">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-gray-900">
                                {{ $student->first_name }} {{ $student->last_name }}
                            </p>
                            <p class="text-xs text-gray-500">
                                Trashed at: {{ $student->trashed_at->toDateString() }}
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <form action="{{ route('trash.restore',['type'=>'student','id'=>$student->id]) }}" method="POST">
                                @csrf
                                <button class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 text-xs">
                                    <i class="fas fa-undo"></i> Restore
                                </button>
                            </form>
                            <form action="{{ route('trash.forceDelete',['type'=>'student','id'=>$student->id]) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-xs">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Progress Records -->
                    <details class="mt-3">
                        <summary class="cursor-pointer text-blue-600 font-medium flex items-center gap-1">
                            <i class="fas fa-book"></i> Progress Records
                        </summary>
                        <ul class="mt-2 space-y-1 text-sm text-gray-700 pl-6">
                            @foreach($student->progressRecords as $record)
                                <li>
                                    Week {{ $record->week->week_number }} — {{ $record->subject }}:
                                    {{ $record->rating_level }}
                                </li>
                            @endforeach
                        </ul>
                    </details>
                </div>
            @empty
                <p class="text-gray-400 italic">No trashed students</p>
            @endforelse
        </section>
    </div>
</x-layout>

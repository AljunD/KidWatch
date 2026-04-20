{{-- resources/views/student.blade.php --}}
<x-layout>
    <x-slot:title>KidWatch | Students</x-slot>

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-[#003366] tracking-tight">Student Directory</h1>
            <p class="text-gray-500 text-sm">Monitor and manage student profiles and guardian connections. ({{ $students->count() }} total)</p>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="relative flex-1 md:w-72">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fas fa-search text-gray-400 text-sm"></i>
                </span>
                <input
                    type="text"
                    id="studentSearch"
                    placeholder="Search by student name or ID..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-400 transition-all duration-200 shadow-sm text-sm">
            </div>

            <button onclick="document.getElementById('addStudentModal').classList.remove('hidden')"
                    class="bg-[#007bff] hover:bg-[#0056b3] text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-200 transition-all duration-200 flex items-center whitespace-nowrap active:scale-95">
                <i class="fas fa-plus-circle mr-2"></i> Add New Student
            </button>
        </div>
    </div>

    <div class="bg-[#f1f5f9] rounded-[2.5rem] p-3 md:p-6 shadow-inner border border-white/50">
        <div class="bg-white rounded-[2rem] shadow-xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="py-5 px-6 text-left text-xs font-black text-[#003366] uppercase tracking-widest border-b border-gray-100">
                                Student Details
                            </th>
                            <th class="py-5 px-6 text-center text-xs font-black text-[#003366] uppercase tracking-widest border-b border-gray-100 w-48">
                                Quick Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody id="studentTableBody" class="divide-y divide-gray-50">
                        @forelse ($students as $student)
                            @php
                                $studentData = $student->only(['id', 'first_name', 'middle_name', 'last_name', 'gender', 'date_of_birth', 'nationality', 'religion']);
                                $primaryGuardian = $student->guardian;
                                $guardianData = $primaryGuardian
                                    ? array_merge(
                                        $primaryGuardian->only(['first_name', 'middle_name', 'last_name', 'relationship_to_child', 'contact_number', 'address']),
                                        ['email' => $primaryGuardian->user?->email ?? '']
                                    )
                                    : [];
                            @endphp

                            <tr class="group hover:bg-blue-50/30 transition-colors"
                                data-id="{{ $student->id }}"
                                data-student="{{ json_encode($studentData) }}"
                                data-guardian="{{ json_encode($guardianData) }}">
                                <td class="py-4 px-6">
                                    <div class="flex items-center space-x-4">
                                        <div class="relative">
                                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center text-blue-600 border border-blue-200 shadow-sm">
                                                <i class="fas fa-user-graduate text-lg"></i>
                                            </div>
                                            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full"></div>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 group-hover:text-blue-700 transition-colors">
                                                {{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name ?? '' }}
                                            </div>
                                            <div class="text-xs text-gray-500 font-medium">ID: #KW-2024-{{ $student->id }}</div>
                                                @if($primaryGuardian)
                                                    <div class="text-[10px] text-emerald-600 font-medium mt-0.5">
                                                        👤 {{ $primaryGuardian->first_name }} {{ $primaryGuardian->last_name }}
                                                    </div>
                                                @endif

                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="handleStudentAction('view', this)"
                                                title="View Profile"
                                                class="p-2.5 rounded-xl border border-blue-100 text-blue-500 hover:bg-blue-500 hover:text-white transition-all active:scale-90">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="handleStudentAction('edit', this)"
                                                title="Edit Information"
                                                class="p-2.5 rounded-xl border border-orange-100 text-orange-400 hover:bg-orange-400 hover:text-white transition-all active:scale-90">
                                            <i class="fas fa-pen-nib"></i>
                                        </button>
                                        <button onclick="handleStudentAction('delete', this)"
                                                title="Move to Trash"
                                                class="p-2.5 rounded-xl border border-red-100 text-red-400 hover:bg-red-500 hover:text-white transition-all active:scale-90">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyStateRow">
                                <td colspan="2" class="py-32 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4 border-2 border-dashed border-gray-200">
                                            <i class="fas fa-users-slash text-gray-300 text-2xl"></i>
                                        </div>
                                        <p class="text-gray-400 font-medium italic">No students found in the directory.</p>
                                        <button onclick="document.getElementById('addStudentModal').classList.remove('hidden')"
                                                class="mt-4 text-blue-500 text-sm font-bold hover:underline flex items-center gap-1">
                                            <i class="fas fa-plus"></i> Register your first student
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
           <!-- Pagination Footer -->
            @if ($students->hasPages())
                <div class="px-6 py-4 border-t bg-gray-50 flex items-center justify-between text-sm text-gray-600">
                    <div>Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} students</div>
                    <div class="flex gap-1">
                        {{ $students->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- ====================== ADD STUDENT MODAL (unchanged structure) ====================== -->
    <div id="addStudentModal" class="hidden fixed inset-0 z-[100] overflow-hidden">
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-3xl overflow-hidden transform transition-all">
                <div class="bg-blue-50/50 px-10 py-6 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-black text-[#003366]">Enroll New Student</h2>
                        <p class="text-gray-500 text-sm">Fill in the profile and link a guardian account.</p>
                    </div>
                    <button onclick="closeModal()" class="w-10 h-10 flex items-center justify-center rounded-full bg-white shadow-sm hover:bg-red-50 hover:text-red-500 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="enrollStudentForm" method="POST" action="{{ route('students.storeWithGuardian') }}" class="p-10 text-left overflow-y-auto max-h-[70vh]">
                    @csrf
                    <!-- Student Profile Section -->
                    <section>
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center text-xs font-bold">1</div>
                            <h3 class="text-lg font-bold text-gray-800">Student Profile</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-6 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">First Name</label>
                                <input type="text" name="student_first_name" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 transition-all outline-none">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Middle Name</label>
                                <input type="text" name="student_middle_name" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 transition-all outline-none">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Last Name</label>
                                <input type="text" name="student_last_name" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 transition-all outline-none">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Gender</label>
                                <select name="student_gender" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 transition-all outline-none">
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Date of Birth</label>
                                <input type="date" name="student_date_of_birth" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 transition-all outline-none">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nationality</label>
                                <input type="text" name="student_nationality" required value="Filipino" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 transition-all outline-none">
                            </div>
                            <div class="md:col-span-6">
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Religion</label>
                                <input type="text" name="student_religion" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 transition-all outline-none">
                            </div>
                        </div>
                    </section>

                    <!-- Guardian Section -->
                    <section>
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center text-xs font-bold">2</div>
                            <h3 class="text-lg font-bold text-gray-800">Guardian</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-6 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">First Name</label>
                                <input type="text" name="guardian_first_name" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 outline-none">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Middle Name</label>
                                <input type="text" name="guardian_middle_name" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 outline-none">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Last Name</label>
                                <input type="text" name="guardian_last_name" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 outline-none">
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Email Address (For Login)</label>
                                <input type="email" name="guardian_email" required placeholder="guardian@example.ph" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 outline-none">
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Relationship</label>
                                <select name="guardian_relationship" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 outline-none">
                                    <option value="Mother">Mother</option>
                                    <option value="Father">Father</option>
                                    <option value="Guardian">Guardian</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Contact Number</label>
                                <input type="tel" id="edit_guardian_contact_number" name="guardian_contact_number" maxlength="11" placeholder="09xxxxxxxxx" pattern="^09\d{9}$" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 outline-none">
                            </div>
                            <div class="md:col-span-4">
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Residential Address</label>
                                <input type="text" name="guardian_address" required value="Brgy. Balite, Quezon City" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 outline-none">
                            </div>
                        </div>
                    </section>

                    <div class="flex items-center justify-end gap-4 mt-12 pt-6 border-t border-gray-100">
                        <button type="button" onclick="closeModal()" class="px-6 py-3 rounded-xl text-gray-500 font-bold hover:bg-gray-50 transition-colors">
                            Discard
                        </button>
                        <button type="submit" id="enrollSubmitBtn" class="bg-[#003366] text-white px-10 py-3 rounded-xl font-bold shadow-xl shadow-blue-900/10 hover:bg-blue-900 transition-all flex items-center">
                            <span>Enroll Student</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ====================== VIEW PROFILE MODAL ====================== -->
    <div id="viewStudentModal" class="hidden fixed inset-0 z-[100] overflow-hidden">
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-3xl overflow-hidden">
                <div class="bg-blue-50/50 px-10 py-6 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-black text-[#003366]" id="viewModalTitle">Student Profile</h2>
                        <p class="text-gray-500 text-sm" id="viewIdDisplay"></p>
                    </div>
                    <button onclick="closeViewModal()" class="w-10 h-10 flex items-center justify-center rounded-full bg-white shadow-sm hover:bg-red-50 hover:text-red-500 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="p-10 text-left max-h-[70vh] overflow-y-auto space-y-10">
                    <section>
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center text-xs font-bold">1</div>
                            <h3 class="text-lg font-bold text-gray-800">Student Profile</h3>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-6 text-sm">
                            <div><span class="font-medium text-gray-400">Full Name:</span><br><span id="viewFullName" class="font-semibold"></span></div>
                            <div><span class="font-medium text-gray-400">Gender:</span><br><span id="viewGender" class="font-semibold"></span></div>
                            <div><span class="font-medium text-gray-400">Age:</span><br><span id="viewAge" class="font-semibold"></span></div>
                            <div><span class="font-medium text-gray-400">Date of Birth:</span><br><span id="viewDob" class="font-semibold"></span></div>
                            <div><span class="font-medium text-gray-400">Nationality:</span><br><span id="viewNationality" class="font-semibold"></span></div>
                            <div><span class="font-medium text-gray-400">Religion:</span><br><span id="viewReligion" class="font-semibold"></span></div>
                        </div>
                    </section>

                    <section>
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">2</div>
                            <h3 class="text-lg font-bold text-gray-800">Guardian</h3>
                        </div>
                        <div id="viewGuardiansList" class="space-y-4">
                            <!-- Populated by JS -->
                        </div>
                    </section>
                </div>

                <div class="px-10 py-6 border-t flex justify-end">
                    <button onclick="closeViewModal()" class="px-8 py-3 rounded-xl font-bold text-[#003366] hover:bg-gray-100">Close Profile</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ====================== EDIT STUDENT MODAL ====================== -->
    <div id="editStudentModal" class="hidden fixed inset-0 z-[100] overflow-hidden">
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-3xl overflow-hidden transform transition-all">
                <div class="bg-orange-50/50 px-10 py-6 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-black text-[#003366]" id="editModalTitle">Edit Student Profile</h2>
                        <p class="text-gray-500 text-sm">Update student and guardian information.</p>
                    </div>
                    <button onclick="closeEditModal()" class="w-10 h-10 flex items-center justify-center rounded-full bg-white shadow-sm hover:bg-red-50 hover:text-red-500 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="editStudentForm" method="POST" class="p-10 text-left overflow-y-auto max-h-[70vh]">
                    @csrf
                    <input type="hidden" name="_method" value="PUT" id="editMethodInput">

                    <div class="space-y-10">
                        <!-- Student Profile -->
                        <section>
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center text-xs font-bold">1</div>
                                <h3 class="text-lg font-bold text-gray-800">Student Profile</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-6 gap-6">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">First Name</label>
                                    <input type="text" id="edit_student_first_name" name="student_first_name" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 transition-all outline-none">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Middle Name</label>
                                    <input type="text" id="edit_student_middle_name" name="student_middle_name" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 transition-all outline-none">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Last Name</label>
                                    <input type="text" id="edit_student_last_name" name="student_last_name" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 transition-all outline-none">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Gender</label>
                                    <select id="edit_student_gender" name="student_gender" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 transition-all outline-none">
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Date of Birth</label>
                                    <input type="date" id="edit_student_date_of_birth" name="student_date_of_birth" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 transition-all outline-none">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nationality</label>
                                    <input type="text" id="edit_student_nationality" name="student_nationality" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 transition-all outline-none">
                                </div>
                                <div class="md:col-span-6">
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Religion</label>
                                    <input type="text" id="edit_student_religion" name="student_religion" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 transition-all outline-none">
                                </div>
                            </div>
                        </section>

                        <!-- Guardian -->
                        <section>
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center text-xs font-bold">2</div>
                                <h3 class="text-lg font-bold text-gray-800">Guardian <span class="text-xs font-normal text-orange-500">(editable)</span></h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-6 gap-6">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">First Name</label>
                                    <input type="text" id="edit_guardian_first_name" name="guardian_first_name" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 outline-none">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Middle Name</label>
                                    <input type="text" id="edit_guardian_middle_name" name="guardian_middle_name" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 outline-none">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Last Name</label>
                                    <input type="text" id="edit_guardian_last_name" name="guardian_last_name" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 outline-none">
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">
                                        Email Address <span class="text-orange-500 text-[10px]">(leave blank to keep current)</span>
                                    </label>
                                    <input type="email" id="edit_guardian_email" name="guardian_email"
                                        placeholder=""
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 outline-none">
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Relationship</label>
                                    <select id="edit_guardian_relationship" name="guardian_relationship" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 outline-none">
                                        <option value="Mother">Mother</option>
                                        <option value="Father">Father</option>
                                        <option value="Guardian">Guardian</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">
                                        Contact Number <span class="text-orange-500 text-[10px]">(leave blank to keep current)</span>
                                    </label>
                                    <input type="tel"
                                        id="edit_guardian_contact_number"
                                        name="guardian_contact_number"
                                        maxlength="11"
                                        placeholder=""
                                        pattern="^09\d{9}$"
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 outline-none">
                                </div>
                                <div class="md:col-span-4">
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Residential Address</label>
                                    <input type="text" id="edit_guardian_address" name="guardian_address" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 outline-none">
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">New Password</label>
                                    <input type="password" id="edit_guardian_password" name="guardian_password" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 outline-none">
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Confirm Password</label>
                                    <input type="password" id="edit_guardian_password_confirmation" name="guardian_password_confirmation" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 outline-none">
                                </div>
                            </div>
                        </section>

                    </div>

                    <div class="flex items-center justify-end gap-4 mt-12 pt-6 border-t border-gray-100">
                        <button type="button" onclick="closeEditModal()" class="px-6 py-3 rounded-xl text-gray-500 font-bold hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" id="editSubmitBtn" class="bg-orange-600 text-white px-10 py-3 rounded-xl font-bold shadow-xl hover:bg-orange-700 transition-all flex items-center">
                            <span>Save Changes</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // ====================== SHARED HELPERS ======================
        function closeModal() {
            document.getElementById('addStudentModal').classList.add('hidden');
            document.getElementById('enrollStudentForm').reset();
        }

        function closeViewModal() {
            document.getElementById('viewStudentModal').classList.add('hidden');
        }

        function closeEditModal() {
            document.getElementById('editStudentModal').classList.add('hidden');
            document.getElementById('editStudentForm').reset();
        }

        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.style.cssText = `position:fixed;bottom:24px;right:24px;padding:16px 24px;border-radius:9999px;color:white;font-weight:600;z-index:9999;box-shadow:0 10px 15px -3px rgb(0 0 0 / 0.2);transition:all 0.3s;`;
            toast.style.backgroundColor = type === 'success' ? '#10b981' : '#ef4444';
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 2800);
        }

        // ====================== HANDLE ALL BUTTON CLICKS ======================
        function handleStudentAction(action, buttonElement) {
            const row = buttonElement.closest('tr');
            const studentId = row.dataset.id;
            const student = JSON.parse(row.dataset.student || '{}');
            const guardian = JSON.parse(row.dataset.guardian || '{}');

            if (action === 'view') {
                populateViewModal(student, guardian, studentId);
                document.getElementById('viewStudentModal').classList.remove('hidden');
            } else if (action === 'edit') {
                populateEditModal(student, guardian, studentId);
                document.getElementById('editStudentModal').classList.remove('hidden');
            } else if (action === 'delete') {
                if (confirm(`Move Student #KW-2024-${studentId} to trash?`)) {
                    deleteStudent(studentId, row);
                }
            }
        }

        // ====================== POPULATE VIEW MODAL ======================
        function calculateAge(dob) {
            const birthDate = new Date(dob);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();

            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--; // not yet had birthday this year
            }
            return age;
        }

        function formatDate(dob) {
            const date = new Date(dob);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        function populateViewModal(student, guardian, id) {
            document.getElementById('viewIdDisplay').innerHTML = `Student ID: <strong>#KW-2024-${id}</strong>`;
            document.getElementById('viewFullName').textContent = `${student.last_name}, ${student.first_name} ${student.middle_name || ''}`.trim();
            document.getElementById('viewGender').textContent = student.gender === 'male' ? 'Male' : 'Female';

            // Format DOB without time
            const formattedDob = formatDate(student.date_of_birth);
            document.getElementById('viewDob').textContent = formattedDob;

            document.getElementById('viewNationality').textContent = student.nationality;
            document.getElementById('viewReligion').textContent = student.religion;

            // Show age
            const age = calculateAge(student.date_of_birth);
            document.getElementById('viewAge').textContent = `${age} years old`;

            const guardiansHTML = guardian.first_name
                ? `
                    <div class="flex items-center gap-4 p-4 bg-emerald-50 rounded-2xl">
                        <div class="w-10 h-10 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600 text-xl">👤</div>
                        <div class="flex-1">
                            <div class="font-semibold">${guardian.first_name} ${guardian.middle_name || ''} ${guardian.last_name}</div>
                            <div class="text-xs text-emerald-600">${guardian.relationship_to_child || 'Guardian'}</div>
                            <div class="text-xs text-gray-500">${guardian.contact_number} • ${guardian.address}</div>
                        </div>
                    </div>
                `
                : `<p class="text-gray-400 italic">No guardians linked yet.</p>`;

            document.getElementById('viewGuardiansList').innerHTML = guardiansHTML;
        }


        // ====================== POPULATE EDIT MODAL ======================
        function formatDateForInput(dob) {
            if (!dob) return '';
            const date = new Date(dob);
            // Format as YYYY-MM-DD for <input type="date">
            return date.toISOString().split('T')[0];
        }

        function populateEditModal(student, guardian, id) {
            const form = document.getElementById('editStudentForm');
            form.action = `/students/${id}`;

            // Student fields
            document.getElementById('edit_student_first_name').value = student.first_name || '';
            document.getElementById('edit_student_middle_name').value = student.middle_name || '';
            document.getElementById('edit_student_last_name').value = student.last_name || '';
            document.getElementById('edit_student_gender').value = student.gender || 'male';
            document.getElementById('edit_student_date_of_birth').value = formatDateForInput(student.date_of_birth);
            document.getElementById('edit_student_nationality').value = student.nationality || 'Filipino';
            document.getElementById('edit_student_religion').value = student.religion || 'Catholic';

            // Guardian fields ( only)
            if (guardian.first_name) {
                document.getElementById('edit_guardian_first_name').value = guardian.first_name || '';
                document.getElementById('edit_guardian_middle_name').value = guardian.middle_name || '';
                document.getElementById('edit_guardian_last_name').value = guardian.last_name || '';
                document.getElementById('edit_guardian_email').value = ''; // start blank
                document.getElementById('edit_guardian_email').placeholder = guardian.email
                    ? `Current: ${guardian.email}`
                    : 'guardian@example.ph';
                document.getElementById('edit_guardian_relationship').value = guardian.relationship_to_child || 'Mother';
                document.getElementById('edit_guardian_contact_number').value = ''; // keep blank for editing
                document.getElementById('edit_guardian_contact_number').placeholder = guardian.contact_number
                    ? `Current: ${guardian.contact_number}`
                    : '09xxxxxxxxx';
                document.getElementById('edit_guardian_address').value = guardian.address || 'Brgy. Balite, Quezon City';
                document.getElementById('edit_guardian_password').value = '';
                document.getElementById('edit_guardian_password_confirmation').value = '';
            }
        }


        // ====================== DELETE (AJAX Soft Delete) ======================
        async function deleteStudent(id, row) {
            try {
                const response = await fetch(`/students/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    row.style.transition = 'all 0.4s ease';
                    row.style.opacity = '0';
                    setTimeout(() => {
                        row.remove();

                        const tbody = document.getElementById('studentTableBody');
                        if (tbody.children.length === 0) {
                            const emptyHTML = `
                                <tr id="emptyStateRow">
                                    <td colspan="2" class="py-32 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4 border-2 border-dashed border-gray-200">
                                                <i class="fas fa-users-slash text-gray-300 text-2xl"></i>
                                            </div>
                                            <p class="text-gray-400 font-medium italic">No students found in the directory.</p>
                                            <button onclick="document.getElementById('addStudentModal').classList.remove('hidden')" class="mt-4 text-blue-500 text-sm font-bold hover:underline flex items-center gap-1">
                                                <i class="fas fa-plus"></i> Register your first student
                                            </button>
                                        </div>
                                    </td>
                                </tr>`;
                            tbody.innerHTML = emptyHTML;
                        }
                        showToast('Student moved to trash successfully!', 'success');
                    }, 400);
                } else {
                    const data = await response.json();
                    alert(data.message || 'Failed to delete student');
                }
            } catch (e) {
                alert('Connection error. Please try again.');
            }
        }

        // ====================== SEARCH (real-time client-side filter) ======================
        function initSearch() {
            const searchInput = document.getElementById('studentSearch');
            if (!searchInput) return;

            searchInput.addEventListener('input', () => {
                const term = searchInput.value.toLowerCase().trim();
                const rows = document.querySelectorAll('#studentTableBody tr:not(#emptyStateRow)');

                let visibleCount = 0;
                rows.forEach(row => {
                    const text = (row.textContent || '').toLowerCase();
                    if (text.includes(term)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Show empty state if nothing matches
                const tbody = document.getElementById('studentTableBody');
                const existingEmpty = document.getElementById('emptyStateRow');
                if (visibleCount === 0 && term !== '') {
                    if (!existingEmpty) {
                        const emptyHTML = `
                            <tr id="emptyStateRow">
                                <td colspan="2" class="py-32 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4 border-2 border-dashed border-gray-200">
                                            <i class="fas fa-users-slash text-gray-300 text-2xl"></i>
                                        </div>
                                        <p class="text-gray-400 font-medium italic">No students match your search.</p>
                                    </div>
                                </td>
                            </tr>`;
                        tbody.innerHTML += emptyHTML;
                    }
                } else if (existingEmpty) {
                    existingEmpty.remove();
                }
            });
        }

        // ====================== ADD STUDENT (AJAX – keeps your form structure) ======================
        function initEnrollment() {
            const form = document.getElementById('enrollStudentForm');
            if (!form) return;

            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                const submitBtn = document.getElementById('enrollSubmitBtn');
                const originalHTML = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<i class="fas fa-circle-notch fa-spin mr-2"></i> Enrolling...`;

                const formData = new FormData(form);

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        closeModal();
                        let msg = '✅ Student enrolled successfully!';
                        if (result.temporary_password) {
                            alert(`🎉 Student enrolled!\n\n🔑 Guardian Temporary Password:\n${result.temporary_password}\n\nPlease copy this and give it to the guardian immediately.\nThey should change it on first login.`);
                        }
                        showToast(msg, 'success');
                        setTimeout(() => location.reload(), 800);
                    } else {
                        alert(result.message || 'Enrollment failed.');
                    }
                } catch (err) {
                    alert('Connection error. Please try again.');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHTML;
                }
            });
        }

// ====================== EDIT FORM (AJAX PUT via _method) ======================
function initEditForm() {
    const form = document.getElementById('editStudentForm');
    if (!form) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const submitBtn = document.getElementById('editSubmitBtn');
        const originalHTML = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<i class="fas fa-circle-notch fa-spin mr-2"></i> Saving...`;

        const formData = new FormData(form);

        // ✅ Ensure empty email/contact fields are not sent as null overwrites
        if (!formData.get('guardian_email')) {
            formData.delete('guardian_email');
        }
        if (!formData.get('guardian_contact_number')) {
            formData.delete('guardian_contact_number');
        }

        try {
            const response = await fetch(form.action, {
                method: 'POST', // ✅ send as POST with _method=PUT
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                closeEditModal();
                showToast('Student updated successfully!', 'success');
                setTimeout(() => location.reload(), 600);
            } else {
                alert(data.message || 'Update failed');
            }
        } catch (err) {
            alert('Connection error. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHTML;
        }
    });
}

        // ====================== INIT EVERYTHING ======================
        document.addEventListener('DOMContentLoaded', function () {
            initSearch();
            initEnrollment();
            initEditForm();
            console.log('%c✅ All buttons in student.blade.php are now fully functional (Add • View • Edit • Delete)', 'color:#007bff;font-weight:bold;font-size:13px');
        });
    </script>
</x-layout>

<x-layout>
    <x-slot:title>KidWatch | Students</x-slot>

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-4xl font-black text-[#003366] tracking-[-1px] leading-none uppercase italic">Student Directory</h1>
            <p class="text-gray-500 text-sm">
                Monitor and manage student profiles and guardian connections. ({{ $students->count() }} total)
            </p>
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
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white 
                        focus:ring-4 focus:ring-blue-100 focus:border-blue-400 
                        transition-all duration-200 shadow-sm text-sm">
            </div>
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
                                $studentData = $student->only([
                                    'id','first_name','middle_name','last_name',
                                    'gender','date_of_birth','nationality','religion','photo_path'
                                ]);
                                $primaryGuardian = $student->guardian;
                                $guardianData = $primaryGuardian
                                    ? array_merge(
                                        $primaryGuardian->only([
                                            'first_name','middle_name','last_name',
                                            'relationship_to_child','contact_number','address'
                                        ]),
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
                                            <img src="{{ asset('storage/' . $student->photo_path) }}"
                                                alt="Student Photo"
                                                class="w-12 h-12 rounded-2xl object-cover border border-blue-200 shadow-sm">
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
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

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
    <div id="viewStudentModal" class="hidden fixed inset-0 z-[100] overflow-hidden">
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-3xl overflow-hidden">
                <div class="bg-blue-50/50 px-10 py-6 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-black text-[#003366]" id="viewModalTitle">Student Profile</h2>
                        <p class="text-gray-500 text-sm" id="viewIdDisplay"></p>
                    </div>
                    <button onclick="closeViewModal()"
                            class="w-10 h-10 flex items-center justify-center rounded-full bg-white shadow-sm hover:bg-red-50 hover:text-red-500 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="p-10 text-left max-h-[70vh] overflow-y-auto space-y-10">
                    <div class="flex justify-center mb-6">
                        <img id="viewStudentPhoto"
                            src="{{ $student->photo_path && Storage::disk('public')->exists($student->photo_path) 
                                ? asset('storage/' . $student->photo_path) 
                                : asset('images/default-avatar.png') }}"
                            alt="Student Photo"
                            class="w-32 h-32 rounded-full object-cover border-4 border-blue-200 shadow-md">
                    </div>

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

                        </div>
                    </section>
                </div>

                <div class="px-10 py-6 border-t flex justify-end">
                    <button onclick="closeViewModal()"
                            class="px-8 py-3 rounded-xl font-bold text-[#003366] hover:bg-gray-100">
                        Close Profile
                    </button>
                </div>
            </div>
        </div>
    </div>

<div id="editStudentModal" class="hidden fixed inset-0 z-[100] overflow-hidden">
    <div class="flex items-center justify-center min-h-screen p-4 text-center">
        <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-3xl overflow-hidden transform transition-all">
            <div class="bg-orange-50/50 px-10 py-6 border-b border-gray-100 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-black text-[#003366]" id="editModalTitle">Edit Student Profile</h2>
                    <p class="text-gray-500 text-sm">Update student and guardian information.</p>
                </div>
                <button onclick="closeEditModal()"
                        class="w-10 h-10 flex items-center justify-center rounded-full bg-white shadow-sm hover:bg-red-50 hover:text-red-500 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="editStudentForm" method="POST" class="p-10 text-left overflow-y-auto max-h-[70vh]">
                @csrf
                <input type="hidden" name="_method" value="PUT" id="editMethodInput">

                <div class="space-y-10">
                    <section>
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center text-xs font-bold">1</div>
                            <h3 class="text-lg font-bold text-gray-800">Student Profile</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-6 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-black text-gray-400 uppercase mb-2">First Name</label>
                                <input type="text" id="edit_student_first_name" name="student_first_name" required class="w-full px-4 py-3 border rounded-xl">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-black text-gray-400 uppercase mb-2">Middle Name</label>
                                <input type="text" id="edit_student_middle_name" name="student_middle_name" class="w-full px-4 py-3 border rounded-xl">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-black text-gray-400 uppercase mb-2">Last Name</label>
                                <input type="text" id="edit_student_last_name" name="student_last_name" required class="w-full px-4 py-3 border rounded-xl">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-black text-gray-400 uppercase mb-2">Gender</label>
                                <select id="edit_student_gender" name="student_gender" required class="w-full px-4 py-3 border rounded-xl">
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-black text-gray-400 uppercase mb-2">Date of Birth</label>
                                <input type="date" id="edit_student_date_of_birth" name="student_date_of_birth" required class="w-full px-4 py-3 border rounded-xl">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-black text-gray-400 uppercase mb-2">Nationality</label>
                                <input type="text" id="edit_student_nationality" name="student_nationality" required class="w-full px-4 py-3 border rounded-xl">
                            </div>
                            <div class="md:col-span-6">
                                <label class="block text-xs font-black text-gray-400 uppercase mb-2">Religion</label>
                                <input type="text" id="edit_student_religion" name="student_religion" required class="w-full px-4 py-3 border rounded-xl">
                            </div>
                            <div class="md:col-span-6">
                                <label class="block text-xs font-black text-gray-400 uppercase mb-2">Student Photo</label>
                                <input type="file" id="edit_student_photo" name="student_photo" accept="image/*"
                                    class="w-full px-4 py-3 border rounded-xl">
                                <p class="text-xs text-gray-500 mt-1">Optional — leave blank to keep current photo.</p>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="flex items-center justify-end gap-4 mt-12 pt-6 border-t border-gray-100">
                    <button type="button" onclick="closeEditModal()" class="px-6 py-3 rounded-xl text-gray-500 font-bold hover:bg-gray-50 transition-colors">Cancel</button>
                    <button type="submit" id="editSubmitBtn" class="bg-orange-600 text-white px-10 py-3 rounded-xl font-bold shadow-xl hover:bg-orange-700 transition-all flex items-center">
                        <span>Save Changes</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

function closeModal() {
    const modal = document.getElementById('addStudentModal');
    const form = document.getElementById('enrollStudentForm');
    if (modal) modal.classList.add('hidden');
    if (form) form.reset();
}

function closeViewModal() {
    const modal = document.getElementById('viewStudentModal');
    if (modal) modal.classList.add('hidden');
}

function closeEditModal() {
    const modal = document.getElementById('editStudentModal');
    const form = document.getElementById('editStudentForm');
    if (modal) modal.classList.add('hidden');
    if (form) form.reset();
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.style.cssText = `position:fixed;bottom:24px;right:24px;padding:16px 24px;
        border-radius:9999px;color:white;font-weight:600;z-index:9999;
        box-shadow:0 10px 15px -3px rgb(0 0 0 / 0.2);transition:all 0.3s;`;
    toast.style.backgroundColor = type === 'success' ? '#10b981' : '#ef4444';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 2800);
}

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
    }
}

function calculateAge(dob) {
    const birthDate = new Date(dob);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
}

function formatDate(dob) {
    const date = new Date(dob);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
}

function populateViewModal(student, guardian, id) {
    document.getElementById('viewIdDisplay').innerHTML = `Student ID: <strong>#KW-2024-${id}</strong>`;
    document.getElementById('viewFullName').textContent = `${student.last_name}, ${student.first_name} ${student.middle_name || ''}`.trim();
    document.getElementById('viewGender').textContent = student.gender === 'male' ? 'Male' : 'Female';
    document.getElementById('viewDob').textContent = formatDate(student.date_of_birth);
    document.getElementById('viewNationality').textContent = student.nationality;
    document.getElementById('viewReligion').textContent = student.religion;
    document.getElementById('viewAge').textContent = `${calculateAge(student.date_of_birth)} years old`;

    const photoElement = document.getElementById('viewStudentPhoto');
    if (photoElement) {
        photoElement.src = student.photo_path ? `/storage/${student.photo_path}` : '/images/default-avatar.png';
    }

    const guardiansHTML = guardian.first_name
        ? `
        <div class="flex items-center gap-4 p-4 bg-emerald-50 rounded-2xl">
            <div class="w-10 h-10 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600 text-xl">👤</div>
            <div class="flex-1">
                <div class="font-semibold">${guardian.first_name} ${guardian.middle_name || ''} ${guardian.last_name}</div>
                <div class="text-xs text-emerald-600">${guardian.relationship_to_child || 'Guardian'}</div>
                <div class="text-xs text-gray-500">${guardian.contact_number} • ${guardian.address}</div>
            </div>
        </div>`
        : `<p class="text-gray-400 italic">No guardians linked yet.</p>`;
    document.getElementById('viewGuardiansList').innerHTML = guardiansHTML;
}

function formatDateForInput(dob) {
    if (!dob) return '';
    const date = new Date(dob);
    return date.toISOString().split('T')[0];
}

function populateEditModal(student, guardian, id) {
    const form = document.getElementById('editStudentForm');
    form.action = `/students/${id}`;

    document.getElementById('edit_student_first_name').value = student.first_name || '';
    document.getElementById('edit_student_middle_name').value = student.middle_name || '';
    document.getElementById('edit_student_last_name').value = student.last_name || '';
    document.getElementById('edit_student_gender').value = student.gender || 'male';
    document.getElementById('edit_student_date_of_birth').value = formatDateForInput(student.date_of_birth);
    document.getElementById('edit_student_nationality').value = student.nationality || 'Filipino';
    document.getElementById('edit_student_religion').value = student.religion || 'Catholic';
}

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

        const tbody = document.getElementById('studentTableBody');
        const existingEmpty = document.getElementById('emptyStateRow');
        if (visibleCount === 0 && term !== '') {
            if (!existingEmpty) {
                tbody.innerHTML += `
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
            }
        } else if (existingEmpty) {
            existingEmpty.remove();
        }
    });
}

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

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            let data;
            try {
                data = await response.json();
            } catch (err) {
                data = { success: response.ok };
            }

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

document.addEventListener('DOMContentLoaded', function () {
    initSearch();
    initEditForm();

    console.log('%c Student page scripts initialized (Search • Edit)',
        'color:#007bff;font-weight:bold;font-size:13px');
});
</script>
</x-layout>

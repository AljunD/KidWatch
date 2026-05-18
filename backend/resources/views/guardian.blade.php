<x-layout>
<x-slot:title>KidWatch | Guardians</x-slot>

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
  <div>
    <h1 class="text-4xl font-black text-[#003366] tracking-[-1px] leading-none uppercase italic">Guardian Directory</h1>
    <p class="text-gray-500 text-sm">
      Manage guardian accounts and linked students. ({{ $guardians->total() }} total)
    </p>
  </div>

  <div class="flex items-center gap-3 w-full md:w-auto">
  <div class="relative flex-1 md:w-72">
    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
      <i class="fas fa-search text-gray-400 text-sm"></i>
    </span>
    <input
      type="text"
      id="guardianSearch"
      placeholder="Search by guardian name or email..."
      class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white 
             focus:ring-4 focus:ring-blue-100 focus:border-blue-400 
             transition-all duration-200 shadow-sm text-sm">
  </div>

  <button
    type="button"
    onclick="openAddStudentGuardianModal()"
    class="flex items-center whitespace-nowrap px-5 py-2.5 rounded-xl font-bold 
           bg-[#28a745] hover:bg-[#1e7e34] text-white shadow-lg shadow-green-200 
           transition-all duration-200 active:scale-95">
    <i class="fas fa-user-graduate mr-2"></i>
    Add Student + Guardian
  </button>
  </div>
</div>

<div class="bg-[#f1f5f9] rounded-[2.5rem] p-3 md:p-6 shadow-inner border border-white/50">
  <div class="bg-white rounded-[2rem] shadow-xl overflow-hidden border border-gray-100">
    <div class="overflow-x-auto">
      <table class="min-w-full">
        <thead>
          <tr class="bg-gray-50/50">
            <th class="py-5 px-6 text-left text-xs font-black text-[#003366] uppercase tracking-widest border-b border-gray-100">Guardian Details</th>
            <th class="py-5 px-6 text-left text-xs font-black text-[#003366] uppercase tracking-widest border-b border-gray-100">Linked Students</th>
            <th class="py-5 px-6 text-center text-xs font-black text-[#003366] uppercase tracking-widest border-b border-gray-100 w-56">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          @forelse($guardians as $guardian)
            <tr
              data-id="{{ $guardian->id }}"
              data-guardian="{{ json_encode([
                  'id' => $guardian->id,
                  'first_name' => $guardian->first_name,
                  'middle_name' => $guardian->middle_name,
                  'last_name' => $guardian->last_name,
                  'contact_number' => $guardian->contact_number,
                  'address' => $guardian->address,
                  'email' => $guardian->user->email,
                  'relationship_to_child' => $guardian->relationship_to_child,
                  'created_at' => $guardian->user->created_at->toDateString(),
                  'email_verified_at' => $guardian->user->email_verified_at,
                'students' => $guardian->students->map(fn($s) => [
                    'id' => $s->id,   
                    'full_name' => $s->full_name,
                    'gender' => $s->gender,
                    'date_of_birth' => $s->date_of_birth,
                    'photo_path' => $s->photo_path,
                ]),
              ]) }}">
              <td class="py-4 px-6">
                <div class="font-bold text-gray-900 group-hover:text-blue-700 transition-colors">
                  {{ $guardian->first_name }} {{ $guardian->middle_name }} {{ $guardian->last_name }}
                </div>
              </td>
              <td class="py-4 px-6">
                @if($guardian->students->count())
                  <ul class="text-sm text-gray-700 space-y-3">
                    @foreach($guardian->students as $student)
                      <li class="flex items-center gap-3">
                        @if($student->photo_path)
                          <img src="{{ asset('storage/' . $student->photo_path) }}"
                               alt="Student Photo"
                               class="w-10 h-10 object-cover rounded-full border">
                        @else
                          <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-xs text-gray-500">N/A</div>
                        @endif
                        <span>
                          {{ $student->full_name }}
                        </span>
                      </li>
                    @endforeach
                  </ul>
                @else
                  <span class="text-gray-400 italic">No students linked</span>
                @endif
              </td>
                <td class="py-4 px-6 text-center">
                <div class="flex items-center justify-center gap-2">
                    <button onclick="openViewGuardianModal(this)"
                            class="p-2.5 rounded-xl border border-blue-100 text-blue-500 hover:bg-blue-500 hover:text-white transition-all active:scale-90"
                            title="View Guardian">
                    <i class="fas fa-eye"></i>
                    </button>

                    <button onclick="openEditGuardianModal(this)"
                            class="p-2.5 rounded-xl border border-orange-100 text-orange-400 hover:bg-orange-400 hover:text-white transition-all active:scale-90"
                            title="Edit Guardian">
                    <i class="fas fa-pen-nib"></i>
                    </button>

                    <button onclick="openAddStudentModal({{ $guardian->id }})"
                            class="p-2.5 rounded-xl border border-green-100 text-green-500 hover:bg-green-500 hover:text-white transition-all active:scale-90"
                            title="Add Student">
                    <i class="fas fa-user-graduate"></i>
                    </button>

                    <button type="button"
                            onclick="openTrashStudentModal({{ $guardian->id }}, '{{ $guardian->first_name }} {{ $guardian->last_name }}')"
                            class="p-2.5 rounded-xl border border-red-100 text-red-500 hover:bg-red-500 hover:text-white transition-all active:scale-90"
                            title="Trash Linked Students">
                    <i class="fas fa-user-graduate"></i>
                    </button>

                    <a href="{{ route('guardians.destroy', $guardian->id) }}"
                    class="p-2.5 rounded-xl border border-red-100 text-red-400 hover:bg-red-500 hover:text-white transition-all active:scale-90"
                    title="Move to Trash"
                    onclick="event.preventDefault(); document.getElementById('delete-form-{{ $guardian->id }}').submit();">
                    <i class="fas fa-trash-alt"></i>
                    </a>
                    <form id="delete-form-{{ $guardian->id }}"
                        action="{{ route('guardians.destroy', $guardian->id) }}"
                        method="POST"
                        class="hidden">
                    @csrf
                    @method('DELETE')
                    </form>
                </div>
                </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="py-32 text-center text-gray-400 italic">No guardians found in the directory.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($guardians->hasPages())
      <div class="px-6 py-4 border-t bg-gray-50 flex items-center justify-between text-sm text-gray-600">
        <div>Showing {{ $guardians->firstItem() }} to {{ $guardians->lastItem() }} of {{ $guardians->total() }} guardians</div>
        <div class="flex gap-1">{{ $guardians->links() }}</div>
      </div>
    @endif
  </div>
</div>

<div id="addStudentGuardianModal" class="hidden fixed inset-0 z-[100] overflow-hidden">
  <div class="flex items-center justify-center min-h-screen p-4 text-center">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-4xl overflow-hidden">

      <div class="bg-green-50/50 px-10 py-6 border-b flex justify-between items-center">
        <h2 class="text-2xl font-black text-[#003366]">Add Student + Guardian</h2>
        <button type="button" onclick="closeAddStudentGuardianModal()"
                class="w-10 h-10 flex items-center justify-center rounded-full bg-white shadow-sm hover:bg-red-50 hover:text-red-500">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <form id="addStudentGuardianForm" method="POST"
            action="{{ route('guardians.storeWithStudent') }}"
            enctype="multipart/form-data"
            class="p-10 text-left overflow-y-auto max-h-[75vh]">
        @csrf

        <section class="mb-8">
          <h3 class="text-lg font-bold text-gray-800 mb-4">Guardian Information</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <input type="text" name="guardian_first_name" placeholder="First Name" required class="w-full px-4 py-3 border rounded-xl">
            <input type="text" name="guardian_middle_name" placeholder="Middle Name" class="w-full px-4 py-3 border rounded-xl">
            <input type="text" name="guardian_last_name" placeholder="Last Name" required class="w-full px-4 py-3 border rounded-xl">
            <input type="email" name="guardian_email" placeholder="Email" required class="w-full px-4 py-3 border rounded-xl">
            <input type="password" name="guardian_password" placeholder="Password" class="w-full px-4 py-3 border rounded-xl">
            <input type="password" name="guardian_password_confirmation" placeholder="Confirm Password" class="w-full px-4 py-3 border rounded-xl">
            <input type="tel" name="guardian_contact_number" placeholder="09xxxxxxxxx" maxlength="11" pattern="^09\d{9}$" required class="w-full px-4 py-3 border rounded-xl">
            <input type="text" name="guardian_address" placeholder="Address" required class="w-full px-4 py-3 border rounded-xl md:col-span-2">
            <input type="text" name="guardian_relationship" placeholder="Relationship to Child" required class="w-full px-4 py-3 border rounded-xl md:col-span-2">
          </div>
        </section>

        <section>
          <h3 class="text-lg font-bold text-gray-800 mb-4">Student Information</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <input type="text" name="student_first_name" placeholder="First Name" required class="w-full px-4 py-3 border rounded-xl">
            <input type="text" name="student_middle_name" placeholder="Middle Name" class="w-full px-4 py-3 border rounded-xl">
            <input type="text" name="student_last_name" placeholder="Last Name" required class="w-full px-4 py-3 border rounded-xl">
            <select name="student_gender" required class="w-full px-4 py-3 border rounded-xl">
              <option value="">Select Gender</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
            </select>
            <input type="date" name="student_date_of_birth" required class="w-full px-4 py-3 border rounded-xl">
            <input type="text" name="student_nationality" placeholder="Nationality" required value="Filipino" class="w-full px-4 py-3 border rounded-xl">
            <input type="text" name="student_religion" placeholder="Religion" required class="w-full px-4 py-3 border rounded-xl md:col-span-2">
            <input type="file" name="student_photo" accept="image/*" required
                   class="w-full px-4 py-3 border rounded-xl md:col-span-2">
          </div>
        </section>

        <div class="flex justify-end gap-4 mt-12 pt-6 border-t">
          <button type="button" onclick="closeAddStudentGuardianModal()"
                  class="px-6 py-3 rounded-xl text-gray-500 font-bold hover:bg-gray-50">Discard</button>
          <button type="submit"
                  class="bg-[#003366] text-white px-10 py-3 rounded-xl font-bold shadow-xl hover:bg-blue-900">
            Save Student + Guardian
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div id="viewGuardianModal" class="hidden fixed inset-0 z-[100] overflow-hidden">
  <div class="flex items-center justify-center min-h-screen p-4 text-center">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-3xl overflow-hidden">
      <div class="bg-blue-50/50 px-10 py-6 border-b flex justify-between items-center">
        <h2 class="text-2xl font-black text-[#003366]">Guardian Profile</h2>
        <button type="button" onclick="closeViewGuardianModal()"
                class="w-10 h-10 flex items-center justify-center rounded-full bg-white shadow-sm hover:bg-red-50 hover:text-red-500">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="p-10 text-left max-h-[70vh] overflow-y-auto space-y-6">
        <div>
          <span class="font-medium text-gray-400">Full Name:</span><br>
          <span id="viewGuardianName" class="font-semibold"></span>
        </div>
        <div>
          <span class="font-medium text-gray-400">Email:</span><br>
          <span id="viewGuardianEmail" class="font-semibold"></span><br>
          <span id="viewGuardianEmailStatus" class="flex items-center gap-1 mt-1"></span>
        </div>
        <div>
          <span class="font-medium text-gray-400">Account Created:</span><br>
          <span id="viewGuardianCreatedAt" class="font-semibold"></span>
        </div>
        <div>
          <span class="font-medium text-gray-400">Residential Address:</span><br>
          <span id="viewGuardianAddress" class="font-semibold"></span>
        </div>
        <div>
          <span class="font-medium text-gray-400">Relationship to Child:</span><br>
          <span id="viewGuardianRelationship" class="font-semibold"></span>
        </div>
        <div>
            <span class="font-medium text-gray-400">Linked Students:</span><br>
            <ul id="viewGuardianStudents" class="space-y-3 text-sm">
            </ul>
        </div>
      </div>
      <div class="px-10 py-6 border-t flex justify-end">
        <button type="button" onclick="closeViewGuardianModal()"
                class="px-8 py-3 rounded-xl font-bold text-[#003366] hover:bg-gray-100">Close</button>
      </div>
    </div>
  </div>
</div>

<div id="editGuardianModal" class="hidden fixed inset-0 z-[100] overflow-hidden">
  <div class="flex items-center justify-center min-h-screen p-4 text-center">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-3xl overflow-hidden">

      <div class="bg-orange-50/50 px-10 py-6 border-b flex justify-between items-center">
        <h2 class="text-2xl font-black text-[#003366]">Edit Guardian Profile</h2>
        <button onclick="closeEditGuardianModal()"
                class="w-10 h-10 flex items-center justify-center rounded-full bg-white shadow-sm hover:bg-red-50 hover:text-red-500">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <form id="editGuardianForm" method="POST" class="p-10 text-left overflow-y-auto max-h-[70vh]">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-xs font-black text-gray-400 uppercase mb-2">First Name</label>
            <input type="text" id="edit_guardian_first_name" name="first_name" required
                   class="w-full px-4 py-3 bg-gray-50 border rounded-xl">
          </div>

          <div>
            <label class="block text-xs font-black text-gray-400 uppercase mb-2">Middle Name</label>
            <input type="text" id="edit_guardian_middle_name" name="middle_name"
                   class="w-full px-4 py-3 bg-gray-50 border rounded-xl">
          </div>

          <div>
            <label class="block text-xs font-black text-gray-400 uppercase mb-2">Last Name</label>
            <input type="text" id="edit_guardian_last_name" name="last_name" required
                   class="w-full px-4 py-3 bg-gray-50 border rounded-xl">
          </div>

          <div class="md:col-span-2">
            <label class="block text-xs font-black text-gray-400 uppercase mb-2">
              Email Address <span class="text-orange-500 text-[10px]">(leave blank to keep current)</span>
            </label>
            <input type="email" id="edit_guardian_email" name="email" placeholder=""
                   class="w-full px-4 py-3 bg-gray-50 border rounded-xl">
          </div>

          <div class="md:col-span-2">
            <label class="block text-xs font-black text-gray-400 uppercase mb-2">
                Password <span class="text-orange-500 text-[10px]">(leave blank to keep current)</span>
            </label>
            <input type="password" id="edit_guardian_password" name="password"
                    class="w-full px-4 py-3 bg-gray-50 border rounded-xl">
            </div>

            <div class="md:col-span-2">
            <label class="block text-xs font-black text-gray-400 uppercase mb-2">
                Confirm Password
            </label>
            <input type="password" id="edit_guardian_password_confirmation" name="password_confirmation"
                    class="w-full px-4 py-3 bg-gray-50 border rounded-xl">
            </div>

          <div>
            <label class="block text-xs font-black text-gray-400 uppercase mb-2">Contact Number</label>
            <input type="tel" id="edit_guardian_contact" name="contact_number" maxlength="11"
                   placeholder="09xxxxxxxxx" pattern="^09\d{9}$"
                   class="w-full px-4 py-3 bg-gray-50 border rounded-xl">
          </div>

          <div class="md:col-span-2">
            <label class="block text-xs font-black text-gray-400 uppercase mb-2">Residential Address</label>
            <input type="text" id="edit_guardian_address" name="address"
                   class="w-full px-4 py-3 bg-gray-50 border rounded-xl">
          </div>

          <div class="md:col-span-2">
            <label class="block text-xs font-black text-gray-400 uppercase mb-2">Relationship to Child</label>
            <input type="text" id="edit_guardian_relationship" name="relationship_to_child" required
                   class="w-full px-4 py-3 bg-gray-50 border rounded-xl">
          </div>
        </div>

        <div class="flex justify-end gap-4 mt-8 border-t pt-6">
          <button type="button" onclick="closeEditGuardianModal()"
                  class="px-6 py-3 rounded-xl text-gray-500 font-bold hover:bg-gray-50">Cancel</button>
          <button type="submit"
                  class="bg-orange-600 text-white px-10 py-3 rounded-xl font-bold shadow-xl hover:bg-orange-700">
            Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div id="addStudentModal" class="hidden fixed inset-0 z-[100] overflow-hidden">
  <div class="flex items-center justify-center min-h-screen p-4 text-center">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-3xl overflow-hidden">

      <div class="bg-blue-50/50 px-10 py-6 border-b flex justify-between items-center">
        <h2 class="text-2xl font-black text-[#003366]">Add Student</h2>
        <button type="button" onclick="closeAddStudentModal()"
          class="w-10 h-10 flex items-center justify-center rounded-full bg-white shadow-sm hover:bg-red-50 hover:text-red-500">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <form id="enrollStudentForm" method="POST" enctype="multipart/form-data"
            class="p-10 text-left overflow-y-auto max-h-[70vh]">
        @csrf

        <input type="hidden" name="guardian_id" id="guardianIdInput">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <input type="text" name="first_name" placeholder="First Name" required class="w-full px-4 py-3 border rounded-xl">
          <input type="text" name="middle_name" placeholder="Middle Name" class="w-full px-4 py-3 border rounded-xl">
          <input type="text" name="last_name" placeholder="Last Name" required class="w-full px-4 py-3 border rounded-xl">

          <select name="gender" required class="w-full px-4 py-3 border rounded-xl">
            <option value="">Select Gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
          </select>

          <input type="date" name="date_of_birth" required class="w-full px-4 py-3 border rounded-xl">
          <input type="text" name="nationality" placeholder="Nationality" required value="Filipino" class="w-full px-4 py-3 border rounded-xl">
          <input type="text" name="religion" placeholder="Religion" required class="w-full px-4 py-3 border rounded-xl md:col-span-2">

          <input type="file" name="photo" accept="image/*" required class="w-full px-4 py-3 border rounded-xl md:col-span-2">
        </div>

        <div class="flex justify-end gap-4 mt-12 pt-6 border-t">
          <button type="button" onclick="closeAddStudentModal()"
                  class="px-6 py-3 rounded-xl text-gray-500 font-bold hover:bg-gray-50">Discard</button>
          <button type="submit"
                  class="bg-[#003366] text-white px-10 py-3 rounded-xl font-bold shadow-xl hover:bg-blue-900">
            Add Student
          </button>
        </div>
      </form>

    </div>
  </div>
</div>

<div id="trashStudentModal" class="hidden fixed inset-0 z-[100] overflow-hidden">
  <div class="flex items-center justify-center min-h-screen p-4 text-center">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-3xl overflow-hidden">

      <div class="bg-red-50/50 px-10 py-6 border-b flex justify-between items-center">
        <h2 class="text-2xl font-black text-red-600">Trash Linked Students</h2>
        <button type="button" onclick="closeTrashStudentModal()"
                class="w-10 h-10 flex items-center justify-center rounded-full bg-white shadow-sm hover:bg-red-50 hover:text-red-500">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <div class="p-10 text-left max-h-[70vh] overflow-y-auto space-y-6">
        <div>
          <span class="font-medium text-gray-400">Guardian:</span><br>
          <span id="trashGuardianName" class="font-semibold"></span>
        </div>

        <div>
          <span class="font-medium text-gray-400">Linked Students:</span><br>
            <ul id="trashGuardianStudents" class="space-y-3 text-sm">

            </ul>
        </div>
      </div>

      <div class="px-10 py-6 border-t flex justify-end">
        <button type="button" onclick="closeTrashStudentModal()"
                class="px-8 py-3 rounded-xl font-bold text-[#003366] hover:bg-gray-100">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
function closeViewGuardianModal() {
  const modal = document.getElementById('viewGuardianModal');
  if (modal) modal.classList.add('hidden');
}

function openViewGuardianModal(button) {
  const row = button.closest('tr');
  const guardian = JSON.parse(row.dataset.guardian || '{}');

  const fullName = [guardian.first_name, guardian.middle_name, guardian.last_name].filter(Boolean).join(' ');
  document.getElementById('viewGuardianName').textContent = fullName || 'N/A';
  document.getElementById('viewGuardianEmail').textContent = guardian.email || 'N/A';
  document.getElementById('viewGuardianAddress').textContent = guardian.address || 'N/A';
  document.getElementById('viewGuardianRelationship').textContent = guardian.relationship_to_child || 'N/A';
  document.getElementById('viewGuardianCreatedAt').textContent = guardian.created_at || 'N/A';

  const emailStatusEl = document.getElementById('viewGuardianEmailStatus');
  if (emailStatusEl) {
    emailStatusEl.innerHTML = guardian.email_verified_at
      ? `<i class="fas fa-check-circle text-green-500"></i><span class="text-green-500 text-sm font-semibold">Verified</span>`
      : `<i class="fas fa-times-circle text-red-500"></i><span class="text-red-500 text-sm font-semibold">Not Verified</span>`;
  }

  const studentsList = document.getElementById('viewGuardianStudents');
  studentsList.innerHTML = '';
  if (guardian.students && guardian.students.length > 0) {
    guardian.students.forEach(student => {
      const li = document.createElement('li');
      li.className = "flex items-center gap-3";
      if (student.photo_path) {
        li.innerHTML = `<img src="/storage/${student.photo_path}" class="w-10 h-10 object-cover rounded-full border">
                        <span>${student.full_name} (${student.gender})</span>`;
      } else {
        li.innerHTML = `<div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-xs text-gray-500">N/A</div>
                        <span>${student.full_name} (${student.gender})</span>`;
      }
      studentsList.appendChild(li);
    });
  } else {
    studentsList.innerHTML = '<li class="text-gray-400 italic">No students linked</li>';
  }

  document.getElementById('viewGuardianModal').classList.remove('hidden');
}

function closeEditGuardianModal() {
  document.getElementById('editGuardianModal').classList.add('hidden');
  document.getElementById('editGuardianForm').reset();
}

function openEditGuardianModal(button) {
  const row = button.closest('tr');
  const guardian = JSON.parse(row.dataset.guardian || '{}');
  const form = document.getElementById('editGuardianForm');
  if (!form) return;

  form.action = '/guardians/' + guardian.id;
  document.getElementById('edit_guardian_first_name').value = guardian.first_name || '';
  document.getElementById('edit_guardian_middle_name').value = guardian.middle_name || '';
  document.getElementById('edit_guardian_last_name').value = guardian.last_name || '';
  document.getElementById('edit_guardian_contact').value = guardian.contact_number || '';
  document.getElementById('edit_guardian_address').value = guardian.address || '';
  document.getElementById('edit_guardian_relationship').value = guardian.relationship_to_child || '';

  const emailInput = document.getElementById('edit_guardian_email');
  if (emailInput) {
    emailInput.value = '';
    emailInput.placeholder = guardian.email ? `Current: ${guardian.email}` : 'guardian@example.ph';
  }

  document.getElementById('editGuardianModal').classList.remove('hidden');
}

function openAddStudentModal(guardianId) {
  const form = document.getElementById('enrollStudentForm');
  const guardianInput = document.getElementById('guardianIdInput');
  const modal = document.getElementById('addStudentModal');

  if (!form || !guardianInput || !modal) {
    console.error('Add Student modal elements not found in DOM.');
    return;
  }

  guardianInput.value = guardianId;
  form.action = `/guardians/${guardianId}/students`;
  modal.classList.remove('hidden');
}

function closeAddStudentModal() {
  const form = document.getElementById('enrollStudentForm');
  const modal = document.getElementById('addStudentModal');

  if (!form || !modal) {
    console.error('Add Student modal elements not found in DOM.');
    return;
  }

  modal.classList.add('hidden');
  form.reset();
  document.getElementById('guardianIdInput').value = '';
}

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('enrollStudentForm');
  if (!form) return;

  form.addEventListener('submit', async function (e) {
    e.preventDefault();

    const guardianId = document.getElementById('guardianIdInput').value;
    const actionUrl = `/guardians/${guardianId}/students`;
    const formData = new FormData(form);

    try {
      const response = await fetch(actionUrl, {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      const data = await response.json();

      if (data.success) {
        closeAddStudentModal();

        const guardianRow = document.querySelector(`tr[data-id="${guardianId}"]`);
        if (guardianRow) {
          const studentsCell = guardianRow.querySelector('td:nth-child(2) ul');
          if (studentsCell) {
            const newLi = document.createElement('li');
            newLi.classList.add('flex','items-center','gap-3');
            newLi.innerHTML = `
              ${formData.get('photo')
                ? `<img src="${URL.createObjectURL(formData.get('photo'))}" class="w-10 h-10 object-cover rounded-full border">`
                : `<div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-xs text-gray-500">N/A</div>`}
              <span>${formData.get('first_name')} ${formData.get('last_name')} (${formData.get('gender')}, ${formData.get('date_of_birth')})</span>
            `;
            studentsCell.appendChild(newLi);
          }
        }

        alert('Student linked to guardian successfully!');
      } else {
        alert('Failed to add student: ' + (data.message || 'Unknown error'));
      }
    } catch (error) {
      console.error(error);
      alert('An error occurred while adding the student.');
    }
  });
});

function openTrashStudentModal(guardianId, guardianName) {
  document.getElementById('trashGuardianName').textContent = guardianName;
  const row = document.querySelector(`tr[data-id="${guardianId}"]`);
  const guardian = JSON.parse(row.dataset.guardian || '{}');
  const studentsList = document.getElementById('trashGuardianStudents');
  studentsList.innerHTML = '';

  if (guardian.students && guardian.students.length > 0) {
    guardian.students.forEach(student => {
      const li = document.createElement('li');
      li.className = "flex items-center justify-between gap-3";
      li.innerHTML = `<div class="flex items-center gap-3">
                        <span>${student.full_name} (${student.gender}, ${student.date_of_birth.split('T')[0]})</span>
                      </div>
                      <button type="button"
                              class="trash-student-btn p-2.5 rounded-xl border border-red-100 text-red-500 hover:bg-red-500 hover:text-white transition-all active:scale-90"
                              data-id="${student.id}" data-name="${student.full_name}">
                        <i class="fas fa-user-graduate"></i>
                      </button>`;
      studentsList.appendChild(li);
    });
  } else {
    studentsList.innerHTML = '<li class="text-gray-400 italic">No students linked</li>';
  }

  document.getElementById('trashStudentModal').classList.remove('hidden');
}
function closeTrashStudentModal() {
  document.getElementById('trashStudentModal').classList.add('hidden');
}

async function refreshGuardianTable() {
  try {
    const response = await fetch('/guardians', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const html = await response.text();
    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');
    const newTable = doc.querySelector('.overflow-x-auto');
    if (newTable) {
      document.querySelector('.overflow-x-auto').innerHTML = newTable.innerHTML;
    }
  } catch (err) {
    console.error('Failed to refresh guardian table:', err);
  }
}

document.addEventListener('click', async function(e) {
  const btn = e.target.closest('.trash-student-btn');
  if (!btn) return;

  const studentId = btn.dataset.id;
  const studentName = btn.dataset.name;

  if (confirm(`Move ${studentName} to trash?`)) {
    try {
      const response = await fetch(`/students/${studentId}/trash`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json'
        }
      });
      const data = await response.json();

      if (response.ok && data.success) {
        btn.closest('li').remove();
        refreshGuardianTable();
        alert(`${studentName} moved to trash.`);
      } else {
        alert(data.message || `Failed to trash ${studentName}.`);
      }
    } catch (error) {
      console.error(error);
      alert('An error occurred while trashing the student.');
    }
  }
});

function openAddStudentGuardianModal() {
  document.getElementById('addStudentGuardianModal').classList.remove('hidden');
}
function closeAddStudentGuardianModal() {
  const modal = document.getElementById('addStudentGuardianModal');
  modal.classList.add('hidden');
  document.getElementById('addStudentGuardianForm').reset();
}

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('addStudentGuardianForm');
  if (!form) return;

  form.addEventListener('submit', async function(e) {
    e.preventDefault();

    form.querySelector('.error-messages')?.remove();

    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.textContent = "Saving...";
    }

    try {
      const formData = new FormData(form);
      const response = await fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      const data = await response.json();

      if (data.success) {
        closeAddStudentGuardianModal();
        const popup = document.createElement('div');
        popup.className = "fixed inset-0 flex items-center justify-center bg-black/50 z-[200]";
        popup.innerHTML = `
          <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md text-center animate-fadeIn">
            <h2 class="text-xl font-bold text-green-600 mb-4">Guardian Created!</h2>
            <p>${data.message}</p>
            ${data.default_password
              ? `<p class="mt-2 text-sm text-gray-600">Generated Password: <span class="font-mono">${data.default_password}</span></p>`
              : ""}
            <button onclick="this.closest('.fixed').remove()"
                    class="mt-6 px-6 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
              OK
            </button>
          </div>
        `;
        document.body.appendChild(popup);

        refreshGuardianTable();
      } else {
        let errorHtml = `<div class="error-messages bg-red-100 text-red-700 p-4 rounded-xl mt-4">
                           <ul class="list-disc list-inside">`;
        if (data.errors) {
          Object.values(data.errors).forEach(errArr => {
            errArr.forEach(err => {
              errorHtml += `<li>${err}</li>`;
            });
          });
        } else {
          errorHtml += `<li>${data.message || 'An error occurred.'}</li>`;
        }
        errorHtml += `</ul></div>`;
        form.insertAdjacentHTML('beforeend', errorHtml);
      }
    } catch (error) {
      console.error(error);
      alert('An error occurred while saving guardian and student.');
    } finally {
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.textContent = "Save Student + Guardian";
      }
    }
  });
});
</script>
</x-layout>

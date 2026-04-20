{{-- resources/views/students/progress.blade.php (enhanced design only) --}}
<x-layout>
    <x-slot:title>Progress Management | KidWatch</x-slot>

    <div class="space-y-8">
        {{-- Header & Controls --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-4xl font-black text-[#003366] tracking-[-1px] leading-none uppercase italic">Student Progress</h1>
                <p class="text-slate-500 mt-2 font-medium">Track weekly academic performance • Real-time updates</p>
            </div>

            {{-- Week Dropdown + Date Picker + Create Week --}}
            <div class="flex flex-wrap items-center gap-6 bg-white rounded-3xl px-6 py-4 shadow-sm border border-blue-100">
                <div class="flex items-center gap-3">
                    <label for="weekSelect" class="text-xs font-black uppercase tracking-widest text-[#003366]/70">Select Week</label>
                    <select id="weekSelect" class="bg-white border border-blue-200 rounded-2xl px-5 py-3 text-sm font-semibold text-[#003366] shadow-sm focus:ring-4 focus:ring-blue-100 focus:border-blue-400 transition-all">
                        <option value="week1">Week 1</option>
                        <option value="week2">Week 2</option>
                        <option value="week3" selected>Week 3</option>
                        <option value="week4">Week 4</option>
                        <option value="week5">Week 5</option>
                        <option value="week6">Week 6</option>
                    </select>
                </div>

                <div class="flex items-center gap-3">
                    <label for="dateStart" class="text-xs font-black uppercase tracking-widest text-[#003366]/70">Start Date</label>
                    <input type="date" id="dateStart" value="2026-04-13" class="bg-white border border-blue-200 rounded-2xl px-5 py-3 text-sm font-semibold text-[#003366] shadow-sm focus:ring-4 focus:ring-blue-100 focus:border-blue-400 transition-all">
                </div>

                <div class="flex items-center gap-3">
                    <label for="dateEnd" class="text-xs font-black uppercase tracking-widest text-[#003366]/70">End Date</label>
                    <input type="date" id="dateEnd" value="2026-04-17" class="bg-white border border-blue-200 rounded-2xl px-5 py-3 text-sm font-semibold text-[#003366] shadow-sm focus:ring-4 focus:ring-blue-100 focus:border-blue-400 transition-all">
                </div>

                {{-- Create Week Button --}}
                <button class="bg-gradient-to-r from-[#003366] to-blue-600 text-white px-7 py-3 rounded-3xl font-black text-xs uppercase shadow-xl shadow-blue-900/20 hover:shadow-2xl hover:-translate-y-0.5 active:scale-95 transition-all whitespace-nowrap flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Create New Week
                </button>
            </div>
        </div>

        {{-- Main CRUD Table - Week 3 (CURRENT) --}}
        <div class="bg-white rounded-3xl border border-blue-50 shadow-2xl shadow-blue-900/5 overflow-hidden">
            <div class="overflow-x-auto">
                {{-- Week Header --}}
                <div class="bg-gradient-to-r from-[#e3f2fd] to-blue-50 px-8 py-6 border-b border-blue-100 flex items-center justify-between">
                    <div>
                        <div class="inline-flex items-center gap-2 bg-white text-[#003366] text-xs font-black uppercase tracking-widest px-5 py-2 rounded-3xl shadow-sm">
                            <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                            CURRENT WEEK
                        </div>
                        <h2 class="text-xl font-black text-[#003366] mt-2">Week 3 • April 13 - April 17, 2026</h2>
                    </div>
                    <div class="text-right">
                        <span class="text-emerald-600 text-sm font-semibold">5 students • 2 pending</span>
                    </div>
                </div>

                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#e3f2fd]/70">
                            <th class="px-8 py-6 text-xs font-black uppercase tracking-widest text-[#003366]/70 border-b border-blue-100">Student Name</th>
                            <th class="px-6 py-6 text-xs font-black uppercase tracking-widest text-[#003366]/70 border-b border-blue-100 text-center">Mathematics</th>
                            <th class="px-6 py-6 text-xs font-black uppercase tracking-widest text-[#003366]/70 border-b border-blue-100 text-center">Science</th>
                            <th class="px-6 py-6 text-xs font-black uppercase tracking-widest text-[#003366]/70 border-b border-blue-100 text-center">English</th>
                            <th class="px-6 py-6 text-xs font-black uppercase tracking-widest text-[#003366]/70 border-b border-blue-100 text-center">Filipino</th>
                            <th class="px-8 py-6 text-xs font-black uppercase tracking-widest text-[#003366]/70 border-b border-blue-100 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-blue-100">
                        {{-- Row 1 --}}
                        <tr class="hover:bg-blue-50/60 transition-all group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-9 h-9 rounded-2xl bg-[#003366] text-white flex items-center justify-center font-bold text-sm uppercase shadow-inner">JD</div>
                                    <span class="font-extrabold text-[#003366] text-lg">John Dominic</span>
                                </div>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="text-gray-300 font-bold text-sm italic">Pending...</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="text-gray-300 font-bold text-sm italic">Pending...</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="text-gray-300 font-bold text-sm italic">Pending...</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="text-gray-300 font-bold text-sm italic">Pending...</span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <button class="bg-[#003366] hover:bg-blue-700 text-white px-6 py-3 rounded-3xl font-black text-xs uppercase shadow-lg shadow-blue-900/20 transition-all active:scale-95">
                                    Add Marks
                                </button>
                            </td>
                        </tr>

                        {{-- Row 2 (Missing Data State) --}}
                        <tr class="hover:bg-blue-50/60 transition-all group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-9 h-9 rounded-2xl bg-pink-100 text-pink-600 flex items-center justify-center font-bold text-sm uppercase shadow-inner">SS</div>
                                    <span class="font-extrabold text-[#003366] text-lg">Sarah Smith</span>
                                </div>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="text-gray-300 font-bold text-sm italic">Pending...</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="text-gray-300 font-bold text-sm italic">Pending...</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="text-gray-300 font-bold text-sm italic">Pending...</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="text-gray-300 font-bold text-sm italic">Pending...</span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <button class="bg-[#003366] hover:bg-blue-700 text-white px-6 py-3 rounded-3xl font-black text-xs uppercase shadow-lg shadow-blue-900/20 transition-all active:scale-95">
                                    Add Marks
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Week 2 (COMPLETED) --}}
        <div class="bg-white rounded-3xl border border-blue-50 shadow-2xl shadow-blue-900/5 overflow-hidden">
            <div class="overflow-x-auto">
                {{-- Week Header --}}
                <div class="bg-gradient-to-r from-emerald-50 to-blue-50 px-8 py-6 border-b border-blue-100 flex items-center justify-between">
                    <div>
                        <div class="inline-flex items-center gap-2 bg-emerald-100 text-emerald-700 text-xs font-black uppercase tracking-widest px-5 py-2 rounded-3xl">
                            ✓ COMPLETED
                        </div>
                        <h2 class="text-xl font-black text-[#003366] mt-2">Week 2 • April 6 - April 10, 2026</h2>
                    </div>
                </div>

                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#e3f2fd]/70">
                            <th class="px-8 py-6 text-xs font-black uppercase tracking-widest text-[#003366]/70 border-b border-blue-100">Student Name</th>
                            <th class="px-6 py-6 text-xs font-black uppercase tracking-widest text-[#003366]/70 border-b border-blue-100 text-center">Mathematics</th>
                            <th class="px-6 py-6 text-xs font-black uppercase tracking-widest text-[#003366]/70 border-b border-blue-100 text-center">Science</th>
                            <th class="px-6 py-6 text-xs font-black uppercase tracking-widest text-[#003366]/70 border-b border-blue-100 text-center">English</th>
                            <th class="px-6 py-6 text-xs font-black uppercase tracking-widest text-[#003366]/70 border-b border-blue-100 text-center">Filipino</th>
                            <th class="px-8 py-6 text-xs font-black uppercase tracking-widest text-[#003366]/70 border-b border-blue-100 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-blue-100">
                        {{-- Row 1 --}}
                        <tr class="hover:bg-blue-50/60 transition-all group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-9 h-9 rounded-2xl bg-[#003366] text-white flex items-center justify-center font-bold text-sm uppercase shadow-inner">JD</div>
                                    <span class="font-extrabold text-[#003366] text-lg">John Dominic</span>
                                </div>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="bg-emerald-100 text-emerald-700 px-5 py-2 rounded-3xl font-black text-xs uppercase">4 - Excellent</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="bg-blue-100 text-blue-700 px-5 py-2 rounded-3xl font-black text-xs uppercase">3 - Very Good</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="bg-amber-100 text-amber-700 px-5 py-2 rounded-3xl font-black text-xs uppercase">2 - Good</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="bg-emerald-100 text-emerald-700 px-5 py-2 rounded-3xl font-black text-xs uppercase">4 - Excellent</span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex justify-end gap-3">
                                    <button title="Add Progress" class="w-9 h-9 rounded-3xl bg-white text-[#003366] border border-blue-200 hover:bg-[#003366] hover:text-white transition-all flex items-center justify-center shadow-sm">
                                        <i class="fas fa-plus text-sm"></i>
                                    </button>
                                    <button title="View Progress" class="w-9 h-9 rounded-3xl bg-white text-emerald-600 border border-emerald-200 hover:bg-emerald-600 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                        <i class="fas fa-eye text-sm"></i>
                                    </button>
                                    <button title="Edit Progress" class="w-9 h-9 rounded-3xl bg-white text-blue-600 border border-blue-200 hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                        <i class="fas fa-edit text-sm"></i>
                                    </button>
                                    <button title="Delete Progress" class="w-9 h-9 rounded-3xl bg-white text-red-500 border border-red-200 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- Row 2 --}}
                        <tr class="hover:bg-blue-50/60 transition-all group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-9 h-9 rounded-2xl bg-pink-100 text-pink-600 flex items-center justify-center font-bold text-sm uppercase shadow-inner">SS</div>
                                    <span class="font-extrabold text-[#003366] text-lg">Sarah Smith</span>
                                </div>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="text-gray-300 font-bold text-sm italic">Pending...</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="text-gray-300 font-bold text-sm italic">Pending...</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="text-gray-300 font-bold text-sm italic">Pending...</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="text-gray-300 font-bold text-sm italic">Pending...</span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <button class="bg-[#003366] hover:bg-blue-700 text-white px-6 py-3 rounded-3xl font-black text-xs uppercase shadow-lg shadow-blue-900/20 transition-all active:scale-95">
                                    Add Marks
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Week 1 (COMPLETED) --}}
        <div class="bg-white rounded-3xl border border-blue-50 shadow-2xl shadow-blue-900/5 overflow-hidden">
            <div class="overflow-x-auto">
                {{-- Week Header --}}
                <div class="bg-gradient-to-r from-emerald-50 to-blue-50 px-8 py-6 border-b border-blue-100 flex items-center justify-between">
                    <div>
                        <div class="inline-flex items-center gap-2 bg-emerald-100 text-emerald-700 text-xs font-black uppercase tracking-widest px-5 py-2 rounded-3xl">
                            ✓ COMPLETED
                        </div>
                        <h2 class="text-xl font-black text-[#003366] mt-2">Week 1 • March 30 - April 3, 2026</h2>
                    </div>
                </div>

                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#e3f2fd]/70">
                            <th class="px-8 py-6 text-xs font-black uppercase tracking-widest text-[#003366]/70 border-b border-blue-100">Student Name</th>
                            <th class="px-6 py-6 text-xs font-black uppercase tracking-widest text-[#003366]/70 border-b border-blue-100 text-center">Mathematics</th>
                            <th class="px-6 py-6 text-xs font-black uppercase tracking-widest text-[#003366]/70 border-b border-blue-100 text-center">Science</th>
                            <th class="px-6 py-6 text-xs font-black uppercase tracking-widest text-[#003366]/70 border-b border-blue-100 text-center">English</th>
                            <th class="px-6 py-6 text-xs font-black uppercase tracking-widest text-[#003366]/70 border-b border-blue-100 text-center">Filipino</th>
                            <th class="px-8 py-6 text-xs font-black uppercase tracking-widest text-[#003366]/70 border-b border-blue-100 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-blue-100">
                        {{-- Row 1 --}}
                        <tr class="hover:bg-blue-50/60 transition-all group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-9 h-9 rounded-2xl bg-[#003366] text-white flex items-center justify-center font-bold text-sm uppercase shadow-inner">JD</div>
                                    <span class="font-extrabold text-[#003366] text-lg">John Dominic</span>
                                </div>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="bg-emerald-100 text-emerald-700 px-5 py-2 rounded-3xl font-black text-xs uppercase">4 - Excellent</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="bg-blue-100 text-blue-700 px-5 py-2 rounded-3xl font-black text-xs uppercase">3 - Very Good</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="bg-amber-100 text-amber-700 px-5 py-2 rounded-3xl font-black text-xs uppercase">2 - Good</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="bg-emerald-100 text-emerald-700 px-5 py-2 rounded-3xl font-black text-xs uppercase">4 - Excellent</span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex justify-end gap-3">
                                    <button title="Add Progress" class="w-9 h-9 rounded-3xl bg-white text-[#003366] border border-blue-200 hover:bg-[#003366] hover:text-white transition-all flex items-center justify-center shadow-sm">
                                        <i class="fas fa-plus text-sm"></i>
                                    </button>
                                    <button title="View Progress" class="w-9 h-9 rounded-3xl bg-white text-emerald-600 border border-emerald-200 hover:bg-emerald-600 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                        <i class="fas fa-eye text-sm"></i>
                                    </button>
                                    <button title="Edit Progress" class="w-9 h-9 rounded-3xl bg-white text-blue-600 border border-blue-200 hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                        <i class="fas fa-edit text-sm"></i>
                                    </button>
                                    <button title="Delete Progress" class="w-9 h-9 rounded-3xl bg-white text-red-500 border border-red-200 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- Row 2 --}}
                        <tr class="hover:bg-blue-50/60 transition-all group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-9 h-9 rounded-2xl bg-pink-100 text-pink-600 flex items-center justify-center font-bold text-sm uppercase shadow-inner">SS</div>
                                    <span class="font-extrabold text-[#003366] text-lg">Sarah Smith</span>
                                </div>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="bg-emerald-100 text-emerald-700 px-5 py-2 rounded-3xl font-black text-xs uppercase">4 - Excellent</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="bg-blue-100 text-blue-700 px-5 py-2 rounded-3xl font-black text-xs uppercase">3 - Very Good</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="bg-amber-100 text-amber-700 px-5 py-2 rounded-3xl font-black text-xs uppercase">2 - Good</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="bg-emerald-100 text-emerald-700 px-5 py-2 rounded-3xl font-black text-xs uppercase">4 - Excellent</span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex justify-end gap-3">
                                    <button title="Add Progress" class="w-9 h-9 rounded-3xl bg-white text-[#003366] border border-blue-200 hover:bg-[#003366] hover:text-white transition-all flex items-center justify-center shadow-sm">
                                        <i class="fas fa-plus text-sm"></i>
                                    </button>
                                    <button title="View Progress" class="w-9 h-9 rounded-3xl bg-white text-emerald-600 border border-emerald-200 hover:bg-emerald-600 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                        <i class="fas fa-eye text-sm"></i>
                                    </button>
                                    <button title="Edit Progress" class="w-9 h-9 rounded-3xl bg-white text-blue-600 border border-blue-200 hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                        <i class="fas fa-edit text-sm"></i>
                                    </button>
                                    <button title="Delete Progress" class="w-9 h-9 rounded-3xl bg-white text-red-500 border border-red-200 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Footer / Pagination --}}
            <div class="bg-gray-50/70 px-8 py-5 border-t border-blue-100 flex justify-between items-center text-sm">
                <p class="text-xs font-black uppercase text-[#003366]/50 tracking-widest">Showing 1–12 of 12 students</p>
                <div class="flex items-center gap-2">
                    <button class="px-5 py-3 bg-white border border-blue-200 rounded-3xl text-xs font-black text-[#003366] hover:bg-blue-50 transition">← Previous</button>
                    <button class="px-5 py-3 bg-[#003366] text-white rounded-3xl text-xs font-black">1</button>
                    <button class="px-5 py-3 bg-white border border-blue-200 rounded-3xl text-xs font-black text-[#003366] hover:bg-blue-50 transition">2</button>
                    <button class="px-5 py-3 bg-white border border-blue-200 rounded-3xl text-xs font-black text-[#003366] hover:bg-blue-50 transition">Next →</button>
                </div>
            </div>
        </div>
    </div>
</x-layout>

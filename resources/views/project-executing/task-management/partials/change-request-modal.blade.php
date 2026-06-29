<div id="changeRequestModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50">

    <div class="bg-white w-[700px] max-w-full rounded-2xl shadow-lg p-6 relative">

        <!-- CLOSE -->
        <button onclick="closeChangeRequestModal()" class="absolute top-3 right-3 text-slate-500 hover:text-black">
            <i class="fas fa-times"></i>
        </button>

        <!-- TITLE -->
        <h2 class="text-xl font-semibold mb-4">
            Change Request
        </h2>

        <!-- FORM -->
        <div class="space-y-5 text-sm">

            <!-- TASK -->
            <div>
                <p class="text-slate-400">Task</p>
                <p id="cr_task_title" class="font-medium"></p>
            </div>

            <!-- BEFORE vs AFTER -->
            <div class="grid grid-cols-2 gap-4">

                <!-- BEFORE -->
                <div>
                    <p class="text-xs text-slate-400 mb-1">Current (Before)</p>
                    <textarea id="cr_current_state" class="w-full border rounded-md p-2 bg-slate-50" readonly></textarea>
                </div>

                <!-- AFTER -->
                <div>
                    <p class="text-xs text-slate-400 mb-1">Proposed (After) <span class="text-red-500">*</span></p>
                    <textarea id="cr_proposed_state" class="w-full border rounded-md p-2"
                        placeholder="Jelaskan perubahan yang diinginkan..." required></textarea>

                    <p id="error_proposed" class="text-red-500 text-xs hidden mt-1"></p>
                </div>

            </div>

            <!-- REASON -->
            <div>
                <p class="text-xs text-slate-400 mb-1">Reason <span class="text-red-500">*</span></p>
                <textarea id="cr_reason" class="w-full border rounded-md p-2" placeholder="Kenapa perubahan ini diperlukan?" required></textarea>

                <p id="error_reason" class="text-red-500 text-xs hidden mt-1"></p>
            </div>

            <!-- IMPACT -->
            <div>
                <p class="text-xs text-slate-400 mb-1">Impact</p>
                <select id="cr_impact" class="w-full border rounded-md p-2">
                    <option value="low">Low Impact</option>
                    <option value="medium">Medium Impact</option>
                    <option value="high">High Impact</option>
                </select>
            </div>

            <!-- DEADLINE -->
            <div>
                <p class="text-xs text-slate-400 mb-1">
                    Requested Deadline <span class="text-red-500">*</span>
                </p>

                <input type="date" id="cr_deadline" class="w-full border rounded-md p-2" required>

                <p id="error_deadline" class="text-red-500 text-xs hidden mt-1">
                </p>
            </div>

        </div>

        <!-- ACTION -->
        <div class="mt-6 flex justify-end gap-2">
            <button onclick="closeChangeRequestModal()" class="px-4 py-2 text-sm border rounded-md">
                Cancel
            </button>

            <button onclick="submitChangeRequest()" class="px-4 py-2 text-sm bg-blue-500 text-white rounded-md">
                Submit Request
            </button>
        </div>

    </div>
</div>

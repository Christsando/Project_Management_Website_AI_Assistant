<div id="taskDetailModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50">
    <div class="bg-white w-[600px] max-w-full rounded-2xl shadow-lg p-6 relative mx-auto mt-20">

        <!-- CLOSE -->
        <button onclick="closeTaskModal()" class="absolute top-3 right-3 text-slate-500 hover:text-black">
            <i class="fas fa-times"></i>
        </button>

        <!-- TITLE -->
        <h2 class="text-xl font-semibold mb-4">
            Task Detail
        </h2>

        <!-- CONTENT -->
        <div class="space-y-3 text-sm">
            <div>
                <p class="text-slate-400">Task</p>
                <p id="modalTaskTitle" class="font-medium"></p>
            </div>

            <div>
                <p class="text-slate-400">Description</p>
                <p id="modalTaskDesc"></p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-slate-400">Priority</p>
                    <p id="modalTaskPriority"></p>
                </div>

                <div>
                    <p class="text-slate-400">Due Date</p>
                    <p id="modalTaskDue"></p>
                </div>
            </div>

            <div>
                <p class="text-slate-400">Status</p>
                <p id="modalTaskStatus"></p>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t flex justify-end gap-2">
            <button onclick="closeTaskModal()"
                class="px-4 py-2 text-sm rounded-md border text-slate-600 hover:bg-slate-100">
                Close
            </button>

            <button onclick="openChangeRequest()"
                class="px-4 py-2 text-sm rounded-md bg-yellow-500 text-white hover:bg-yellow-600">
                Request Change
            </button>
        </div>
        
    </div>
</div>

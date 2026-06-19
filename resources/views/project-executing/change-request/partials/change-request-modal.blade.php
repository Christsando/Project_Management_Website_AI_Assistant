<div id="changeRequestDetailModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50">
    <div class="bg-white w-[600px] max-w-full rounded-2xl shadow-lg p-6 relative mx-auto mt-20">

        <!-- CLOSE -->
        <button onclick="closeChangeRequestDetailModal()" class="absolute top-3 right-3 text-slate-500 hover:text-black">
            <i class="fas fa-times"></i>
        </button>

        <!-- TITLE -->
        <h2 class="text-xl font-semibold mb-4">
            Detail Change Request
        </h2>

        <!-- CONTENT -->
        <div class="space-y-3 text-sm">
            <div>
                <p class="text-slate-400">Task</p>
                <p id="crTaskTitle" class="font-medium"></p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-slate-400">Before</p>
                    <p id="crOldValue" class="whitespace-pre-line"></p>
                </div>

                <div>
                    <p class="text-slate-400">After</p>
                    <p id="crNewValue" class="whitespace-pre-line"></p>
                </div>
            </div>

            <div>
                <p class="text-slate-400">Reason</p>
                <p id="crReason" class="whitespace-pre-line"></p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-slate-400">Status</p>
                    <p id="crStatus" class="font-medium capitalize"></p>
                </div>

                <div>
                    <p class="text-slate-400">Tanggal</p>
                    <p id="crDate"></p>
                </div>
            </div>

            <div>
                <p class="text-slate-400">Requested By</p>
                <p id="crRequestedBy"></p>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t flex justify-end gap-2">
            <button onclick="closeChangeRequestDetailModal()"
                class="px-4 py-2 text-sm rounded-md border text-slate-600 hover:bg-slate-100">
                Close
            </button>
        </div>

    </div>
</div>
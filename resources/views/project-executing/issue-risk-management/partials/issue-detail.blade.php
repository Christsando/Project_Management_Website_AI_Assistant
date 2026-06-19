<div id="issue-detail-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-xl rounded-2xl shadow-lg p-6 relative">

        <!-- header -->
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Issue Detail</h2>
            <button onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <!-- content -->
        <div class="space-y-3 text-sm">
            <div>
                <span class="text-slate-400">Title</span>
                <p id="detail-title" class="font-medium"></p>
            </div>

            <div>
                <span class="text-slate-400">Description</span>
                <p id="detail-description"></p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-slate-400">Status</span>
                    <p id="detail-status"></p>
                </div>

                <div>
                    <span class="text-slate-400">Priority</span>
                    <p id="detail-priority"></p>
                </div>

                <div>
                    <span class="text-slate-400">Assignee</span>
                    <p id="detail-assignee"></p>
                </div>

                <div>
                    <span class="text-slate-400">Reporter</span>
                    <p id="detail-reporter"></p>
                </div>

                <div>
                    <span class="text-slate-400">Due Date</span>
                    <p id="detail-due"></p>
                </div>
            </div>
        </div>
    </div>
</div>
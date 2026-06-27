<!-- Devotion Modal for Add/Edit -->
<div id="devotionModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white mb-10">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 id="devotionModalTitle" class="text-xl font-bold text-gray-800">Add Devotion Content</h3>
            <button onclick="closeModal('devotionModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form id="devotion-form" method="POST">
            @csrf
            <input type="hidden" id="devotion_id" name="devotion_id">
            <input type="hidden" id="form_method" name="_method" value="POST">

            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" id="devotion_title" name="title" required
                        placeholder="e.g., Walking in Faith"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date <span class="text-red-500">*</span></label>
                    <input type="date" id="devotion_date" name="date" required
                        value="{{ date('Y-m-d') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bible Verse</label>
                    <input type="text" id="devotion_verse" name="bible_verse"
                        placeholder="e.g., John 3:16 - For God so loved the world..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Text (English) <span class="text-red-500">*</span></label>
                    <textarea id="devotion_content" name="content" rows="6" required
                        placeholder="Write the devotion content in English..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Text (Kinyarwanda)</label>
                    <textarea id="devotion_content_rw" name="content_rw" rows="6"
                        placeholder="Andika ibiri mu mutima wawe mu Kinyarwanda..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="devotion_active" name="is_active" value="1" class="w-4 h-4 text-blue-600 rounded" checked>
                    <label class="text-sm text-gray-700">Active</label>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-3 border-t">
                <button type="button" onclick="closeModal('devotionModal')" class="px-4 py-2 border rounded-lg text-sm hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                    Save Devotion
                </button>
            </div>
        </form>
    </div>
</div>
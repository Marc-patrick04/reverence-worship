@extends('layouts.app')

@section('title', 'Announcement Management')
@section('page-title', 'Announcement Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    
 
    <!-- Announcements -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-6">
            <div id="announcements-tab">
                @include('modules.announcements.partials.announcements-tab')
            </div>
        </div>
    </div>
</div>

@include('modules.announcements.partials.create-modal')

<script>
// Global functions that need to be available everywhere
window.openCreateModal = function() {
    console.log('Opening create modal');
    const modal = document.getElementById('createAnnouncementModal');
    if (modal) {
        const form = document.getElementById('createAnnouncementForm');
        if (form) form.reset();
        modal.classList.remove('hidden');
    } else {
        console.error('Create modal not found');
    }
};

window.closeModal = function(modalId) {
    console.log('Closing modal:', modalId);
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
    }
};

window.refreshAnnouncementsList = function() {
    if (typeof window.loadAnnouncements === 'function') {
        window.loadAnnouncements();
    }
};

document.addEventListener('DOMContentLoaded', function() {
    localStorage.removeItem('announcements_active_tab');
});
</script>
@endsection

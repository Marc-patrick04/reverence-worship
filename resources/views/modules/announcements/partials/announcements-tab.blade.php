<div>
    <!-- Email Inbox Header -->
    <div class="bg-white border-b px-3 sm:px-4 py-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3 min-w-0">
            <i class="fas fa-paper-plane text-blue-600 text-xl"></i>
            <h3 class="text-lg sm:text-xl font-semibold text-gray-800">Sent Messages</h3>
            <span class="text-sm text-gray-500" id="messageCount">(0 messages)</span>
        </div>
        <button onclick="window.openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center justify-center gap-2 shadow-sm transition w-full sm:w-auto">
            <i class="fas fa-plus"></i> Compose
        </button>
    </div>
    
    <!-- Email Toolbar -->
    <div class="bg-white border-b px-3 sm:px-4 py-3 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-center gap-3">
            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="rounded border-gray-300">
            <button onclick="refreshMessages()" class="text-gray-500 hover:text-gray-700 transition" title="Refresh">
                <i class="fas fa-sync-alt"></i>
            </button>
            <button onclick="deleteSelected()" class="text-gray-500 hover:text-red-600 transition" title="Delete Selected">
                <i class="fas fa-trash"></i>
            </button>
            <span class="text-xs text-gray-400" id="selectedCount">0 selected</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 w-full lg:w-auto">
            <div class="relative w-full sm:flex-1 lg:w-auto">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="searchInput" placeholder="Search messages..." 
                       class="pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full lg:w-64">
            </div>
            <button onclick="window.applyFilters()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm transition w-full sm:w-auto">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
    
    <!-- Email List -->
    <div id="announcementsList" class="bg-white divide-y divide-gray-100">
        <div class="text-center py-12">
            <i class="fas fa-spinner fa-spin text-3xl text-gray-400 mb-3"></i>
            <p class="text-gray-500">Loading messages...</p>
        </div>
    </div>
</div>

<style>
.email-row:hover .email-actions {
    opacity: 1;
}

.email-actions {
    opacity: 0;
    transition: opacity 0.15s ease;
}

@media (max-width: 640px) {
    .email-actions {
        opacity: 1;
    }
}

.group:hover .group-hover\:opacity-100 {
    opacity: 1;
}

.hover\:bg-gray-50:hover {
    background-color: #f9fafb;
}

.transition {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<script>
// Store recipient data for each announcement
let recipientCache = {};
let roleCache = {};
let isLoading = false;
let loadTimeout = null;

window.loadAnnouncements = function() {
    // Prevent multiple simultaneous loads
    if (isLoading) return;
    isLoading = true;
    
    const search = document.getElementById('searchInput')?.value || '';
    
    // Show loading state
    const container = document.getElementById('announcementsList');
    if (container) {
        container.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-spinner fa-spin text-3xl text-gray-400 mb-3"></i>
                <p class="text-gray-500">Loading messages...</p>
            </div>
        `;
    }
    
    // Use a single API call instead of multiple sequential calls
    fetch(`/announcements/filter?search=${encodeURIComponent(search)}&limit=20`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const announcements = data.announcements || [];
            
            // Load recipients in parallel
            return Promise.all([
                Promise.resolve(announcements),
                window.loadRecipients(announcements)
            ]);
        } else {
            console.error('Failed to load announcements:', data.message);
            window.showEmptyState();
            return Promise.reject(data.message);
        }
    })
    .then(([announcements, recipients]) => {
        window.renderAnnouncementsList(announcements, recipients);
    })
    .catch(error => {
        console.error('Error loading announcements:', error);
        window.showEmptyState();
    })
    .finally(() => {
        isLoading = false;
    });
};

window.loadRecipients = function(announcements) {
    return new Promise((resolve) => {
        if (!announcements || announcements.length === 0) {
            resolve({});
            return;
        }
        
        // Only fetch recipients for visible announcements (first 20)
        const limitedAnnouncements = announcements.slice(0, 20);
        const announcementIds = limitedAnnouncements.map(a => a.id);
        
        // First, load roles if needed
        const roleAnnouncements = limitedAnnouncements.filter(a => a.target_type === 'roles');
        let rolePromise = Promise.resolve({});
        
        if (roleAnnouncements.length > 0) {
            let allRoleIds = [];
            roleAnnouncements.forEach(a => {
                try {
                    const roleIds = JSON.parse(a.target_roles || '[]');
                    allRoleIds = allRoleIds.concat(roleIds);
                } catch (e) {
                    console.error('Error parsing target_roles:', e);
                }
            });
            
            allRoleIds = [...new Set(allRoleIds)];
            
            if (allRoleIds.length > 0) {
                rolePromise = fetch(`/announcements/roles/batch`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ role_ids: allRoleIds })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        roleCache = data.roles || {};
                    }
                    return roleCache;
                })
                .catch(error => {
                    console.error('Error loading roles:', error);
                    return {};
                });
            }
        }
        
        // Then load recipients
        let recipientPromise = Promise.resolve({});
        if (announcementIds.length > 0) {
            recipientPromise = rolePromise.then(() => {
                return fetch(`/announcements/recipients/batch`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ announcement_ids: announcementIds })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        recipientCache = data.recipients || {};
                    }
                    return recipientCache;
                })
                .catch(error => {
                    console.error('Error loading recipients:', error);
                    return {};
                });
            });
        }
        
        Promise.all([rolePromise, recipientPromise]).then(([roles, recipients]) => {
            resolve(recipients);
        });
    });
};

window.renderAnnouncementsList = function(announcements, recipients) {
    const container = document.getElementById('announcementsList');
    
    if (!announcements || announcements.length === 0) {
        window.showEmptyState();
        return;
    }
    
    document.getElementById('messageCount').textContent = `(${announcements.length} messages)`;
    
    // Limit to 20 for performance
    const displayAnnouncements = announcements.slice(0, 20);
    
    container.innerHTML = displayAnnouncements.map(announcement => {
        const statusConfig = window.getMessageStatusConfig(announcement.status);
        const recipientInfo = (recipients && recipients[announcement.id]) || { count: 0, preview: [], type: 'loading' };
        const sentDate = announcement.published_at || announcement.created_at;
        const isScheduled = announcement.status === 'scheduled';
        const isDraft = announcement.status === 'draft';
        const emailSent = announcement.email_sent;
        
        // Build recipient display
        let recipientDisplay = '';
        
        if (announcement.target_type === 'all') {
            recipientDisplay = 'All Users';
        } else if (announcement.target_type === 'roles') {
            let roleIds = [];
            try {
                roleIds = JSON.parse(announcement.target_roles || '[]');
            } catch (e) {
                console.error('Error parsing target_roles:', e);
            }
            
            const roleNames = roleIds.map(id => {
                const role = roleCache[id];
                return role ? role.display_name || role.name : 'Unknown Role';
            });
            
            recipientDisplay = roleNames.length > 0 ? roleNames.join(', ') : 'Roles';
        } else if (announcement.target_type === 'users') {
            if (recipientInfo.preview && recipientInfo.preview.length > 0) {
                const names = recipientInfo.preview.slice(0, 2).map(r => r.name);
                const remainingCount = recipientInfo.count - 2;
                recipientDisplay = names.join(', ');
                if (remainingCount > 0) {
                    recipientDisplay += ` +${remainingCount} more`;
                }
            } else {
                recipientDisplay = `${recipientInfo.count || 0} recipients`;
            }
        }
        
        // Show icon based on status
        let icon = 'fa-paper-plane';
        let iconColor = 'text-blue-600';
        if (isDraft) {
            icon = 'fa-pen';
            iconColor = 'text-gray-400';
        } else if (isScheduled) {
            icon = 'fa-clock';
            iconColor = 'text-yellow-500';
        } else if (!emailSent && announcement.status === 'active') {
            icon = 'fa-spinner';
            iconColor = 'text-yellow-500';
        } else if (emailSent) {
            icon = 'fa-check-circle';
            iconColor = 'text-green-500';
        }
        
        const timeStr = window.formatTime(sentDate);
        const previewText = window.truncateText(announcement.content, 100);
        
        return `
            <div class="email-row hover:bg-gray-50 transition cursor-pointer group" onclick="window.viewMessage(${announcement.id})">
                <div class="flex items-start gap-3 py-3 px-3 sm:px-4 sm:items-center">
                    <div class="pt-1 sm:pt-0 sm:w-8 text-center flex-shrink-0">
                        <input type="checkbox" class="message-checkbox rounded border-gray-300" 
                               data-id="${announcement.id}" onclick="event.stopPropagation()" onchange="updateSelectedCount()">
                    </div>

                    <div class="pt-1 sm:pt-0 w-6 sm:w-8 text-center flex-shrink-0">
                        <i class="fas ${icon} ${iconColor}"></i>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-col gap-1 lg:flex-row lg:items-center lg:gap-4">
                            <div class="truncate text-xs sm:text-sm font-medium text-gray-700 lg:w-48 lg:flex-shrink-0" title="${window.escapeHtml(recipientDisplay)}">
                                ${isDraft ? '<span class="text-gray-400">[Draft]</span> ' : ''}
                                ${announcement.target_type === 'all' ? 'To: All Users' :
                                  announcement.target_type === 'roles' ? `To: ${window.escapeHtml(recipientDisplay)}` :
                                  `To: ${window.escapeHtml(recipientDisplay)}`}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="truncate font-medium text-gray-900 ${isDraft ? 'text-gray-400' : ''}">
                                    ${window.escapeHtml(announcement.title)}
                                </div>
                                <div class="line-clamp-2 text-sm text-gray-500 sm:line-clamp-1">
                                    ${window.escapeHtml(previewText)}
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            ${isScheduled ? `
                                <span class="text-xs text-yellow-600 bg-yellow-50 px-2 py-0.5 rounded-full">
                                    Scheduled
                                </span>
                            ` : isDraft ? `
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">
                                    Draft
                                </span>
                            ` : !emailSent && announcement.status === 'active' ? `
                                <span class="text-xs text-yellow-600 bg-yellow-50 px-2 py-0.5 rounded-full">
                                    Sending...
                                </span>
                            ` : ''}
                            <span class="text-xs text-gray-500">${timeStr}</span>
                            <span class="ml-auto flex items-center gap-1 sm:ml-0">
                                <button onclick="event.stopPropagation(); window.resendAnnouncement(${announcement.id})"
                                        class="rounded-lg border border-gray-100 bg-white px-2 py-1 text-gray-500 hover:text-green-600 transition" title="Resend">
                                    <i class="fas fa-paper-plane text-sm"></i>
                                </button>
                                <button onclick="event.stopPropagation(); window.deleteAnnouncement(${announcement.id})"
                                        class="rounded-lg border border-red-100 bg-red-50 px-2 py-1 text-red-500 hover:text-red-600 transition" title="Delete">
                                    <i class="fas fa-trash-alt text-sm"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
};

// Toggle select all
function toggleSelectAll() {
    const checked = document.getElementById('selectAll').checked;
    document.querySelectorAll('.message-checkbox').forEach(cb => {
        cb.checked = checked;
    });
    updateSelectedCount();
}

function updateSelectedCount() {
    const selected = document.querySelectorAll('.message-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = selected + ' selected';
}

// Delete selected messages
function deleteSelected() {
    const selected = document.querySelectorAll('.message-checkbox:checked');
    if (selected.length === 0) {
        appAlert('Please select messages to delete');
        return;
    }
    
    appConfirm(`Delete ${selected.length} selected message(s)?`).then((confirmed) => {
        if (!confirmed) {
            return;
        }

        let deleted = 0;
        let promises = [];
        
        selected.forEach(cb => {
            const id = cb.dataset.id;
            promises.push(
                fetch(`/announcements/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) deleted++;
                })
            );
        });
        
        Promise.all(promises).then(() => {
            appAlert(`${deleted} message(s) deleted successfully`);
            window.loadAnnouncements();
            if (typeof window.refreshOverviewStats === 'function') {
                window.refreshOverviewStats();
            }
        });
    });
}

function refreshMessages() {
    window.loadAnnouncements();
}

// Status config function
window.getMessageStatusConfig = function(status) {
    const configs = {
        active: {
            dotColor: 'bg-green-500',
            badgeClass: 'bg-green-100 text-green-700',
            icon: 'fa-paper-plane',
            label: 'Sent'
        },
        scheduled: {
            dotColor: 'bg-yellow-500',
            badgeClass: 'bg-yellow-100 text-yellow-700',
            icon: 'fa-clock',
            label: 'Scheduled'
        },
        draft: {
            dotColor: 'bg-gray-400',
            badgeClass: 'bg-gray-100 text-gray-600',
            icon: 'fa-pen',
            label: 'Draft'
        },
        expired: {
            dotColor: 'bg-red-500',
            badgeClass: 'bg-red-100 text-red-700',
            icon: 'fa-calendar-times',
            label: 'Expired'
        }
    };
    return configs[status] || configs.draft;
};

window.viewMessage = function(id) {
    window.openViewMessageModal(id);
};

window.resendAnnouncement = function(id) {
    appConfirm('Resend this announcement to all recipients?').then((confirmed) => {
        if (!confirmed) {
            return;
        }

        fetch(`/announcements/${id}/resend`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                appAlert(data.message || 'Message resent successfully!');
                window.loadAnnouncements();
            } else {
                appAlert('Error: ' + (data.message || 'Failed to resend'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            appAlert('Error resending announcement');
        });
    });
};

window.deleteAnnouncement = function(id) {
    appConfirm('Are you sure you want to delete this announcement?').then((confirmed) => {
        if (!confirmed) {
            return;
        }

        fetch(`/announcements/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.loadAnnouncements();
                if (typeof window.refreshOverviewStats === 'function') {
                    window.refreshOverviewStats();
                }
                appAlert('Announcement deleted successfully');
            } else {
                appAlert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            appAlert('Error deleting announcement');
        });
    });
};

window.openViewMessageModal = function(id) {
    fetch(`/announcements/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const a = data.announcement;
                let recipientListHtml = '<div class="text-sm text-gray-600">Loading recipients...</div>';
                
                fetch(`/announcements/${id}/recipients`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(recipientData => {
                    if (recipientData.success && recipientData.recipients) {
                        const recipients = recipientData.recipients;
                        if (recipients.length > 0) {
                            recipientListHtml = `
                                <div class="space-y-1 max-h-40 overflow-y-auto">
                                    ${recipients.map(r => `
                                        <div class="flex flex-col gap-0.5 py-2 border-b last:border-0 sm:flex-row sm:items-center sm:justify-between">
                                            <span class="text-sm font-medium">${window.escapeHtml(r.name)}</span>
                                            <span class="break-all text-xs text-gray-500 sm:break-normal">${window.escapeHtml(r.email)}</span>
                                        </div>
                                    `).join('')}
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Total: ${recipients.length} recipients</p>
                            `;
                        } else {
                            recipientListHtml = '<p class="text-sm text-gray-500">No recipients found</p>';
                        }
                    }
                    
                    const modalHtml = `
                        <div id="viewMessageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center p-3 sm:p-6">
                            <div class="relative mx-auto p-4 sm:p-5 border w-full max-w-3xl max-h-[92vh] overflow-y-auto shadow-xl rounded-xl bg-white">
                                <div class="flex justify-between items-start gap-3 pb-3 border-b">
                                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 leading-snug">${window.escapeHtml(a.title)}</h3>
                                    <button onclick="closeViewMessageModal()" class="text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-times text-xl"></i>
                                    </button>
                                </div>
                                <div class="mt-4 space-y-4">
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                            <div><span class="font-medium">From:</span> ${window.escapeHtml(a.created_by_name || 'System')}</div>
                                            <div><span class="font-medium">Status:</span> ${a.status}</div>
                                            <div><span class="font-medium">Sent:</span> ${window.formatDateTime(a.published_at || a.created_at)}</div>
                                            ${a.scheduled_date ? `<div><span class="font-medium">Scheduled:</span> ${window.formatDate(a.scheduled_date)}</div>` : ''}
                                            ${a.email_sent_at ? `<div><span class="font-medium">Delivered:</span> ${window.formatDateTime(a.email_sent_at)}</div>` : ''}
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <h4 class="font-medium text-gray-800 mb-2">To:</h4>
                                        ${recipientListHtml}
                                    </div>
                                    
                                    <div>
                                        <h4 class="font-medium text-gray-800 mb-2">Subject:</h4>
                                        <p class="text-gray-700">${window.escapeHtml(a.title)}</p>
                                    </div>
                                    
                                    <div>
                                        <h4 class="font-medium text-gray-800 mb-2">Message:</h4>
                                        <div class="bg-gray-50 rounded-lg p-4 text-gray-700 whitespace-pre-wrap">
                                            ${window.escapeHtml(a.content).replace(/\n/g, '<br>')}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 mt-4 pt-3 border-t">
                                    <button onclick="closeViewMessageModal()" class="px-4 py-2 border rounded-lg text-sm hover:bg-gray-50 w-full sm:w-auto">Close</button>
                                    <button onclick="window.resendAnnouncement(${a.id}); closeViewMessageModal();" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 w-full sm:w-auto">
                                        <i class="fas fa-paper-plane mr-1"></i> Resend
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    const existingModal = document.getElementById('viewMessageModal');
                    if (existingModal) existingModal.remove();
                    
                    document.body.insertAdjacentHTML('beforeend', modalHtml);
                    document.getElementById('viewMessageModal').classList.remove('hidden');
                });
            }
        });
};

window.closeViewMessageModal = function() {
    const modal = document.getElementById('viewMessageModal');
    if (modal) modal.remove();
};

window.showEmptyState = function() {
    const container = document.getElementById('announcementsList');
    document.getElementById('messageCount').textContent = '(0 messages)';
    container.innerHTML = `
        <div class="text-center py-16 bg-gray-50">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-envelope-open-text text-3xl text-gray-400"></i>
            </div>
            <h4 class="text-lg font-medium text-gray-700 mb-2">No messages yet</h4>
            <p class="text-gray-500 text-sm mb-4">Create your first announcement to reach your audience</p>
            <button onclick="window.openCreateModal()" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition w-full sm:w-auto">
                <i class="fas fa-plus"></i> Compose Message
            </button>
        </div>
    `;
};

window.applyFilters = function() {
    window.loadAnnouncements();
};

window.resetFilters = function() {
    document.getElementById('searchInput').value = '';
    window.loadAnnouncements();
};

window.formatDate = function(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    const now = new Date();
    const diffDays = Math.floor((now - date) / (1000 * 60 * 60 * 24));
    
    if (diffDays === 0) {
        return 'Today';
    } else if (diffDays === 1) {
        return 'Yesterday';
    } else if (diffDays < 7) {
        return `${diffDays} days ago`;
    } else {
        return date.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' });
    }
};

window.formatTime = function(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    const now = new Date();
    const diffDays = Math.floor((now - date) / (1000 * 60 * 60 * 24));
    
    if (diffDays === 0) {
        return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    } else if (diffDays < 7) {
        return ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'][date.getDay()];
    } else {
        return date.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit' });
    }
};

window.formatDateTime = function(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', { 
        day: '2-digit', 
        month: '2-digit', 
        year: 'numeric', 
        hour: '2-digit', 
        minute: '2-digit' 
    });
};

window.truncateText = function(text, length) {
    if (!text) return '';
    if (text.length <= length) return text;
    return text.substring(0, length) + '...';
};

window.escapeHtml = function(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
};

// Set up event listeners when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.loadAnnouncements();
    
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        let timeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => window.applyFilters(), 300);
        });
    }
});

window.refreshAnnouncementsList = function() {
    window.loadAnnouncements();
};
</script>

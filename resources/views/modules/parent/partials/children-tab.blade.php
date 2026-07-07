<div>
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 mb-4">
        <h2 class="text-base sm:text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-child text-blue-600"></i>
            My Children ({{ count($children) }})
        </h2>
        @if(isset($familyName) && $familyName)
        <span class="text-sm bg-blue-50 text-blue-600 px-3 py-1 rounded-full w-fit">
            <i class="fas fa-users mr-1"></i> {{ $familyName }}
        </span>
        @endif
    </div>

    @if(count($children) > 0)
    <!-- Search -->
    <div class="mb-4">
        <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" id="searchChildren" placeholder="Search children by name, email, phone or location..." 
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
    </div>

    <!-- Children List -->
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" id="childrenTable">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">CHILD</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">EMAIL</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">PHONE</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">LOCATION</th>
                    
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="childrenTableBody">
                @foreach($children as $child)
                <tr class="hover:bg-gray-50 transition child-row">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-bold text-xs">
                                {{ strtoupper(substr($child->name, 0, 2)) }}
                            </div>
                            <span class="font-medium text-gray-800">{{ $child->name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $child->email ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $child->phone ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $child->location ?? 'N/A' }}</td>
                    
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="md:hidden space-y-3" id="childrenMobileList">
        @foreach($children as $child)
        <div class="child-mobile-card bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-xs shrink-0">
                    {{ strtoupper(substr($child->name, 0, 2)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-gray-900 break-words">{{ $child->name }}</p>
                    <div class="mt-3 space-y-2 text-sm text-gray-600">
                        <p class="flex items-start gap-2">
                            <i class="fas fa-envelope text-gray-400 mt-1 w-4"></i>
                            <span class="break-all">{{ $child->email ?? 'N/A' }}</span>
                        </p>
                        <p class="flex items-start gap-2">
                            <i class="fas fa-phone text-gray-400 mt-1 w-4"></i>
                            <span>{{ $child->phone ?? 'N/A' }}</span>
                        </p>
                        <p class="flex items-start gap-2">
                            <i class="fas fa-location-dot text-gray-400 mt-1 w-4"></i>
                            <span>{{ $child->location ?? 'N/A' }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-12">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-user-friends text-4xl text-gray-400"></i>
        </div>
        <p class="text-gray-500 text-lg">No children linked to your account</p>
        <p class="text-sm text-gray-400 mt-1">Ask an admin to add children to your family</p>
    </div>
    @endif
</div>

<script>
// Search children
document.getElementById('searchChildren')?.addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#childrenTableBody .child-row');
    const cards = document.querySelectorAll('#childrenMobileList .child-mobile-card');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });

    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});
</script>

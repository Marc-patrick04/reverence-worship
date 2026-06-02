@if($todayDevotion)
<div class="mb-8">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-sun text-yellow-500 mr-2"></i>
            Today's Devotion
        </h2>
        <span class="text-sm text-gray-500">{{ date('l, F j, Y') }}</span>
    </div>
    
    <div class="border-l-4 border-blue-500 pl-4 mb-6">
        <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $todayDevotion->title }}</h3>
        @if(isset($todayDevotion->bible_verse) && $todayDevotion->bible_verse)
            <p class="text-blue-600 italic">"{{ $todayDevotion->bible_verse }}"</p>
        @endif
    </div>
    
    <div class="prose max-w-none mb-6">
        <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $todayDevotion->content }}</p>
    </div>
    
    @if($hasCompletedToday)
        <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg text-center flex items-center justify-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span>You've completed today's devotion</span>
        </div>
    @else
        <form id="devotion-complete-form" method="POST" action="{{ route('intercession.devotion.complete', $todayDevotion->id) }}">
            @csrf
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-medium transition flex items-center justify-center gap-2">
                <i class="fas fa-check"></i>
                Mark as Read & Completed
            </button>
        </form>
    @endif
</div>
@else
<div class="text-center py-8">
    <i class="fas fa-praying-hands text-5xl text-gray-300 mb-3"></i>
    <p class="text-gray-500">No devotion for today. Check back later.</p>
</div>
@endif

@if(isset($recentDevotions) && count($recentDevotions) > 0)
<div class="mt-8 pt-6 border-t">
    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-history text-gray-500 mr-2"></i>
        Recent Devotions
    </h2>
    
    <div class="space-y-3">
        @foreach($recentDevotions as $devotion)
        <div class="border rounded-lg p-4 hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="font-semibold text-gray-800">{{ $devotion->title }}</h3>
                        @if(isset($devotion->completed_by_user) && $devotion->completed_by_user)
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                                <i class="fas fa-check-circle"></i> Read
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mb-2">
                        <i class="fas fa-calendar mr-1"></i> 
                        {{ date('l, F j, Y', strtotime($devotion->date)) }}
                    </p>
                    @if(isset($devotion->bible_verse) && $devotion->bible_verse)
                        <p class="text-sm text-blue-600 italic">{{ \Illuminate\Support\Str::limit($devotion->bible_verse, 100) }}</p>
                    @endif
                    <p class="text-sm text-gray-600 mt-2">{{ \Illuminate\Support\Str::limit($devotion->content, 150) }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
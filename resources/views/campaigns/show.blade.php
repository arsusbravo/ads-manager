<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $campaign->name }}</h2>
            <a href="{{ route('campaigns.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 rounded p-3 text-sm">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-lg shadow p-6">
                <div class="grid sm:grid-cols-3 gap-4 mb-5">
                    <div><p class="text-xs text-gray-400 uppercase">Channel</p><p class="font-medium text-gray-800 mt-0.5">{{ $campaign->channelIntegration->name }}</p></div>
                    <div><p class="text-xs text-gray-400 uppercase">Budget</p><p class="font-medium text-gray-800 mt-0.5">{{ $campaign->budget ? '€'.$campaign->budget.'/day' : '—' }}</p></div>
                    <div><p class="text-xs text-gray-400 uppercase">Status</p>
                        <span class="mt-0.5 inline-block px-2 py-0.5 rounded text-xs font-medium
                            {{ match($campaign->status) { 'active' => 'bg-green-100 text-green-700', 'draft' => 'bg-gray-100 text-gray-600', 'paused' => 'bg-yellow-100 text-yellow-700', default => 'bg-gray-100 text-gray-500' } }}">
                            {{ $campaign->status }}
                        </span>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2 pt-4 border-t">
                    <form method="POST" action="{{ route('campaigns.generate-content', $campaign) }}">
                        @csrf
                        <button class="px-4 py-2 bg-purple-600 text-white text-sm rounded hover:bg-purple-700">
                            Generate AI Ad Content
                        </button>
                    </form>
                    <form method="POST" action="{{ route('campaigns.push', $campaign) }}">
                        @csrf
                        <button class="px-4 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                            Push to {{ str_replace('_', ' ', $campaign->channelIntegration->channel_type) }}
                        </button>
                    </form>
                    <a href="{{ route('campaigns.edit', $campaign) }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded hover:bg-gray-200">Edit</a>
                    <form method="POST" action="{{ route('campaigns.destroy', $campaign) }}" onsubmit="return confirm('Delete this campaign?')">
                        @csrf @method('DELETE')
                        <button class="px-4 py-2 bg-red-50 text-red-600 text-sm rounded hover:bg-red-100">Delete</button>
                    </form>
                </div>
            </div>

            {{-- AI generated content --}}
            @if(!empty($campaign->ai_content))
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b"><h3 class="font-semibold text-gray-700">AI Generated Ad Copy</h3></div>
                    <div class="divide-y divide-gray-100">
                        @foreach($campaign->ai_content as $item)
                            <div class="px-6 py-4">
                                <p class="text-xs font-medium text-gray-400 uppercase mb-1">{{ $item['product_title'] }}</p>
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $item['ad_copy'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-6 text-sm text-gray-400 text-center">
                    No AI content generated yet. Click "Generate AI Ad Content" above.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

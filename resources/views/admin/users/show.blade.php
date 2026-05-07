<x-admin-layout :title="$user->name">
    <div class="max-w-2xl space-y-4">
        <div class="bg-white rounded shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="font-semibold text-gray-800 text-lg">{{ $user->name }}</p>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $user->role === 'admin' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                    {{ $user->role }}
                </span>
            </div>
            <div class="text-sm text-gray-400">Joined {{ $user->created_at->format('d M Y') }}</div>
            <div class="mt-4 flex gap-2">
                <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">Edit</a>
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user?')">
                    @csrf @method('DELETE')
                    <button class="px-4 py-2 bg-red-50 text-red-600 text-sm rounded hover:bg-red-100">Delete</button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded shadow p-5">
            <h3 class="font-semibold text-gray-700 mb-3">Stores ({{ $user->stores->count() }})</h3>
            @forelse($user->stores as $store)
                <p class="text-sm text-gray-600 py-1 border-b last:border-0">{{ $store->name }}</p>
            @empty
                <p class="text-sm text-gray-400">No stores.</p>
            @endforelse
        </div>

        <div class="bg-white rounded shadow p-5">
            <h3 class="font-semibold text-gray-700 mb-3">Channels ({{ $user->channelIntegrations->count() }})</h3>
            @forelse($user->channelIntegrations as $ch)
                <p class="text-sm text-gray-600 py-1 border-b last:border-0">{{ $ch->name }} <span class="text-gray-400">({{ $ch->channel_type }})</span></p>
            @empty
                <p class="text-sm text-gray-400">No channels.</p>
            @endforelse
        </div>
    </div>
</x-admin-layout>

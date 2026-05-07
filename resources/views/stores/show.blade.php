<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $store->name }}</h2>
            <a href="{{ route('stores.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 rounded p-3 text-sm">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="text-sm text-gray-400">Platform: <span class="text-gray-700 capitalize">{{ str_replace('_', ' ', $store->channelIntegration->channel_type) }}</span></div>
                        @if($store->url)
                            <div class="text-sm text-gray-400">URL: <a href="{{ $store->url }}" target="_blank" class="text-indigo-600 hover:underline">{{ $store->url }}</a></div>
                        @endif
                        <div class="text-sm text-gray-400">
                            Last synced: {{ $store->last_synced_at ? $store->last_synced_at->diffForHumans() : 'Never' }}
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('stores.sync', $store) }}">
                            @csrf
                            <button class="px-4 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                Sync Products Now
                            </button>
                        </form>
                        <a href="{{ route('stores.edit', $store) }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded hover:bg-gray-200">Edit</a>
                        <form method="POST" action="{{ route('stores.destroy', $store) }}" onsubmit="return confirm('Delete this store and all its products?')">
                            @csrf @method('DELETE')
                            <button class="px-4 py-2 bg-red-50 text-red-600 text-sm rounded hover:bg-red-100">Delete</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b flex items-center justify-between">
                    <h3 class="font-semibold text-gray-700">Products ({{ $store->products->count() }})</h3>
                </div>
                @if($store->products->isEmpty())
                    <p class="p-6 text-sm text-gray-400">No products yet. Click "Sync Products Now" to import.</p>
                @else
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($store->products->take(50) as $product)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        <a href="{{ route('products.show', $product) }}" class="hover:text-indigo-600">{{ $product->title }}</a>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-400 font-mono">{{ $product->sku ?: '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">€{{ $product->price }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $product->stock }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

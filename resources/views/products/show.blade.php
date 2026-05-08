<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $product->title }}</h2>
            <a href="{{ route('products.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="bg-white rounded-lg shadow p-6">
                <div class="grid md:grid-cols-2 gap-6">
                    {{-- Images --}}
                    @if(!empty($product->images))
                        <div class="flex gap-2 flex-wrap">
                            @foreach(array_slice($product->images, 0, 4) as $img)
                                <img src="{{ $img }}" class="w-24 h-24 object-cover rounded border" />
                            @endforeach
                        </div>
                    @endif

                    <div class="space-y-3">
                        <div><span class="text-xs text-gray-400 uppercase">SKU</span><p class="font-mono text-sm">{{ $product->sku ?: '—' }}</p></div>
                        <div><span class="text-xs text-gray-400 uppercase">Price</span><p class="text-lg font-semibold">€{{ $product->price }}</p></div>
                        <div><span class="text-xs text-gray-400 uppercase">Stock</span><p>{{ $product->stock }}</p></div>
                        <div><span class="text-xs text-gray-400 uppercase">Store</span><p>{{ $product->store->name }}</p></div>
                        <div><span class="text-xs text-gray-400 uppercase">Status</span>
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">{{ $product->status }}</span>
                        </div>
                    </div>
                </div>

                @if($product->description)
                    <div class="mt-4 border-t pt-4">
                        <p class="text-xs text-gray-400 uppercase mb-1">Description</p>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $product->description }}</p>
                    </div>
                @endif
            </div>

            {{-- Variants --}}
            @if($product->variants->isNotEmpty())
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b"><h3 class="font-semibold text-gray-700">Variants ({{ $product->variants->count() }})</h3></div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Attributes</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($product->variants as $variant)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-mono text-gray-400">{{ $variant->sku ?: '—' }}</td>
                                    @php
                                        $parts = [];
                                        foreach ($variant->attributes ?? [] as $k => $v) {
                                            if (is_array($v)) { foreach ($v as $ak => $av) { $parts[] = "$ak: $av"; } }
                                            else { $parts[] = "$k: $v"; }
                                        }
                                    @endphp
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ implode(', ', $parts) ?: '—' }}</td>
                                    <td class="px-4 py-3 text-sm">€{{ $variant->price }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $variant->stock }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Listings --}}
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b flex items-center justify-between">
                    <h3 class="font-semibold text-gray-700">Marketplace Listings</h3>
                    <a href="{{ route('listings.index') }}" class="text-sm text-indigo-600 hover:underline">Manage listings →</a>
                </div>
                @if($product->listings->isEmpty())
                    <p class="p-6 text-sm text-gray-400">Not listed on any marketplace yet.</p>
                @else
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Channel</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last pushed</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($product->listings as $listing)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $listing->channelIntegration->name }}</td>
                                    <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">{{ $listing->status }}</span></td>
                                    <td class="px-4 py-3 text-sm text-gray-400">{{ $listing->last_pushed_at ? $listing->last_pushed_at->diffForHumans() : 'Never' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

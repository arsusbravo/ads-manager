<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Store</h2>
            <a href="{{ route('stores.show', $store) }}" class="text-sm text-gray-500 hover:text-gray-700">← Back</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">
                <form method="POST" action="{{ route('stores.update', $store) }}">
                    @csrf @method('PUT')

                    <div class="mb-5">
                        <x-input-label for="name" value="Store name" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                            value="{{ old('name', $store->name) }}" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <div class="mb-5">
                        <x-input-label for="url" value="Store URL (optional)" />
                        <x-text-input id="url" name="url" type="url" class="mt-1 block w-full"
                            value="{{ old('url', $store->url) }}" />
                        <x-input-error :messages="$errors->get('url')" class="mt-1" />
                    </div>

                    <div class="flex items-center gap-3 pt-2 border-t">
                        <x-primary-button>Save Changes</x-primary-button>
                        <a href="{{ route('stores.show', $store) }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

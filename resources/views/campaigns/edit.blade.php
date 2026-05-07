<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Campaign</h2>
            <a href="{{ route('campaigns.show', $campaign) }}" class="text-sm text-gray-500 hover:text-gray-700">← Back</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">
                <form method="POST" action="{{ route('campaigns.update', $campaign) }}">
                    @csrf @method('PUT')

                    <div class="mb-5">
                        <x-input-label for="name" value="Campaign name" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                            value="{{ old('name', $campaign->name) }}" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <div class="mb-5">
                        <x-input-label for="budget" value="Daily budget (€)" />
                        <x-text-input id="budget" name="budget" type="number" step="0.01" min="0"
                            class="mt-1 block w-full" value="{{ old('budget', $campaign->budget) }}" />
                    </div>

                    <div class="mb-5">
                        <x-input-label for="status" value="Status" />
                        <select id="status" name="status"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach(['draft', 'active', 'paused'] as $s)
                                <option value="{{ $s }}" {{ old('status', $campaign->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-3 pt-2 border-t">
                        <x-primary-button>Save Changes</x-primary-button>
                        <a href="{{ route('campaigns.show', $campaign) }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

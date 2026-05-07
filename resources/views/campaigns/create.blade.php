<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">New Campaign</h2>
            <a href="{{ route('campaigns.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if($channels->isEmpty())
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-5 text-sm text-yellow-800">
                    No active advertising channels found. Please
                    <a href="{{ route('channels.create') }}" class="underline">connect a Google Ads or Facebook Ads channel</a>
                    first.
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-6">
                    <form method="POST" action="{{ route('campaigns.store') }}">
                        @csrf

                        <div class="mb-5">
                            <x-input-label for="channel_integration_id" value="Ad channel" />
                            <select id="channel_integration_id" name="channel_integration_id" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">— Select —</option>
                                @foreach($channels as $channel)
                                    <option value="{{ $channel->id }}" {{ old('channel_integration_id') == $channel->id ? 'selected' : '' }}>
                                        {{ $channel->name }} ({{ str_replace('_', ' ', $channel->channel_type) }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('channel_integration_id')" class="mt-1" />
                        </div>

                        <div class="mb-5">
                            <x-input-label for="name" value="Campaign name" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                value="{{ old('name') }}" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>

                        <div class="mb-5">
                            <x-input-label for="budget" value="Daily budget (€)" />
                            <x-text-input id="budget" name="budget" type="number" step="0.01" min="0"
                                class="mt-1 block w-full" value="{{ old('budget') }}" placeholder="e.g. 25.00" />
                            <x-input-error :messages="$errors->get('budget')" class="mt-1" />
                        </div>

                        <div class="flex items-center gap-3 pt-2 border-t">
                            <x-primary-button>Create Campaign</x-primary-button>
                            <a href="{{ route('campaigns.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

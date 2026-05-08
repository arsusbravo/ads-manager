<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Add Channel</h2>
            <a href="{{ route('channels.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6"
                x-data="{ type: '{{ old('channel_type', '') }}' }">

                <form method="POST" action="{{ route('channels.store') }}">
                    @csrf

                    {{-- Channel type --}}
                    <div class="mb-5">
                        <x-input-label for="channel_type" value="Channel type" />
                        <select id="channel_type" name="channel_type" x-model="type" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">— Select a channel —</option>
                            <optgroup label="Stores (import products)">
                                <option value="woocommerce">WooCommerce</option>
                                <option value="shopify">Shopify</option>
                                <option value="magento">Magento 2</option>
                            </optgroup>
                            <optgroup label="Marketplaces (sell products)">
                                <option value="bol">BOL.com</option>
                                <option value="amazon">Amazon</option>
                            </optgroup>
                            <optgroup label="Advertising">
                                <option value="google_ads">Google Ads</option>
                                <option value="facebook_ads">Facebook Ads</option>
                            </optgroup>
                        </select>
                        <x-input-error :messages="$errors->get('channel_type')" class="mt-1" />
                    </div>

                    {{-- Name --}}
                    <div class="mb-5">
                        <x-input-label for="name" value="Label (e.g. My WooCommerce Store)" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                            value="{{ old('name') }}" required placeholder="My Store" />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    {{-- WooCommerce --}}
                    <div x-show="type === 'woocommerce'" class="space-y-4 mb-5">
                        <p class="text-sm text-gray-500">Generate keys in WooCommerce → Settings → Advanced → REST API.</p>
                        @foreach(['site_url' => 'Store URL (https://yourstore.com)', 'consumer_key' => 'Consumer Key', 'consumer_secret' => 'Consumer Secret'] as $field => $label)
                            <div>
                                <x-input-label :value="$label" />
                                <x-text-input type="{{ str_ends_with($field, 'secret') ? 'password' : 'text' }}"
                                    name="credentials[{{ $field }}]" class="mt-1 block w-full"
                                    value="{{ old('credentials.' . $field) }}" />
                            </div>
                        @endforeach
                    </div>

                    {{-- Shopify --}}
                    <div x-show="type === 'shopify'" class="space-y-4 mb-5">
                        <p class="text-sm text-gray-500">Create an app in the Shopify Dev Dashboard, then add your callback URL to the app's allowed redirect URIs before connecting.</p>
                        <div>
                            <x-input-label value="Shop Domain (mystore.myshopify.com)" />
                            <x-text-input type="text" name="credentials[shop_domain]" class="mt-1 block w-full"
                                value="{{ old('credentials.shop_domain') }}" placeholder="mystore.myshopify.com" />
                        </div>
                        <div>
                            <x-input-label value="Client ID" />
                            <x-text-input type="text" name="credentials[client_id]" class="mt-1 block w-full"
                                value="{{ old('credentials.client_id') }}" />
                        </div>
                        <div>
                            <x-input-label value="Client Secret" />
                            <x-text-input type="password" name="credentials[client_secret]" class="mt-1 block w-full" />
                        </div>
                    </div>

                    {{-- Magento --}}
                    <div x-show="type === 'magento'" class="space-y-4 mb-5">
                        <p class="text-sm text-gray-500">Create an integration in Magento → System → Integrations with Resource Access set to All, then activate it and copy the Access Token.</p>
                        <div>
                            <x-input-label value="Base URL (https://yourstore.com)" />
                            <x-text-input type="text" name="credentials[base_url]" class="mt-1 block w-full"
                                value="{{ old('credentials.base_url') }}" />
                        </div>
                        <div>
                            <x-input-label value="Access Token" />
                            <x-text-input type="password" name="credentials[access_token]" class="mt-1 block w-full"
                                value="{{ old('credentials.access_token') }}" />
                        </div>
                    </div>

                    {{-- BOL.com --}}
                    <div x-show="type === 'bol'" class="space-y-4 mb-5">
                        <p class="text-sm text-gray-500">Find your credentials in the BOL.com Retailer API developer portal.</p>
                        <div>
                            <x-input-label value="Client ID" />
                            <x-text-input type="text" name="credentials[client_id]" class="mt-1 block w-full"
                                value="{{ old('credentials.client_id') }}" />
                        </div>
                        <div>
                            <x-input-label value="Client Secret" />
                            <x-text-input type="password" name="credentials[client_secret]" class="mt-1 block w-full"
                                value="{{ old('credentials.client_secret') }}" />
                        </div>
                    </div>

                    {{-- Amazon --}}
                    <div x-show="type === 'amazon'" class="space-y-4 mb-5">
                        <p class="text-sm text-gray-500">Amazon SP-API credentials from Seller Central → Apps & Services → Develop Apps.</p>
                        <div>
                            <x-input-label value="Client ID (LWA)" />
                            <x-text-input type="text" name="credentials[client_id]" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label value="Client Secret (LWA)" />
                            <x-text-input type="password" name="credentials[client_secret]" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label value="Seller ID" />
                            <x-text-input type="text" name="credentials[seller_id]" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label value="Marketplace Region (e.g. eu-west-1)" />
                            <x-text-input type="text" name="credentials[region]" class="mt-1 block w-full" placeholder="eu-west-1" />
                        </div>
                    </div>

                    {{-- Google Ads --}}
                    <div x-show="type === 'google_ads'" class="space-y-4 mb-5">
                        <p class="text-sm text-gray-500">Google Ads API credentials from Google Cloud Console + your developer token.</p>
                        <div>
                            <x-input-label value="Client ID" />
                            <x-text-input type="text" name="credentials[client_id]" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label value="Client Secret" />
                            <x-text-input type="password" name="credentials[client_secret]" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label value="Developer Token" />
                            <x-text-input type="password" name="credentials[developer_token]" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label value="Customer ID (without dashes)" />
                            <x-text-input type="text" name="credentials[customer_id]" class="mt-1 block w-full" placeholder="1234567890" />
                        </div>
                    </div>

                    {{-- Facebook Ads --}}
                    <div x-show="type === 'facebook_ads'" class="space-y-4 mb-5">
                        <p class="text-sm text-gray-500">Meta Business credentials from developers.facebook.com → your app.</p>
                        <div>
                            <x-input-label value="Access Token" />
                            <x-text-input type="password" name="credentials[access_token]" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label value="Ad Account ID (act_XXXXXXXX)" />
                            <x-text-input type="text" name="credentials[ad_account_id]" class="mt-1 block w-full" placeholder="act_123456789" />
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-2 border-t">
                        <x-primary-button>Save Channel</x-primary-button>
                        <a href="{{ route('channels.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

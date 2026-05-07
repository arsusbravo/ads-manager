<x-admin-layout title="Admin Dashboard">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded shadow p-6">
            <div class="text-sm text-gray-500 uppercase tracking-wide">Total Users</div>
            <div class="text-3xl font-bold mt-1">{{ $totalUsers }}</div>
        </div>
        <div class="bg-white rounded shadow p-6">
            <div class="text-sm text-gray-500 uppercase tracking-wide">Active Stores</div>
            <div class="text-3xl font-bold mt-1">{{ $totalStores }}</div>
        </div>
        <div class="bg-white rounded shadow p-6">
            <div class="text-sm text-gray-500 uppercase tracking-wide">Total Products</div>
            <div class="text-3xl font-bold mt-1">{{ $totalProducts }}</div>
        </div>
    </div>
</x-admin-layout>

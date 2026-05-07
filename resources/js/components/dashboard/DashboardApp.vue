<template>
  <div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
      <div v-for="stat in stats" :key="stat.label" class="bg-white rounded-lg shadow p-5">
        <div class="text-sm text-gray-500 uppercase tracking-wide">{{ stat.label }}</div>
        <div class="text-3xl font-bold mt-1 text-gray-800">{{ stat.value }}</div>
        <a :href="stat.href" class="text-sm text-indigo-600 hover:underline mt-1 inline-block">View all</a>
      </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
      <div class="bg-white rounded-lg shadow p-5">
        <h3 class="font-semibold text-gray-700 mb-3">Quick Actions</h3>
        <div class="space-y-2">
          <a href="/channels/create" class="block px-4 py-2 bg-indigo-50 hover:bg-indigo-100 rounded text-sm text-indigo-700">
            + Connect a channel (store, marketplace or ad platform)
          </a>
          <a href="/stores" class="block px-4 py-2 bg-green-50 hover:bg-green-100 rounded text-sm text-green-700">
            + Add a store &amp; import products
          </a>
          <a href="/campaigns/create" class="block px-4 py-2 bg-purple-50 hover:bg-purple-100 rounded text-sm text-purple-700">
            + Create an ad campaign
          </a>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-5">
        <h3 class="font-semibold text-gray-700 mb-3">Getting Started</h3>
        <ol class="space-y-2 text-sm text-gray-600 list-decimal list-inside">
          <li>Connect your store (WooCommerce, Shopify or Magento)</li>
          <li>Sync products from your store</li>
          <li>Connect a marketplace or ad channel</li>
          <li>Push products to a marketplace listing</li>
          <li>Generate AI ad copy and launch a campaign</li>
        </ol>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DashboardApp',

  data() {
    return {
      stats: [
        { label: 'Stores',           value: '—', href: '/stores' },
        { label: 'Products',         value: '—', href: '/products' },
        { label: 'Active Listings',  value: '—', href: '/listings' },
        { label: 'Active Campaigns', value: '—', href: '/campaigns' },
      ],
    };
  },

  async created() {
    try {
      const data = await window.api('/api/dashboard/stats');
      this.stats[0].value = data.stores;
      this.stats[1].value = data.products;
      this.stats[2].value = data.listings;
      this.stats[3].value = data.campaigns;
    } catch {
      // silently degrade — page still shows from blade
    }
  },
};
</script>

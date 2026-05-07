<template>
  <div>
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-semibold text-gray-700">Your Stores</h3>
      <a href="/stores/create" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded hover:bg-indigo-700">+ Add Store</a>
    </div>

    <div v-if="loading" class="text-center py-12 text-gray-400">Loading…</div>

    <div v-else-if="stores.length === 0" class="bg-white rounded-lg shadow p-8 text-center text-gray-400">
      No stores yet. Connect a WooCommerce, Shopify or Magento channel first, then add a store.
    </div>

    <div v-else class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Platform</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Sync</th>
            <th class="px-6 py-3"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="store in stores" :key="store.id">
            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ store.name }}</td>
            <td class="px-6 py-4 text-sm text-gray-500 capitalize">{{ store.channel_integration?.channel_type }}</td>
            <td class="px-6 py-4">
              <span :class="syncBadge(store.sync_status)" class="px-2 py-1 rounded text-xs font-medium">
                {{ store.sync_status }}
              </span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-400">{{ store.last_synced_at || 'Never' }}</td>
            <td class="px-6 py-4 text-right space-x-3">
              <button @click="sync(store)" :disabled="store.syncing" class="text-sm text-green-600 hover:underline disabled:opacity-50">
                {{ store.syncing ? 'Syncing…' : 'Sync' }}
              </button>
              <a :href="`/stores/${store.id}`" class="text-sm text-indigo-600 hover:underline">View</a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: 'StoresApp',

  data() {
    return {
      stores: [],
      loading: true,
    };
  },

  async created() {
    await this.fetchStores();
  },

  methods: {
    async fetchStores() {
      this.loading = true;
      try {
        const data = await window.api('/api/stores');
        this.stores = data.data.map(s => ({ ...s, syncing: false }));
      } finally {
        this.loading = false;
      }
    },

    async sync(store) {
      store.syncing = true;
      try {
        await window.api(`/stores/${store.id}/sync`, { method: 'POST' });
        store.sync_status = 'syncing';
      } catch (e) {
        alert('Sync failed: ' + e.message);
      } finally {
        store.syncing = false;
      }
    },

    syncBadge(status) {
      return {
        idle:    'bg-gray-100 text-gray-600',
        syncing: 'bg-yellow-100 text-yellow-700',
        error:   'bg-red-100 text-red-700',
      }[status] ?? 'bg-gray-100 text-gray-600';
    },
  },
};
</script>

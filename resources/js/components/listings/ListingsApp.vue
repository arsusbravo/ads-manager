<template>
  <div>
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-semibold text-gray-700">Marketplace Listings</h3>
      <button @click="showForm = !showForm" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded hover:bg-indigo-700">
        + Add Listing
      </button>
    </div>

    <div v-if="message" :class="message.type === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'"
      class="border rounded p-3 text-sm mb-4 flex items-center justify-between">
      <span>{{ message.text }}</span>
      <button @click="message = null" class="ml-4 opacity-60 hover:opacity-100">✕</button>
    </div>

    <!-- Add listing form -->
    <div v-if="showForm" class="bg-white rounded-lg shadow p-5 mb-4">
      <h4 class="font-medium text-gray-700 mb-4">List a product on a marketplace</h4>
      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Product</label>
          <input v-model="productSearch" @input="searchProducts" type="text" placeholder="Search products…"
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm mb-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" />
          <select v-model="form.product_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            <option value="">— Select product —</option>
            <option v-for="p in productOptions" :key="p.id" :value="p.id">{{ p.title }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Marketplace Channel</label>
          <select v-model="form.channel_integration_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            <option value="">— Select channel —</option>
            <option v-for="c in marketplaceChannels" :key="c.id" :value="c.id">{{ c.name }} ({{ c.channel_type }})</option>
          </select>
        </div>
      </div>
      <div class="mt-4 flex gap-2">
        <button @click="createListing" :disabled="!form.product_id || !form.channel_integration_id || saving"
          class="bg-indigo-600 text-white text-sm px-4 py-2 rounded hover:bg-indigo-700 disabled:opacity-40">
          {{ saving ? 'Saving…' : 'Create Listing' }}
        </button>
        <button @click="showForm = false" class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2">Cancel</button>
      </div>
    </div>

    <div v-if="loading" class="text-center py-12 text-gray-400">Loading…</div>

    <div v-else-if="listings.length === 0" class="bg-white rounded-lg shadow p-8 text-center text-gray-400">
      No listings yet. Add a listing to push products to a marketplace.
    </div>

    <div v-else class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Channel</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Pushed</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="listing in listings" :key="listing.id">
            <td class="px-4 py-3 text-sm text-gray-900">{{ listing.product?.title }}</td>
            <td class="px-4 py-3 text-sm text-gray-500 capitalize">{{ listing.channel_integration?.name }}</td>
            <td class="px-4 py-3">
              <span :class="statusBadge(listing.status)" class="px-2 py-1 rounded text-xs font-medium">{{ listing.status }}</span>
            </td>
            <td class="px-4 py-3 text-sm text-gray-400">{{ listing.last_pushed_at || 'Never' }}</td>
            <td class="px-4 py-3 text-right space-x-3">
              <button @click="push(listing)" class="text-sm text-green-600 hover:underline font-medium">Push</button>
              <button @click="remove(listing)" class="text-sm text-red-500 hover:underline">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ListingsApp',

  data() {
    return {
      listings: [],
      loading: true,
      showForm: false,
      saving: false,
      message: null,
      productSearch: '',
      productOptions: [],
      marketplaceChannels: [],
      searchTimer: null,
      form: { product_id: '', channel_integration_id: '' },
    };
  },

  async created() {
    await Promise.all([this.fetchListings(), this.fetchChannels()]);
  },

  methods: {
    async fetchListings() {
      this.loading = true;
      try {
        const data = await window.api('/api/listings');
        this.listings = data.data;
      } finally {
        this.loading = false;
      }
    },

    async fetchChannels() {
      const data = await window.api('/api/channels');
      this.marketplaceChannels = data.filter(c => ['bol', 'amazon'].includes(c.channel_type) && c.status === 'active');
    },

    searchProducts() {
      clearTimeout(this.searchTimer);
      this.searchTimer = setTimeout(async () => {
        if (this.productSearch.length < 2) return;
        const data = await window.api(`/api/products?search=${encodeURIComponent(this.productSearch)}`);
        this.productOptions = data.data;
      }, 350);
    },

    async createListing() {
      this.saving = true;
      try {
        const listing = await window.api('/listings', { body: this.form });
        this.listings.unshift(listing);
        this.showForm = false;
        this.form = { product_id: '', channel_integration_id: '' };
        this.message = { type: 'success', text: 'Listing created.' };
      } catch (e) {
        this.message = { type: 'error', text: e.data?.message || 'Failed to create listing.' };
      } finally {
        this.saving = false;
      }
    },

    async push(listing) {
      try {
        await window.api(`/listings/${listing.id}/push`, { method: 'POST' });
        listing.status = 'pending';
        this.message = { type: 'success', text: 'Push queued.' };
      } catch (e) {
        this.message = { type: 'error', text: 'Push failed.' };
      }
    },

    async remove(listing) {
      if (!confirm('Delete this listing?')) return;
      try {
        await window.api(`/listings/${listing.id}`, { method: 'DELETE' });
        this.listings = this.listings.filter(l => l.id !== listing.id);
      } catch (e) {
        this.message = { type: 'error', text: 'Delete failed.' };
      }
    },

    statusBadge(status) {
      return {
        pending:  'bg-yellow-100 text-yellow-700',
        active:   'bg-green-100 text-green-700',
        error:    'bg-red-100 text-red-700',
        delisted: 'bg-gray-100 text-gray-500',
      }[status] ?? 'bg-gray-100 text-gray-600';
    },
  },
};
</script>

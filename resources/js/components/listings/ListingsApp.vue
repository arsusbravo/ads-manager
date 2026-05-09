<template>
  <div>
    <div v-if="message" :class="message.type === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'"
      class="border rounded p-3 text-sm mb-4 flex items-center justify-between">
      <span>{{ message.text }}</span>
      <button @click="message = null" class="ml-4 opacity-60 hover:opacity-100">✕</button>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">

      <!-- Left: product selector -->
      <div class="lg:col-span-2 bg-white rounded-lg shadow overflow-hidden">
        <div class="px-4 py-3 border-b flex items-center justify-between">
          <h3 class="font-semibold text-gray-700">Select Products</h3>
          <span class="text-sm text-gray-400">{{ selectedProducts.length }} selected</span>
        </div>
        <div class="px-4 py-2 border-b">
          <input v-model="search" @input="debouncedSearch" type="text" placeholder="Search products…"
            class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" />
        </div>
        <div v-if="loadingProducts && products.length === 0" class="text-center py-8 text-gray-400 text-sm">Loading…</div>
        <div v-else-if="products.length === 0" class="text-center py-8 text-gray-400 text-sm">No products found.</div>
        <div v-else>
          <div class="px-4 py-2 border-b bg-gray-50 flex items-center gap-2 text-sm text-gray-500">
            <input type="checkbox" :checked="allSelected" @change="toggleAll" class="rounded" />
            <span>Select all on this page ({{ products.length }})</span>
          </div>
          <div class="max-h-96 overflow-y-auto divide-y divide-gray-100" ref="scrollContainer" @scroll="onScroll">
            <label v-for="product in products" :key="product.id"
              class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 cursor-pointer">
              <input type="checkbox" :value="product.id" v-model="selectedProducts" class="rounded" />
              <img v-if="product.images?.[0]" :src="product.images[0]" class="w-8 h-8 object-cover rounded" />
              <div v-else class="w-8 h-8 bg-gray-100 rounded"></div>
              <div class="flex-1 min-w-0">
                <div class="text-sm font-medium text-gray-900 truncate">{{ product.title }}</div>
                <div class="text-xs text-gray-400">€{{ product.price }} · {{ product.store?.name }}</div>
              </div>
            </label>
            <div v-if="loadingMore" class="text-center py-3 text-gray-400 text-xs">Loading more…</div>
            <div v-else-if="!hasMore && products.length > 0" class="text-center py-3 text-gray-400 text-xs">All {{ products.length }} products loaded</div>
          </div>
        </div>
      </div>

      <!-- Right: channels + action -->
      <div class="space-y-4">
        <div class="bg-white rounded-lg shadow p-4">
          <h3 class="font-semibold text-gray-700 mb-3">Marketplace Channels</h3>
          <div v-if="marketplaceChannels.length === 0" class="text-sm text-gray-400">
            No active marketplace channels. Connect a BOL.com or Amazon channel first.
          </div>
          <div v-else class="space-y-2">
            <label v-for="channel in marketplaceChannels" :key="channel.id"
              class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" :value="channel.id" v-model="selectedChannels" class="rounded" />
              <span class="text-sm text-gray-700">{{ channel.name }}</span>
              <span class="text-xs text-gray-400 capitalize">({{ channel.channel_type }})</span>
            </label>
          </div>
        </div>

        <button @click="pushToMarketplaces" :disabled="!canPush || pushing"
          class="w-full bg-indigo-600 text-white text-sm px-4 py-2.5 rounded hover:bg-indigo-700 disabled:opacity-40 font-medium">
          {{ pushing ? 'Creating listings…' : `Push ${selectedProducts.length} product(s) to ${selectedChannels.length} channel(s)` }}
        </button>

        <p class="text-xs text-gray-400 text-center">
          Creates {{ selectedProducts.length * selectedChannels.length }} listing(s). Existing listings will be skipped.
        </p>
      </div>
    </div>

    <!-- Existing listings -->
    <div class="mt-6 bg-white rounded-lg shadow overflow-hidden">
      <div class="px-4 py-3 border-b flex items-center justify-between">
        <h3 class="font-semibold text-gray-700">Existing Listings</h3>
        <button @click="fetchListings" class="text-xs text-indigo-600 hover:underline">Refresh</button>
      </div>
      <div v-if="loadingListings" class="text-center py-8 text-gray-400 text-sm">Loading…</div>
      <div v-else-if="listings.length === 0" class="text-center py-8 text-gray-400 text-sm">No listings yet.</div>
      <table v-else class="min-w-full divide-y divide-gray-200">
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
            <td class="px-4 py-3 text-sm text-gray-500">{{ listing.channel_integration?.name }}</td>
            <td class="px-4 py-3">
              <span :class="statusBadge(listing.status)" class="px-2 py-1 rounded text-xs font-medium">{{ listing.status }}</span>
            </td>
            <td class="px-4 py-3 text-sm text-gray-400">{{ listing.last_pushed_at || 'Never' }}</td>
            <td class="px-4 py-3 text-right space-x-3">
              <button @click="push(listing)" class="text-sm text-green-600 hover:underline font-medium">Re-push</button>
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
      products: [],
      listings: [],
      marketplaceChannels: [],
      selectedProducts: [],
      selectedChannels: [],
      loadingProducts: true,
      loadingMore: false,
      loadingListings: true,
      pushing: false,
      message: null,
      search: '',
      searchTimer: null,
      currentPage: 1,
      lastPage: 1,
    };
  },

  computed: {
    allSelected() {
      return this.products.length > 0 && this.products.every(p => this.selectedProducts.includes(p.id));
    },
    canPush() {
      return this.selectedProducts.length > 0 && this.selectedChannels.length > 0;
    },
    hasMore() {
      return this.currentPage < this.lastPage;
    },
  },

  async created() {
    await Promise.all([this.fetchProducts(1, true), this.fetchListings(), this.fetchChannels()]);
  },

  methods: {
    async fetchProducts(page = 1, replace = false) {
      if (replace) {
        this.loadingProducts = true;
        this.products = [];
      } else {
        this.loadingMore = true;
      }

      try {
        const params = new URLSearchParams({ page });
        if (this.search) params.set('search', this.search);
        const data = await window.api(`/api/products?${params}`);

        this.products = replace ? data.data : [...this.products, ...data.data];
        this.currentPage = data.current_page;
        this.lastPage = data.last_page;
      } finally {
        this.loadingProducts = false;
        this.loadingMore = false;
      }
    },

    onScroll(e) {
      if (this.loadingMore || !this.hasMore) return;
      const el = e.target;
      if (el.scrollTop + el.clientHeight >= el.scrollHeight - 50) {
        this.fetchProducts(this.currentPage + 1, false);
      }
    },

    debouncedSearch() {
      clearTimeout(this.searchTimer);
      this.searchTimer = setTimeout(() => this.fetchProducts(1, true), 350);
    },

    toggleAll(e) {
      if (e.target.checked) {
        const ids = this.products.map(p => p.id);
        this.selectedProducts = [...new Set([...this.selectedProducts, ...ids])];
      } else {
        const ids = new Set(this.products.map(p => p.id));
        this.selectedProducts = this.selectedProducts.filter(id => !ids.has(id));
      }
    },

    async fetchListings() {
      this.loadingListings = true;
      try {
        const data = await window.api('/api/listings');
        this.listings = data.data;
      } finally {
        this.loadingListings = false;
      }
    },

    async fetchChannels() {
      const data = await window.api('/api/channels');
      this.marketplaceChannels = data.filter(c => ['bol', 'amazon'].includes(c.channel_type) && c.status === 'active');
    },

    async pushToMarketplaces() {
      this.pushing = true;
      this.message = null;
      let created = 0;
      let errors = 0;

      const requests = this.selectedProducts.flatMap(product_id =>
        this.selectedChannels.map(channel_integration_id =>
          window.api('/listings', { body: { product_id, channel_integration_id } })
            .then(() => created++)
            .catch(() => errors++)
        )
      );

      await Promise.allSettled(requests);
      await this.fetchListings();

      this.message = {
        type: errors === 0 ? 'success' : 'error',
        text: `${created} listing(s) created${errors ? `, ${errors} skipped (already exist or error)` : ''}.`,
      };
      this.selectedProducts = [];
      this.pushing = false;
    },

    async push(listing) {
      try {
        await window.api(`/listings/${listing.id}/push`, { method: 'POST' });
        listing.status = 'pending';
        this.message = { type: 'success', text: 'Re-push queued.' };
      } catch {
        this.message = { type: 'error', text: 'Push failed.' };
      }
    },

    async remove(listing) {
      if (!confirm('Delete this listing?')) return;
      try {
        await window.api(`/listings/${listing.id}`, { method: 'DELETE' });
        this.listings = this.listings.filter(l => l.id !== listing.id);
      } catch {
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

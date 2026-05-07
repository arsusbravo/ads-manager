<template>
  <div>
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-semibold text-gray-700">Products</h3>
      <div class="flex gap-2">
        <input v-model="search" @input="debouncedSearch" type="text" placeholder="Search…"
          class="border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" />
        <select v-model="storeFilter" @change="fetchProducts(1)" class="border border-gray-300 rounded px-3 py-1.5 text-sm">
          <option value="">All stores</option>
          <option v-for="s in stores" :key="s.id" :value="s.id">{{ s.name }}</option>
        </select>
      </div>
    </div>

    <div v-if="loading" class="text-center py-12 text-gray-400">Loading…</div>

    <div v-else-if="products.length === 0" class="bg-white rounded-lg shadow p-8 text-center text-gray-400">
      No products yet. Sync a store to import products.
    </div>

    <div v-else class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-16"></th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Store</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="product in products" :key="product.id">
            <td class="px-4 py-3">
              <img v-if="product.images?.[0]" :src="product.images[0]" class="w-10 h-10 object-cover rounded" />
              <div v-else class="w-10 h-10 bg-gray-100 rounded"></div>
            </td>
            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ product.title }}</td>
            <td class="px-4 py-3 text-sm text-gray-400 font-mono">{{ product.sku || '—' }}</td>
            <td class="px-4 py-3 text-sm text-gray-700">€{{ product.price }}</td>
            <td class="px-4 py-3 text-sm text-gray-700">{{ product.stock }}</td>
            <td class="px-4 py-3 text-sm text-gray-400">{{ product.store?.name }}</td>
            <td class="px-4 py-3 text-right">
              <a :href="`/products/${product.id}`" class="text-sm text-indigo-600 hover:underline">View</a>
            </td>
          </tr>
        </tbody>
      </table>

      <div class="px-4 py-3 flex items-center justify-between border-t text-sm text-gray-500">
        <span>{{ meta.total }} products</span>
        <div class="flex gap-2">
          <button @click="fetchProducts(meta.current_page - 1)" :disabled="meta.current_page <= 1"
            class="px-3 py-1 border rounded disabled:opacity-40 hover:bg-gray-50">Prev</button>
          <button @click="fetchProducts(meta.current_page + 1)" :disabled="meta.current_page >= meta.last_page"
            class="px-3 py-1 border rounded disabled:opacity-40 hover:bg-gray-50">Next</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ProductsApp',

  data() {
    return {
      products: [],
      stores: [],
      loading: true,
      search: '',
      storeFilter: '',
      meta: { current_page: 1, last_page: 1, total: 0 },
      searchTimer: null,
    };
  },

  async created() {
    const [, storeData] = await Promise.all([
      this.fetchProducts(1),
      window.api('/api/stores/all'),
    ]);
    this.stores = storeData || [];
  },

  methods: {
    async fetchProducts(page = 1) {
      this.loading = true;
      try {
        const params = new URLSearchParams({ page });
        if (this.search) params.set('search', this.search);
        if (this.storeFilter) params.set('store_id', this.storeFilter);

        const data = await window.api(`/api/products?${params}`);
        this.products = data.data;
        this.meta = data.meta;
      } finally {
        this.loading = false;
      }
    },

    debouncedSearch() {
      clearTimeout(this.searchTimer);
      this.searchTimer = setTimeout(() => this.fetchProducts(1), 350);
    },
  },
};
</script>

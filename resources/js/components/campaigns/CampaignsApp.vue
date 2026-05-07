<template>
  <div>
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-semibold text-gray-700">Ad Campaigns</h3>
      <a href="/campaigns/create" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded hover:bg-indigo-700">+ New Campaign</a>
    </div>

    <div v-if="loading" class="text-center py-12 text-gray-400">Loading…</div>

    <div v-else-if="campaigns.length === 0" class="bg-white rounded-lg shadow p-8 text-center text-gray-400">
      No campaigns yet. Connect an ad channel (Google Ads or Facebook Ads) to get started.
    </div>

    <div v-else class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Campaign</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Channel</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Budget</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">AI Content</th>
            <th class="px-6 py-3"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="c in campaigns" :key="c.id">
            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ c.name }}</td>
            <td class="px-6 py-4 text-sm text-gray-500 capitalize">{{ c.channel_integration?.channel_type?.replace('_', ' ') }}</td>
            <td class="px-6 py-4 text-sm text-gray-700">{{ c.budget ? `€${c.budget}` : '—' }}</td>
            <td class="px-6 py-4">
              <span :class="statusBadge(c.status)" class="px-2 py-1 rounded text-xs font-medium">{{ c.status }}</span>
            </td>
            <td class="px-6 py-4 text-sm">
              <span v-if="c.ai_content?.length" class="text-green-600">{{ c.ai_content.length }} ads generated</span>
              <span v-else class="text-gray-400">Not generated</span>
            </td>
            <td class="px-6 py-4 text-right space-x-3">
              <a :href="`/campaigns/${c.id}`" class="text-sm text-indigo-600 hover:underline">View</a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CampaignsApp',

  data() {
    return {
      campaigns: [],
      loading: true,
    };
  },

  async created() {
    try {
      const data = await window.api('/api/campaigns');
      this.campaigns = data.data;
    } finally {
      this.loading = false;
    }
  },

  methods: {
    statusBadge(status) {
      return {
        draft:  'bg-gray-100 text-gray-600',
        active: 'bg-green-100 text-green-700',
        paused: 'bg-yellow-100 text-yellow-700',
        ended:  'bg-gray-100 text-gray-400',
        error:  'bg-red-100 text-red-700',
      }[status] ?? 'bg-gray-100 text-gray-600';
    },
  },
};
</script>

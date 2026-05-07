<template>
  <div>
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-semibold text-gray-700">Connected Channels</h3>
      <a href="/channels/create" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded hover:bg-indigo-700">+ Add Channel</a>
    </div>

    <div v-if="loading" class="text-center py-12 text-gray-400">Loading…</div>

    <div v-else-if="channels.length === 0" class="bg-white rounded-lg shadow p-8 text-center text-gray-400">
      No channels connected yet. Add a store, marketplace or advertising channel to get started.
    </div>

    <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="ch in channels" :key="ch.id" class="bg-white rounded-lg shadow p-5">
        <div class="flex items-start justify-between">
          <div>
            <div class="font-medium text-gray-800">{{ ch.name }}</div>
            <div class="text-sm text-gray-400 mt-0.5 capitalize">{{ ch.channel_type.replace('_', ' ') }}</div>
          </div>
          <span :class="statusBadge(ch.status)" class="px-2 py-0.5 rounded text-xs font-medium">{{ ch.status }}</span>
        </div>

        <div class="mt-4 flex gap-2">
          <a :href="`/channels/${ch.id}/connect`"
            class="flex-1 text-center text-sm bg-green-50 text-green-700 hover:bg-green-100 px-3 py-1.5 rounded">
            {{ ch.status === 'active' ? 'Re-connect' : 'Connect' }}
          </a>
          <a :href="`/channels/${ch.id}/edit`"
            class="flex-1 text-center text-sm bg-gray-50 text-gray-600 hover:bg-gray-100 px-3 py-1.5 rounded">
            Settings
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ChannelsApp',

  data() {
    return {
      channels: [],
      loading: true,
    };
  },

  async created() {
    try {
      const data = await window.api('/api/channels');
      this.channels = data;
    } finally {
      this.loading = false;
    }
  },

  methods: {
    statusBadge(status) {
      return {
        active:   'bg-green-100 text-green-700',
        inactive: 'bg-gray-100 text-gray-500',
        error:    'bg-red-100 text-red-700',
      }[status] ?? 'bg-gray-100 text-gray-500';
    },
  },
};
</script>

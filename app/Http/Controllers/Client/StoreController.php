<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\ChannelIntegration;
use App\Jobs\ImportProductsFromStoreJob;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $stores = Store::where('user_id', $request->user()->id)
            ->with('channelIntegration')
            ->latest()
            ->paginate(20);

        return view('stores.index', compact('stores'));
    }

    public function create(Request $request)
    {
        $integrations = ChannelIntegration::where('user_id', $request->user()->id)
            ->whereIn('channel_type', ['woocommerce', 'shopify', 'magento'])
            ->where('status', 'active')
            ->get();

        return view('stores.create', compact('integrations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'channel_integration_id' => 'required|exists:channel_integrations,id',
            'name' => 'required|string|max:255',
            'url' => 'nullable|url|max:255',
        ]);

        $integration = ChannelIntegration::where('id', $validated['channel_integration_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $store = Store::create([
            'user_id' => $request->user()->id,
            'channel_integration_id' => $integration->id,
            'name' => $validated['name'],
            'url' => $validated['url'] ?? null,
        ]);

        return redirect()->route('stores.show', $store)->with('success', 'Store created.');
    }

    public function show(Request $request, Store $store)
    {
        abort_if($store->user_id !== $request->user()->id, 403);
        $store->load(['channelIntegration', 'products']);

        return view('stores.show', compact('store'));
    }

    public function edit(Request $request, Store $store)
    {
        abort_if($store->user_id !== $request->user()->id, 403);

        return view('stores.edit', compact('store'));
    }

    public function update(Request $request, Store $store)
    {
        abort_if($store->user_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'nullable|url|max:255',
        ]);

        $store->update($validated);

        return redirect()->route('stores.show', $store)->with('success', 'Store updated.');
    }

    public function destroy(Request $request, Store $store)
    {
        abort_if($store->user_id !== $request->user()->id, 403);
        $store->delete();

        return redirect()->route('stores.index')->with('success', 'Store removed.');
    }

    public function apiIndex(Request $request)
    {
        $stores = Store::where('user_id', $request->user()->id)
            ->with('channelIntegration')
            ->latest()
            ->paginate(20);

        return response()->json($stores);
    }

    public function apiAll(Request $request)
    {
        return response()->json(
            Store::where('user_id', $request->user()->id)->select('id', 'name')->get()
        );
    }

    public function sync(Request $request, Store $store)
    {
        abort_if($store->user_id !== $request->user()->id, 403);

        ImportProductsFromStoreJob::dispatch($store);
        $store->update(['sync_status' => 'syncing']);

        return back()->with('success', 'Sync started. Products will be imported shortly.');
    }
}

<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AdCampaign;
use App\Models\ChannelIntegration;
use App\Jobs\GenerateAdContentJob;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $campaigns = AdCampaign::where('user_id', $request->user()->id)
            ->with('channelIntegration')
            ->latest()
            ->paginate(20);

        return view('campaigns.index', compact('campaigns'));
    }

    public function apiIndex(Request $request)
    {
        return response()->json(
            AdCampaign::where('user_id', $request->user()->id)
                ->with('channelIntegration')
                ->latest()
                ->paginate(20)
        );
    }

    public function create(Request $request)
    {
        $channels = ChannelIntegration::where('user_id', $request->user()->id)
            ->whereIn('channel_type', ['google_ads', 'facebook_ads'])
            ->where('status', 'active')
            ->get();

        return view('campaigns.create', compact('channels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'channel_integration_id' => 'required|exists:channel_integrations,id',
            'name' => 'required|string|max:255',
            'budget' => 'nullable|numeric|min:0',
            'targeting' => 'nullable|array',
        ]);

        $channel = ChannelIntegration::where('id', $validated['channel_integration_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $campaign = AdCampaign::create([
            'user_id' => $request->user()->id,
            'channel_integration_id' => $channel->id,
            'name' => $validated['name'],
            'budget' => $validated['budget'] ?? null,
            'targeting' => $validated['targeting'] ?? null,
            'status' => 'draft',
        ]);

        return redirect()->route('campaigns.show', $campaign)->with('success', 'Campaign created.');
    }

    public function show(Request $request, AdCampaign $campaign)
    {
        abort_if($campaign->user_id !== $request->user()->id, 403);
        $campaign->load('channelIntegration');

        return view('campaigns.show', compact('campaign'));
    }

    public function edit(Request $request, AdCampaign $campaign)
    {
        abort_if($campaign->user_id !== $request->user()->id, 403);

        return view('campaigns.edit', compact('campaign'));
    }

    public function update(Request $request, AdCampaign $campaign)
    {
        abort_if($campaign->user_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'budget' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:draft,active,paused',
        ]);

        $campaign->update($validated);

        return redirect()->route('campaigns.show', $campaign)->with('success', 'Campaign updated.');
    }

    public function destroy(Request $request, AdCampaign $campaign)
    {
        abort_if($campaign->user_id !== $request->user()->id, 403);
        $campaign->delete();

        return redirect()->route('campaigns.index')->with('success', 'Campaign deleted.');
    }

    public function generateContent(Request $request, AdCampaign $campaign)
    {
        abort_if($campaign->user_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'context' => 'nullable|string|max:500',
        ]);

        GenerateAdContentJob::dispatch($campaign, $validated['product_ids'] ?? [], $validated['context'] ?? '');

        return back()->with('success', 'AI content generation queued.');
    }

    public function push(Request $request, AdCampaign $campaign)
    {
        abort_if($campaign->user_id !== $request->user()->id, 403);

        return back()->with('success', 'Campaign push queued.');
    }
}

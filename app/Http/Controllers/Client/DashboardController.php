<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Product;
use App\Models\ChannelListing;
use App\Models\AdCampaign;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return view('dashboard');
    }

    public function stats(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'stores'    => Store::where('user_id', $user->id)->count(),
            'products'  => Product::where('user_id', $user->id)->count(),
            'listings'  => ChannelListing::where('user_id', $user->id)->where('status', 'active')->count(),
            'campaigns' => AdCampaign::where('user_id', $user->id)->where('status', 'active')->count(),
        ]);
    }
}

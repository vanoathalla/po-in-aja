<?php

use App\Livewire\OrderConfigurator;
use App\Livewire\TrackOrder;
use App\Models\Campaign;
use Illuminate\Support\Facades\Route;

// Redirect homepage ke campaign aktif utama
Route::get('/', function () {
    $activeCampaign = Campaign::where('is_active', true)->first();

    if (! $activeCampaign) {
        return view('errors.no-active-campaign');
    }

    return redirect()->route('campaign.show', $activeCampaign->slug);
});

// Halaman Storefront Pre-Order + Alpine.js Back-Jersey Preview
Route::get('/po/{campaign:slug}', OrderConfigurator::class)->name('campaign.show');

// Halaman Pelacakan Progres Produksi Publik & Upload Pelunasan
Route::get('/track', TrackOrder::class)->name('order.track');

<?php

namespace App\Providers;

use App\Models\City;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Inject nama kota & daftar kota ke SEMUA view sekaligus
        // Sehingga header city selector tampil benar di halaman manapun
        View::composer('*', function ($view) {
            static $shared = null;

            if ($shared === null) {
                $slug   = request()->cookie('selected_city', 'cilegon');
                $cities = City::where('is_active', true)->get();
                $city   = $cities->firstWhere('slug', $slug) ?? $cities->first();

                $shared = [
                    'selectedCityName' => $city?->name ?? 'Cilegon',
                    'cities'           => $cities,
                ];
            }

            // Hanya inject jika view belum punya variable ini
            // (hindari override data yang sudah di-pass controller)
            if (! array_key_exists('selectedCityName', $view->getData())) {
                $view->with('selectedCityName', $shared['selectedCityName']);
            }
            if (! array_key_exists('cities', $view->getData())) {
                $view->with('cities', $shared['cities']);
            }
        });
    }
}

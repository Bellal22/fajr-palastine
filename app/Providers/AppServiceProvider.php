<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use App\View\Forms\Components\ColorComponent;
use App\View\Forms\Components\PriceComponent;
use Carbon\Carbon;
use Laraeast\LaravelBootstrapForms\Facades\BsForm;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        BsForm::registerComponent('price', PriceComponent::class);
        BsForm::registerComponent('color', ColorComponent::class);
        Paginator::useBootstrap();
        Carbon::setWeekStartsAt(Carbon::SATURDAY);  // 6
        Carbon::setWeekEndsAt(Carbon::FRIDAY);     // 5

    }
}

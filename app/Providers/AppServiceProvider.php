<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Models\SystemSetting;
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
        Paginator::useBootstrapFive();
        // Lấy dòng cấu hình đầu tiên trong bảng và chia sẻ cho tất cả các file .blade.php
        // Biến $clinicSetting sẽ dùng được ở mọi nơi
        $setting = SystemSetting::first();
        View::share('clinicSetting', $setting);
    }
}

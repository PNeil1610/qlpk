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
        // Lấy cấu hình đang áp dụng (status=1, mới nhất) chia sẻ cho tất cả views
        // Biến $clinicSetting sẽ dùng được ở mọi nơi
        $setting = SystemSetting::active();
        View::share('clinicSetting', $setting);
    }
}

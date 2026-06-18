<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class KeycloakController extends Controller
{
    /**
     * Redirect người dùng đến trang đăng nhập Keycloak.
     */
    public function redirect()
    {
        return Socialite::driver('keycloak')->redirect();
    }

    /**
     * Xử lý callback từ Keycloak sau khi đăng nhập thành công.
     * Tạo/cập nhật User local, lưu roles vào session, đăng nhập.
     */
    public function callback(Request $request)
    {
        try {
            $socialUser = Socialite::driver('keycloak')->user();
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['keycloak' => 'Đăng nhập thất bại. Vui lòng thử lại.']);
        }

        $keycloakSub = $socialUser->getId(); // UUID 'sub' từ Keycloak

        // Lấy roles từ realm_access.roles trong token
        $tokenData  = $socialUser->getRaw();
        $realmRoles = $tokenData['realm_access']['roles'] ?? [];

        // Lọc chỉ lấy các role nghiệp vụ của app (bỏ qua role hệ thống Keycloak)
        $appRoles = ['ADMIN', 'RECEPTIONIST', 'DOCTOR', 'CASHIER', 'PHARMACIST', 'PATIENT'];
        $userRoles = array_values(array_intersect($realmRoles, $appRoles));

        // Tìm user local theo keycloak_sub trước
        $user = User::where('keycloak_sub', $keycloakSub)->first();
        $isRecentlyCreated = false;

        if (!$user) {
            // Nếu không tìm thấy bằng keycloak_sub, thử tìm bằng username (để liên kết tài khoản đã tạo sẵn từ seeder/thủ công)
            $username = $socialUser->getNickname() ?? $keycloakSub;
            $user = User::where('username', $username)->first();

            if ($user) {
                // Liên kết tài khoản hiện có với Keycloak sub UUID
                $user->keycloak_sub = $keycloakSub;
                $user->save();
            } else {
                // Tạo mới nếu hoàn toàn chưa tồn tại
                $user = User::create([
                    'keycloak_sub' => $keycloakSub,
                    'full_name'    => $socialUser->getName() ?? $socialUser->getNickname() ?? 'Người dùng',
                    'email'        => $socialUser->getEmail(),
                    'username'     => $username,
                    'status'       => 1,
                ]);
                $isRecentlyCreated = true;
            }
        }

        // Cập nhật thông tin đăng nhập
        if (!$isRecentlyCreated) {
            $user->full_name      = $socialUser->getName() ?? $user->full_name;
            $user->email          = $socialUser->getEmail() ?? $user->email;
        }
        $user->last_login_at = now();
        $user->save();

        // Lưu roles và token vào session
        session([
            'keycloak_roles'         => $userRoles,
            'keycloak_access_token'  => $socialUser->token,
            'keycloak_refresh_token' => $socialUser->refreshToken,
            'keycloak_token_expires' => now()->addSeconds($socialUser->expiresIn ?? 300),
            'keycloak_sub'           => $keycloakSub,
        ]);

        // Đăng nhập bằng Laravel Auth
        Auth::login($user);

        // Điều hướng dựa trên role
        return $this->redirectByRole($userRoles);
    }

    /**
     * Đăng xuất khỏi cả Laravel lẫn Keycloak (Single Logout).
     */
    public function logout(Request $request)
    {
        // Lấy ID token trước khi xóa session
        $refreshToken = session('keycloak_refresh_token');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Revoke refresh token tại Keycloak (tùy chọn)
        if ($refreshToken) {
            try {
                $baseUrl      = config('services.keycloak.base_url');
                $realm        = config('services.keycloak.realms');
                $clientId     = config('services.keycloak.client_id');
                $clientSecret = config('services.keycloak.client_secret');

                $logoutUrl = "{$baseUrl}/realms/{$realm}/protocol/openid-connect/logout";

                app('Illuminate\Http\Client\Factory')
                    ->asForm()
                    ->post($logoutUrl, [
                        'client_id'     => $clientId,
                        'client_secret' => $clientSecret,
                        'refresh_token' => $refreshToken,
                    ]);
            } catch (\Exception $e) {
                // Bỏ qua lỗi revoke - vẫn đăng xuất local
            }
        }

        return redirect('/login');
    }

    /**
     * Điều hướng người dùng đến trang phù hợp dựa trên role.
     */
    private function redirectByRole(array $roles): \Illuminate\Http\RedirectResponse
    {
        if (in_array('ADMIN', $roles))          return redirect('/dashboard');
        if (in_array('RECEPTIONIST', $roles))   return redirect('/consultations');
        if (in_array('DOCTOR', $roles))         return redirect('/consultations/doctor/my-consultations');
        if (in_array('CASHIER', $roles))        return redirect('/invoices');
        if (in_array('PHARMACIST', $roles))     return redirect('/prescriptions');
        if (in_array('PATIENT', $roles))        return redirect('/my-medical-record');

        // Fallback: không có role nghiệp vụ
        Auth::logout();
        return redirect('/login')->withErrors([
            'keycloak' => 'Tài khoản của bạn chưa được cấp quyền truy cập. Vui lòng liên hệ quản trị viên.'
        ]);
    }
}

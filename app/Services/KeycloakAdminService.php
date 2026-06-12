<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

/**
 * KeycloakAdminService
 *
 * Đóng gói toàn bộ giao tiếp với Keycloak Admin REST API.
 * Dùng client_credentials để lấy admin access token.
 *
 * Tham khảo: https://www.keycloak.org/docs-api/24.0/rest-api/
 */
class KeycloakAdminService
{
    private string $baseUrl;
    private string $realm;
    private string $clientId;
    private string $clientSecret;

    public function __construct()
    {
        $this->baseUrl      = rtrim(config('services.keycloak.base_url'), '/');
        $this->realm        = config('services.keycloak.realms');
        $this->clientId     = config('services.keycloak.client_id');
        $this->clientSecret = config('services.keycloak.client_secret');
    }

    // =====================================================================
    // AUTH: Lấy Admin Token qua client_credentials
    // =====================================================================

    /**
     * Lấy admin access token (cache 55 giây để tránh gọi API liên tục).
     */
    private function getAdminToken(): string
    {
        return Cache::remember('keycloak_admin_token', 55, function () {
            $response = Http::asForm()->post(
                "{$this->baseUrl}/realms/{$this->realm}/protocol/openid-connect/token",
                [
                    'grant_type'    => 'client_credentials',
                    'client_id'     => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ]
            );

            if ($response->failed()) {
                throw new \RuntimeException('Không thể lấy Keycloak admin token: ' . $response->body());
            }

            return $response->json('access_token');
        });
    }

    /**
     * Tạo HTTP request đã được xác thực với admin token.
     */
    private function adminHttp()
    {
        return Http::withToken($this->getAdminToken())
                   ->baseUrl("{$this->baseUrl}/admin/realms/{$this->realm}");
    }

    // =====================================================================
    // USERS
    // =====================================================================

    /**
     * Lấy danh sách users từ Keycloak.
     */
    public function getUsers(int $first = 0, int $max = 50, ?string $search = null): array
    {
        $params = ['first' => $first, 'max' => $max];
        if ($search) $params['search'] = $search;

        $response = $this->adminHttp()->get('/users', $params);
        return $response->successful() ? $response->json() : [];
    }

    /**
     * Lấy chi tiết một user theo Keycloak ID (sub).
     */
    public function getUser(string $keycloakId): ?array
    {
        $response = $this->adminHttp()->get("/users/{$keycloakId}");
        return $response->successful() ? $response->json() : null;
    }

    /**
     * Enable/Disable user (lock/unlock tài khoản).
     */
    public function setUserEnabled(string $keycloakId, bool $enabled): bool
    {
        $response = $this->adminHttp()->put("/users/{$keycloakId}", [
            'enabled' => $enabled,
        ]);
        return $response->successful();
    }

    /**
     * Admin đặt lại mật khẩu tạm cho user.
     * Nếu $temporary = true, user sẽ bị bắt đổi mật khẩu khi đăng nhập lần đầu.
     */
    public function resetPassword(string $keycloakId, string $newPassword, bool $temporary = true): bool
    {
        $response = $this->adminHttp()->put("/users/{$keycloakId}/reset-password", [
            'type'      => 'password',
            'value'     => $newPassword,
            'temporary' => $temporary,
        ]);
        return $response->successful();
    }

    /**
     * Gửi email reset password cho user.
     */
    public function sendResetPasswordEmail(string $keycloakId): bool
    {
        $response = $this->adminHttp()->put("/users/{$keycloakId}/execute-actions-email", [
            'UPDATE_PASSWORD',
        ]);
        return $response->successful();
    }

    // =====================================================================
    // ROLES
    // =====================================================================

    /**
     * Lấy danh sách roles của user.
     */
    public function getUserRoles(string $keycloakId): array
    {
        $response = $this->adminHttp()->get("/users/{$keycloakId}/role-mappings/realm");
        return $response->successful() ? $response->json() : [];
    }

    /**
     * Gán realm role cho user.
     * $roles là mảng ['id' => '...', 'name' => '...']
     */
    public function assignRoles(string $keycloakId, array $roles): bool
    {
        $response = $this->adminHttp()->post(
            "/users/{$keycloakId}/role-mappings/realm",
            $roles
        );
        return $response->successful();
    }

    /**
     * Lấy danh sách tất cả realm roles trong Keycloak.
     */
    public function getAllRealmRoles(): array
    {
        $response = $this->adminHttp()->get('/roles');
        return $response->successful() ? $response->json() : [];
    }

    /**
     * Lấy role object theo tên.
     */
    public function getRealmRole(string $roleName): ?array
    {
        $response = $this->adminHttp()->get("/roles/{$roleName}");
        return $response->successful() ? $response->json() : null;
    }

    /**
     * URL trang Account Console Keycloak (đổi mật khẩu, profile...).
     */
    public function getAccountConsoleUrl(): string
    {
        return "{$this->baseUrl}/realms/{$this->realm}/account";
    }
}

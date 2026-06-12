# Hướng dẫn Setup Keycloak cho QLPK

## Bước 1: Cài đặt Docker Desktop

> Máy chưa có Docker. Tải và cài đặt tại:
> https://www.docker.com/products/docker-desktop/

Sau khi cài xong, khởi động Docker Desktop và chờ icon Docker ở thanh taskbar chuyển xanh.

---

## Bước 2: Khởi động Keycloak

Mở terminal tại thư mục dự án (`e:\laragon\www\qlpk`) và chạy:

```bash
docker compose -f docker-compose.keycloak.yml up -d
```

Keycloak sẽ khởi động tại: **http://localhost:8180**

> ⏳ Lần đầu chạy có thể mất 1-2 phút để Keycloak khởi động hoàn toàn.

---

## Bước 3: Cấu hình Realm `qlpk`

### 3.1. Đăng nhập Keycloak Admin Console

- URL: http://localhost:8180/admin
- Username: `admin`
- Password: `admin`

### 3.2. Tạo Realm mới

1. Click **Master** (góc trái trên) → **Create realm**
2. Realm name: `qlpk`
3. Click **Create**

### 3.3. Tạo Client `qlpk-app`

1. Sidebar → **Clients** → **Create client**
2. **Client type**: `OpenID Connect`
3. **Client ID**: `qlpk-app`
4. Click **Next**
5. **Client authentication**: `ON` (Confidential client)
6. **Standard flow**: `ON`
7. Click **Next**
8. **Valid redirect URIs**: `http://localhost/auth/callback`
9. **Valid post logout redirect URIs**: `http://localhost/`
10. **Web origins**: `http://localhost`
11. Click **Save**

### 3.4. Lấy Client Secret

1. Vào tab **Credentials** của client `qlpk-app`
2. Copy **Client Secret**
3. Điền vào `.env`:
   ```dotenv
   KEYCLOAK_CLIENT_SECRET=<dán secret vào đây>
   ```

### 3.5. Tạo Realm Roles

Sidebar → **Realm roles** → **Create role** (tạo lần lượt 6 roles):

| Role name | Mô tả |
|---|---|
| `ADMIN` | Quản trị viên tối cao |
| `RECEPTIONIST` | Tiếp đón viên |
| `DOCTOR` | Bác sĩ khám bệnh |
| `CASHIER` | Thu ngân |
| `PHARMACIST` | Dược sĩ |
| `PATIENT` | Bệnh nhân |

### 3.6. Cấu hình Mapper để Roles xuất hiện trong Token

1. Sidebar → **Clients** → `qlpk-app` → tab **Client scopes**
2. Click vào `qlpk-app-dedicated`
3. Tab **Mappers** → **Add mapper** → **By configuration**
4. Chọn **User Realm Role**
5. Cấu hình:
   - Name: `realm-roles`
   - Token Claim Name: `realm_access.roles`
   - Add to ID token: `ON`
   - Add to access token: `ON`
   - Add to userinfo: `ON`
6. Click **Save**

---

## Bước 4: Tạo tài khoản `bacsi_admin`

1. Sidebar → **Users** → **Add user**
2. Điền thông tin:
   - **Username**: `bacsi_admin`
   - **Email**: `bacsi.admin@gmail.com`
   - **First name**: `BS. Nguyễn Văn`
   - **Last name**: `A`
   - **Email verified**: `ON`
3. Click **Create**

### 4.1. Đặt mật khẩu

1. Tab **Credentials** → **Set password**
2. Password: `123456`
3. **Temporary**: `OFF` (để không bắt đổi mật khẩu khi login lần đầu)
4. Click **Save**

### 4.2. Gán Roles

1. Tab **Role mappings** → **Assign role**
2. Filter: **Filter by realm roles**
3. Tích chọn: `ADMIN` và `DOCTOR`
4. Click **Assign**

---

## Bước 5: Cập nhật `.env` và chạy Seeder

```bash
# Sau khi điền KEYCLOAK_CLIENT_SECRET vào .env, xóa cache config
php artisan config:clear

# Chạy seeder để tạo dữ liệu nghiệp vụ ban đầu (Phòng, Khoa, Nhân sự)
php artisan db:seed --class=SingleDoctorClinicSeeder
```

---

## Bước 6: Đăng nhập lần đầu

1. Truy cập: **http://localhost/login**
2. Click nút **"Đăng nhập qua Keycloak"**
3. Điền `bacsi_admin` / `123456`
4. Hệ thống tự động:
   - Nhận `sub` UUID từ Keycloak
   - Tìm User có `username = 'bacsi_admin'` trong DB
   - Cập nhật `keycloak_sub` = UUID nhận được
   - Lưu roles `['ADMIN', 'DOCTOR']` vào session
   - Redirect vào `/dashboard`

✅ **Từ lần sau**, chỉ cần vào Keycloak Admin Console để tạo/sửa/khóa user. App tự nhận roles từ token.

---

## Quản lý User sau này

### Tạo nhân viên mới (ví dụ: Thu ngân)

1. Keycloak → **Users** → **Add user** (tạo tài khoản)
2. Gán role `CASHIER`
3. Vào QLPK → **Quản lý nhân sự** → Tạo hồ sơ Staff

### Khóa tài khoản

- Trong QLPK: trang **Quản lý người dùng** → Toggle trạng thái (tự động call Keycloak API)
- Hoặc trực tiếp tại Keycloak: Users → [user] → **Enabled: OFF**

### Đặt lại mật khẩu

- Trong QLPK: trang **Chi tiết người dùng** → **Đặt lại mật khẩu** (call Keycloak API)
- Hoặc Keycloak → Users → [user] → Credentials → Set password

### Quên mật khẩu (tự phục hồi)

Người dùng click **"Forgot password"** trên trang đăng nhập Keycloak → nhận email reset link.

> **Cần cấu hình SMTP trong Keycloak:**
> Realm Settings → Email → điền cấu hình SMTP (Gmail, Mailpit...)

---

## Dừng Keycloak

```bash
docker compose -f docker-compose.keycloak.yml down
```

## Khởi động lại (data được giữ)

```bash
docker compose -f docker-compose.keycloak.yml up -d
```

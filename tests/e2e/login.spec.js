const { test, expect } = require('@playwright/test');

test.describe('Trang Đăng Nhập (Login Page)', () => {
  
  test.beforeEach(async ({ page }) => {
    // Di chuyển tới trang đăng nhập
    await page.goto('/login');
  });

  test('Hiển thị tiêu đề và nội dung trang đăng nhập', async ({ page }) => {
    // Kiểm tra tiêu đề của trang
    await expect(page).toHaveTitle(/Đăng nhập/);

    // Kiểm tra sự xuất hiện của tiêu đề "Welcome!"
    const welcomeHeader = page.locator('h3:has-text("Welcome!")');
    await expect(welcomeHeader).toBeVisible();

    // Kiểm tra thông tin mô tả bảo mật Keycloak
    const alertInfo = page.locator('.alert-keycloak');
    await expect(alertInfo).toBeVisible();
    await expect(alertInfo).toContainText('Keycloak Identity Server');
  });

  test('Hiển thị nút đăng nhập qua Keycloak đầy đủ và đúng định dạng', async ({ page }) => {
    // Tìm nút Đăng nhập Keycloak theo ID
    const loginButton = page.locator('#loginBtn');
    
    // Đảm bảo nút này hiển thị trên màn hình
    await expect(loginButton).toBeVisible();
    
    // Kiểm tra xem nút có chứa text "Đăng nhập qua Keycloak" không
    await expect(loginButton).toContainText('Đăng nhập qua Keycloak');
    
    // Kiểm tra href của nút xem có điều hướng đúng tới endpoint auth của Keycloak không
    const hrefAttr = await loginButton.getAttribute('href');
    expect(hrefAttr).toContain('/auth/redirect'); // route('keycloak.redirect') trong Laravel trỏ về /auth/redirect
  });

  test('Hiển thị đầy đủ thông tin bảo mật và huy hiệu', async ({ page }) => {
    // Kiểm tra sự xuất hiện của các huy hiệu bảo mật
    const ssoBadge = page.locator('.security-badges >> text=SSO');
    await expect(ssoBadge).toBeVisible();

    const oidcBadge = page.locator('.security-badges >> text=OIDC');
    await expect(oidcBadge).toBeVisible();

    // Kiểm tra danh sách tính năng bảo mật ở bên dưới
    const featureItems = page.locator('.feature-list li');
    await expect(featureItems).toHaveCount(3);
    await expect(featureItems.first()).toContainText('Mật khẩu được Keycloak mã hóa');
  });

  test('Đăng nhập thành công với tài khoản bacsi_admin qua Keycloak', async ({ page }) => {
    // 1. Click nút "Đăng nhập qua Keycloak" trên trang Laravel
    await page.locator('#loginBtn').click();

    // 2. Chờ chuyển hướng tới giao diện đăng nhập Keycloak
    // Đợi ô nhập username xuất hiện
    await page.waitForSelector('#username', { timeout: 15000 });

    // 3. Điền thông tin đăng nhập
    await page.fill('#username', 'bacsi_admin');
    await page.fill('#password', '123456');

    // 4. Bấm nút đăng nhập trên Keycloak (ID mặc định của Keycloak là #kc-login)
    const kcLoginBtn = page.locator('#kc-login');
    if (await kcLoginBtn.count() > 0) {
      await kcLoginBtn.click();
    } else {
      await page.click('input[type="submit"]');
    }

    // 5. Đợi hệ thống xử lý callback và chuyển hướng về Laravel Dashboard/Consultations
    // Trình duyệt sẽ được chuyển hướng về trang sau khi đăng nhập thành công
    await page.waitForURL(/.*(consultations|dashboard|home)/, { timeout: 20000 });

    // 6. Xác nhận đăng nhập thành công bằng cách kiểm tra URL hiện tại
    const currentURL = page.url();
    expect(currentURL).toMatch(/.*(consultations|dashboard|home)/);
  });

});

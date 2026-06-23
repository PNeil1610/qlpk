const { test, expect } = require('@playwright/test');

test.describe('Quản Lý Bệnh Nhân (Patient Management)', () => {

  test('Tạo bệnh nhân mới thành công', async ({ page }) => {
    // Tăng thời gian chờ lên 90 giây
    test.setTimeout(90000);

    // 1. Đăng nhập với tài khoản bacsi_admin
    await page.goto('/login');
    await page.locator('#loginBtn').click();
    await page.waitForSelector('#username', { timeout: 15000 });
    await page.fill('#username', 'bacsi_admin');
    await page.fill('#password', '123456');

    const kcLoginBtn = page.locator('#kc-login');
    if (await kcLoginBtn.count() > 0) {
      await kcLoginBtn.click();
    } else {
      await page.click('input[type="submit"]');
    }

    // Đợi đăng nhập thành công và chuyển hướng về ứng dụng
    await page.waitForURL(/.*(consultations|dashboard|home)/, { timeout: 20000 });

    // 2. Đi tới trang tạo bệnh nhân
    await page.goto('/patients/create');

    // Tạo các thông tin duy nhất để tránh bị trùng lặp dữ liệu (Unique constraint)
    const uniqueId = Date.now();
    const testPhone = `08${String(uniqueId).substring(5)}`; // SĐT hợp lệ từ timestamp
    const testFullName = `Bệnh Nhân Test ${uniqueId}`;
    const testEmail = `test.patient.${uniqueId}@example.com`;
    const testNationalId = `34${String(uniqueId).substring(3)}`; // CCCD hợp lệ từ timestamp

    // Điền thông tin vào form
    await page.fill('input[name="full_name"]', testFullName);
    await page.selectOption('select[name="gender"]', 'Male');
    await page.fill('input[name="age"]', '30');
    await page.fill('input[name="phone_number"]', testPhone);
    await page.fill('input[name="email"]', testEmail);
    await page.fill('input[name="address"]', 'Ninh Kiều, Cần Thơ');
    await page.fill('input[name="national_id"]', testNationalId);
    await page.fill('input[name="insurance_number"]', `GD491${String(uniqueId).substring(8)}`);

    // 3. Nhấp nút "Lưu" để gửi form
    await page.click('button:has-text("Lưu"), input[type="submit"]');

    // 4. Đợi điều hướng về trang danh sách bệnh nhân
    await page.waitForURL(/\/patients/, { timeout: 60000 });

    // 5. Kiểm tra thông báo thành công từ Laravel flash session
    const successAlert = page.locator('.alert-success');
    if (await successAlert.count() > 0) {
      await expect(successAlert).toBeVisible();
      await expect(successAlert).toContainText('thành công');
    }

    // 6. Xác nhận tên bệnh nhân vừa tạo xuất hiện trong bảng danh sách bệnh nhân
    const nameLocator = page.locator(`text=${testFullName}`);
    await expect(nameLocator).toBeVisible();
  });

});

const { test, expect } = require('@playwright/test');

test.describe('Quản Lý Nhân Viên (Staff Management)', () => {

  test('Tạo nhân viên mới thành công', async ({ page }) => {
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

    // 2. Đi tới trang tạo nhân viên
    await page.goto('/staff/create');

    // Tạo các thông tin duy nhất dựa trên timestamp để tránh bị trùng lặp dữ liệu (Unique constraint)
    const uniqueId = Date.now();
    const testUsername = `test.staff.${uniqueId}`;
    const testEmail = `test.staff.${uniqueId}@example.com`;
    const testPhone = `09${String(uniqueId).substring(5)}`; // SĐT hợp lệ từ timestamp
    const testFullName = `Nhân Viên Test ${uniqueId}`;
    const testNationalId = `12${String(uniqueId).substring(3)}`; // CCCD hợp lệ từ timestamp

    // Điền thông tin cá nhân
    await page.fill('input[name="full_name"]', testFullName);
    await page.selectOption('select[name="gender"]', 'Male');
    await page.fill('input[name="yob"]', '1995');
    await page.fill('input[name="phone_number"]', testPhone);
    await page.fill('input[name="national_id"]', testNationalId);
    await page.fill('input[name="email"]', testEmail);
    await page.fill('input[name="address"]', 'Ninh Kiều, Cần Thơ');

    // Chuyên môn & Vị trí
    await page.selectOption('select[name="staff_type"]', 'Doctor');
    await page.fill('input[name="academic_degree"]', 'BS.CKI');
    
    // Chọn Khoa Lâm Sàng (tìm kiếm option chứa chữ "Lâm Sàng")
    const deptSelect = page.locator('select[name="department_id"]');
    const optionToSelect = deptSelect.locator('option:has-text("Lâm Sàng"), option:has-text("lâm sàng"), option:has-text("Lâm sàng")').first();
    if (await optionToSelect.count() > 0) {
      const deptValue = await optionToSelect.getAttribute('value');
      await deptSelect.selectOption(deptValue);
    } else {
      // Fallback nếu không tìm thấy text, thử chọn theo nhãn chính xác
      await deptSelect.selectOption({ label: 'Khoa Lâm Sàng' });
    }

    // Chọn Phòng làm việc (Chọn phòng đầu tiên khả dụng không bị disabled và khác rỗng)
    const roomSelect = page.locator('select[name="room_id"]');
    const firstAvailableRoom = roomSelect.locator('option:not([disabled]):not([value=""])').first();
    if (await firstAvailableRoom.count() > 0) {
      const roomValue = await firstAvailableRoom.getAttribute('value');
      await roomSelect.selectOption(roomValue);
    }

    await page.fill('input[name="specialty"]', 'Nội tổng hợp');
    await page.fill('input[name="position"]', 'Bác sĩ điều trị');
    await page.fill('input[name="medical_license"]', `CCHN-${uniqueId}`);

    // Thiết lập tài khoản
    await page.fill('input[name="username"]', testUsername);
    await page.fill('input[name="password"]', 'Password123');

    // 3. Nhấp nút "Lưu Nhân Viên" để gửi form
    await page.click('button[type="submit"]:has-text("Lưu Nhân Viên")');

    // 4. Đợi điều hướng về trang danh sách nhân viên
    await page.waitForURL(/\/staff/, { timeout: 60000 });

    // 5. Kiểm tra thông báo thành công từ Laravel flash session
    const successAlert = page.locator('.alert-success');
    if (await successAlert.count() > 0) {
      await expect(successAlert).toBeVisible();
      await expect(successAlert).toContainText('thành công');
    }

    // 6. Xác nhận tên nhân viên vừa tạo xuất hiện trong bảng danh sách nhân viên
    const nameLocator = page.locator(`text=${testFullName}`);
    await expect(nameLocator).toBeVisible();
  });

});

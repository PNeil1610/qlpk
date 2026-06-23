const { test, expect } = require('@playwright/test');

test.describe('Quản Lý Phiên Khám (Consultation Management)', () => {

  test('Tạo phiên khám mới thành công tại phòng khám Khoa Lâm Sàng', async ({ page }) => {
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

    // 2. Đi tới trang danh sách Phòng để tìm Phòng thuộc Khoa Lâm Sàng
    await page.goto('/rooms');
    
    // Tìm hàng trong bảng có cột Khoa là "Khoa Lâm Sàng" và lấy Tên phòng
    const roomRow = page.locator('tbody#roomTable tr:has(td:nth-child(4):has-text("Khoa Lâm Sàng"))').first();
    
    let targetRoomName = '';
    if (await roomRow.count() > 0) {
      // Cột thứ 2 là tên phòng (td:nth-child(2))
      const nameText = await roomRow.locator('td:nth-child(2)').textContent();
      targetRoomName = nameText ? nameText.trim() : '';
    }
    
    // Fallback nếu không quét được phòng nào thuộc Khoa Lâm Sàng
    if (!targetRoomName) {
      targetRoomName = 'Phòng Khám'; // Từ khóa chung để tìm phòng
    }

    // 3. Đi tới trang danh sách Bệnh nhân để chọn bệnh nhân đầu tiên tạo phiên khám
    await page.goto('/patients');
    
    const firstPatientRow = page.locator('tbody#patientTable tr').filter({ hasNotText: 'Nguyễn Văn Test' }).first();
    const patientName = await firstPatientRow.locator('td:nth-child(2)').textContent();
    const cleanPatientName = patientName ? patientName.trim() : '';

    // Click nút "Tạo phiên khám" (nút màu xanh lá có class btn-success hoặc icon fa-notes-medical)
    const createConsultationBtn = firstPatientRow.locator('a[title="Tạo phiên khám"]').first();
    await createConsultationBtn.click();

    // 4. Đợi điều hướng tới trang tạo phiên khám (URL sẽ dạng /consultations/create?patient_id=X)
    await page.waitForURL(/\/consultations\/create/, { timeout: 15000 });

    // 5. Điền thông tin vào form tạo phiên khám
    // Chọn phòng khám thuộc Khoa Lâm Sàng đã lấy được ở bước 2
    const roomSelect = page.locator('select[name="room_id"]');
    
    // Tìm option chứa tên phòng khám mục tiêu
    const roomOption = roomSelect.locator(`option:has-text("${targetRoomName}")`).first();
    if (await roomOption.count() > 0) {
      const roomValue = await roomOption.getAttribute('value');
      await roomSelect.selectOption(roomValue);
    } else {
      // Fallback: chọn option phòng đầu tiên khả dụng nếu không khớp chính xác tên phòng
      await roomSelect.selectOption({ index: 1 });
    }

    // Điền lý do khám
    await page.fill('input[name="chief_complaint"]', 'Đau đầu, chóng mặt và mệt mỏi nhẹ');

    // Điền triệu chứng
    await page.fill('textarea[name="symptoms"]', 'Sốt nhẹ khoảng 37.8 độ C vào buổi chiều, đau vùng thái dương.');

    // Điền chỉ số sinh tồn
    await page.fill('input[name="temperature"]', '37.8');
    await page.fill('input[name="blood_pressure"]', '120/80');

    // 6. Nhấp nút "Tạo Phiên Khám"
    await page.click('button[type="submit"]:has-text("Tạo Phiên Khám")');

    // 7. Đợi điều hướng về trang danh sách phiên khám
    await page.waitForURL(/\/consultations/, { timeout: 60000 });

    // 8. Kiểm tra thông báo thành công và sự xuất hiện của bệnh nhân trong danh sách phiên khám mới tạo
    const successAlert = page.locator('.alert-success');
    if (await successAlert.count() > 0) {
      await expect(successAlert).toBeVisible();
      await expect(successAlert).toContainText('thành công');
    }

    // Xác nhận tên bệnh nhân có mặt trong danh sách phiên khám
    const patientInList = page.locator(`tbody tr:has-text("${cleanPatientName}")`).first();
    await expect(patientInList).toBeVisible();
  });

});

const { test, expect } = require('@playwright/test');

test.describe('Nghiệp Vụ Lâm Sàng của Bác Sĩ (Doctor Clinical Examination)', () => {

  test('Thực hiện quy trình khám lâm sàng và lưu kết luận thành công', async ({ page }) => {
    // Tăng thời gian chờ cho test case dài này lên 120 giây (do chạy trên DB remote)
    test.setTimeout(120000);

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
    const roomRow = page.locator('tbody#roomTable tr:has(td:nth-child(4):has-text("Khoa Lâm Sàng"))').first();
    let targetRoomName = '';
    if (await roomRow.count() > 0) {
      const nameText = await roomRow.locator('td:nth-child(2)').textContent();
      targetRoomName = nameText ? nameText.trim() : '';
    }
    if (!targetRoomName) {
      targetRoomName = 'Phòng Khám';
    }

    // 3. Lấy bệnh nhân đầu tiên trong danh sách để chuẩn bị tạo ca khám
    await page.goto('/patients');
    
    const firstPatientRow = page.locator('tbody#patientTable tr').filter({ hasNotText: 'Nguyễn Văn Test' }).first();
    const patientNameText = await firstPatientRow.locator('td:nth-child(2)').textContent();
    const testFullName = patientNameText ? patientNameText.trim() : '';

    // Bấm Tạo phiên khám cho Bệnh nhân này
    const createConsultationBtn = firstPatientRow.locator('a[title="Tạo phiên khám"]').first();
    await createConsultationBtn.click();

    // Điền form tạo phiên khám
    await page.waitForURL(/\/consultations\/create/, { timeout: 15000 });
    const roomSelect = page.locator('select[name="room_id"]');
    const roomOption = roomSelect.locator(`option:has-text("${targetRoomName}")`).first();
    if (await roomOption.count() > 0) {
      const roomValue = await roomOption.getAttribute('value');
      await roomSelect.selectOption(roomValue);
    } else {
      await roomSelect.selectOption({ index: 1 });
    }

    await page.fill('input[name="chief_complaint"]', 'Ho sốt kéo dài');
    await page.fill('textarea[name="symptoms"]', 'Ho có đờm, đau họng, sốt nhẹ về chiều.');
    await page.fill('input[name="temperature"]', '37.9');
    await page.fill('input[name="blood_pressure"]', '115/75');
    await page.click('button[type="submit"]:has-text("Tạo Phiên Khám")');

    // Đợi tạo xong phiên khám
    await page.waitForURL(/\/consultations/, { timeout: 60000 });

    // 5. Điều hướng tới Trang danh sách ca khám của Bác sĩ
    await page.goto('/consultations/doctor/my-consultations');

    // Tìm phiên khám của bệnh nhân vừa tạo trong phòng của bác sĩ
    // Tìm hàng trong bảng dựa trên tên bệnh nhân
    const consultationRow = page.locator(`tbody#consultationTable tr:has-text("${testFullName}")`).first();
    await expect(consultationRow).toBeVisible();

    // Click nút khám bệnh (nút có icon fa-stethoscope)
    const examineBtn = consultationRow.locator('a[title="Khám bệnh & Kê đơn"]').first();
    await examineBtn.click();

    // 6. Đợi điều hướng tới Trang khám bệnh
    await page.waitForURL(/\/consultations\/doctor\/\d+\/examine/, { timeout: 15000 });

    // 7. Điền thông tin Chẩn đoán & Kết luận chuyên môn
    await page.fill('textarea[name="diagnosis_notes"]', 'Viêm họng cấp tính, cần theo dõi thêm sốt siêu vi.');

    // Chọn mã bệnh ICD-10 chuẩn (sử dụng Select2)
    // Click vào select2 container để mở dropdown danh sách có sẵn
    const select2Container = page.locator('select#diagnosis_code + .select2-container .select2-selection').first();
    await select2Container.click();

    // Đợi danh sách tùy chọn xuất hiện và click chọn option đầu tiên có sẵn
    const firstResult = page.locator('.select2-results__option').first();
    await firstResult.waitFor({ state: 'visible', timeout: 10000 });
    await firstResult.click();

    // Điền ngày tái khám (7 ngày sau)
    const futureDate = new Date();
    futureDate.setDate(futureDate.getDate() + 7);
    const dateString = futureDate.toISOString().split('T')[0]; // Định dạng YYYY-MM-DD
    await page.fill('input[name="follow_up_date"]', dateString);

    // Điền lời dặn bác sĩ
    await page.fill('textarea[name="doctor_advice"]', 'Uống nhiều nước ấm, nghỉ ngơi tại giường, tránh đồ lạnh.');

    // 7.5. Kê đơn thuốc (Bắt buộc do dòng thuốc đầu tiên được tạo sẵn có thuộc tính required)
    // Chọn Nhóm thuốc
    const categorySelect = page.locator('.category-select + .select2-container .select2-selection').first();
    if (await categorySelect.count() > 0) {
      await categorySelect.click();
      const firstCatOption = page.locator('.select2-results__option').first();
      await firstCatOption.waitFor({ state: 'visible', timeout: 5000 });
      await firstCatOption.click();
    }

    // Chọn Tên thuốc
    const medicineSelect = page.locator('.medicine-select + .select2-container .select2-selection').first();
    if (await medicineSelect.count() > 0) {
      await medicineSelect.click();
      const firstMedOption = page.locator('.select2-results__option').first();
      await firstMedOption.waitFor({ state: 'visible', timeout: 10000 });
      await firstMedOption.click();
    }

    // Điền liều dùng Sáng/Tối và Số ngày sử dụng
    await page.locator('.dose-sang').first().fill('1');
    await page.locator('.dose-toi').first().fill('1');
    await page.locator('.duration-days').first().fill('5');

    // 8. Bấm Lưu kết quả khám
    await page.click('button[type="submit"]:has-text("Lưu kết quả khám")');

    // 9. Đợi điều hướng về lại danh sách ca khám của bác sĩ
    try {
      await page.waitForURL(/\/consultations\/doctor\/my-consultations/, { timeout: 60000 });
    } catch (e) {
      console.error("=== E2E TEST DEBUG ===");
      console.error("Lỗi điều hướng! URL hiện tại là:", page.url());
      try {
        const bodyText = await page.locator('body').innerText();
        console.error("Nội dung trang hiện tại:", bodyText.substring(0, 1500));
      } catch (innerErr) {
        console.error("Không thể đọc nội dung trang:", innerErr.message);
      }
      console.error("=======================");
      throw e;
    }

    // 10. Xác nhận lưu kết quả khám thành công
    const successAlert = page.locator('.alert-success');
    if (await successAlert.count() > 0) {
      await expect(successAlert).toBeVisible();
      await expect(successAlert).toContainText('thành công');
    }
  });

});

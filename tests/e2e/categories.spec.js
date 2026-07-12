const { test, expect } = require('@playwright/test');

// Helper function to log in as bacsi_admin via Keycloak OIDC
async function loginAsAdmin(page) {
  await page.goto('/login');
  
  // Check if we are already logged in (redirected to home, dashboard, or consultations)
  if (page.url().includes('home') || page.url().includes('dashboard') || page.url().includes('consultations')) {
    return;
  }
  
  const loginBtn = page.locator('#loginBtn');
  if (await loginBtn.count() > 0) {
    await loginBtn.click();
    await page.waitForSelector('#username', { timeout: 15000 });
    await page.fill('#username', 'bacsi_admin');
    await page.fill('#password', '123456');

    const kcLoginBtn = page.locator('#kc-login');
    if (await kcLoginBtn.count() > 0) {
      await kcLoginBtn.click();
    } else {
      await page.click('input[type="submit"]');
    }

    // Wait for redirection back to the app dashboard or home
    await page.waitForURL(/.*(consultations|dashboard|home)/, { timeout: 40000 });
  }
}

test.describe('Quản Lý Danh Mục (Category Management)', () => {

  test('Tạo mới Loại thuốc thủ công thành công', async ({ page }) => {
    test.setTimeout(90000);
    await loginAsAdmin(page);

    // Đi tới trang thêm loại thuốc
    await page.goto('/medicine-categories/create');

    const uniqueId = Date.now();
    const catName = `Loại Thuốc Test ${uniqueId}`;
    const description = `Mô tả cho Loại thuốc Test ${uniqueId}`;

    // Điền thông tin form
    await page.fill('input[name="category_name"]', catName);
    await page.fill('textarea[name="description"]', description);
    await page.selectOption('select[name="status"]', '1');

    // Lưu danh mục
    await page.click('button[type="submit"]:has-text("Lưu Danh Mục")');

    // Đợi điều hướng về trang danh sách
    await page.waitForURL(/\/medicine-categories/, { timeout: 60000 });

    // Kiểm tra thông báo thành công
    const successAlert = page.locator('.alert-success');
    if (await successAlert.count() > 0) {
      await expect(successAlert).toBeVisible();
      await expect(successAlert).toContainText('thành công');
    }

    // Xác nhận loại thuốc vừa tạo xuất hiện trong bảng danh sách (chỉ tìm trong thẻ td để tránh trùng với alert)
    const nameLocator = page.locator(`td:has-text("${catName}")`).first();
    await expect(nameLocator).toBeVisible();
  });

  test('Tạo mới Thuốc thủ công thành công', async ({ page }) => {
    test.setTimeout(90000);
    await loginAsAdmin(page);

    // Đi tới trang tạo thuốc
    await page.goto('/medicines/create');

    const uniqueId = Date.now();
    const medicineName = `Thuốc Test ${uniqueId}`;
    const note = `Ghi chú cho Thuốc Test ${uniqueId}`;

    // Điền thông tin thuốc
    await page.fill('input[name="medicine_name"]', medicineName);
    
    // Chọn nhóm thuốc ngẫu nhiên/đầu tiên có sẵn trong dropdown (không dùng toBeVisible vì option bị ẩn)
    const catSelect = page.locator('select[name="medicine_category_id"]');
    const firstOption = catSelect.locator('option:not([disabled]):not([value=""])').first();
    if (await firstOption.count() > 0) {
      const catValue = await firstOption.getAttribute('value');
      await catSelect.selectOption(catValue);
    }

    await page.fill('textarea[name="note"]', note);
    await page.selectOption('select[name="status"]', '1');

    // Nhấp nút Tạo thuốc
    await page.click('button[type="submit"]:has-text("Tạo Thuốc")');

    // Đợi chuyển hướng tới trang chi tiết thuốc (/medicines/{id})
    await page.waitForURL(/\/medicines\/\d+/, { timeout: 60000 });

    // Kiểm tra thông báo thành công
    const successAlert = page.locator('.alert-success');
    if (await successAlert.count() > 0) {
      await expect(successAlert).toBeVisible();
      await expect(successAlert).toContainText('thành công');
    }

    // Xác nhận tên thuốc xuất hiện trong giao diện chi tiết
    const nameHeader = page.locator(`h3:has-text("${medicineName}"), h2:has-text("${medicineName}"), div:has-text("${medicineName}")`).first();
    await expect(nameHeader).toBeVisible();
  });

  test('Tạo mới Mã bệnh thủ công thành công', async ({ page }) => {
    test.setTimeout(90000);
    await loginAsAdmin(page);

    // Đi tới trang tạo mã bệnh
    await page.goto('/diagnoses/create');

    const uniqueId = Date.now();
    // diagnosis_code tối đa 15 ký tự, ví dụ: TESTA01
    const diagnosisCode = `T${String(uniqueId).substring(7)}`; // 1 chữ cái + 6 số
    const diagnosisName = `Bệnh Test ${uniqueId}`;
    const description = `Mô tả bệnh Test ${uniqueId}`;

    // Điền thông tin mã bệnh
    await page.fill('input[name="diagnosis_code"]', diagnosisCode);
    await page.fill('input[name="diagnosis_name"]', diagnosisName);
    await page.fill('input[name="icd_version"]', 'ICD-10');
    await page.fill('textarea[name="description"]', description);

    // Lưu mã bệnh
    await page.click('button:has-text("Lưu")');

    // Đợi điều hướng về trang danh sách
    await page.waitForURL(/\/diagnoses/, { timeout: 60000 });

    // Kiểm tra thông báo thành công
    const successAlert = page.locator('.alert-success');
    if (await successAlert.count() > 0) {
      await expect(successAlert).toBeVisible();
      await expect(successAlert).toContainText('thành công');
    }

    // Xác nhận tên bệnh xuất hiện trong bảng danh sách (chỉ tìm trong thẻ td)
    const nameLocator = page.locator(`td:has-text("${diagnosisName}")`).first();
    await expect(nameLocator).toBeVisible();
  });

  test('Tạo mới Khoa thủ công thành công', async ({ page }) => {
    test.setTimeout(90000);
    await loginAsAdmin(page);

    // Đi tới trang tạo khoa mới
    await page.goto('/departments/create');

    const uniqueId = Date.now();
    // dept_code tối đa 30 ký tự
    const deptCode = `DEPT_${String(uniqueId).substring(7)}`;
    const deptName = `Khoa Test ${uniqueId}`;
    const description = `Mô tả Khoa Test ${uniqueId}`;

    // Điền thông tin khoa
    await page.fill('input[name="dept_code"]', deptCode);
    await page.fill('input[name="dept_name"]', deptName);
    await page.fill('textarea[name="description"]', description);
    await page.selectOption('select[name="status"]', '1');

    // Lưu khoa
    await page.click('button:has-text("Lưu")');

    // Đợi điều hướng về trang danh sách
    await page.waitForURL(/\/departments/, { timeout: 60000 });

    // Kiểm tra thông báo thành công
    const successAlert = page.locator('.alert-success');
    if (await successAlert.count() > 0) {
      await expect(successAlert).toBeVisible();
      await expect(successAlert).toContainText('thành công');
    }

    // Xác nhận tên khoa vừa tạo xuất hiện trong bảng danh sách (chỉ tìm trong thẻ td)
    const nameLocator = page.locator(`td:has-text("${deptName}")`).first();
    await expect(nameLocator).toBeVisible();
  });

  test('Tạo mới Phòng thủ công thành công', async ({ page }) => {
    test.setTimeout(90000);
    await loginAsAdmin(page);

    // Đi tới trang tạo phòng mới
    await page.goto('/rooms/create');

    const uniqueId = Date.now();
    // room_code tối đa 30 ký tự
    const roomCode = `ROOM_${String(uniqueId).substring(7)}`;
    const roomName = `Phòng Test ${uniqueId}`;

    // Điền thông tin phòng
    await page.fill('input[name="room_code"]', roomCode);
    await page.fill('input[name="room_name"]', roomName);
    await page.selectOption('select[name="room_type"]', 'Examination');

    // Chọn Khoa (lấy option khoa đầu tiên có sẵn trong dropdown)
    const deptSelect = page.locator('select[name="department_id"]');
    const firstOption = deptSelect.locator('option:not([disabled]):not([value=""])').first();
    if (await firstOption.count() > 0) {
      const deptValue = await firstOption.getAttribute('value');
      await deptSelect.selectOption(deptValue);
    }

    await page.selectOption('select[name="status"]', '1');

    // Lưu phòng
    await page.click('button:has-text("Lưu")');

    // Đợi điều hướng về trang danh sách
    await page.waitForURL(/\/rooms/, { timeout: 60000 });

    // Kiểm tra thông báo thành công
    const successAlert = page.locator('.alert-success');
    if (await successAlert.count() > 0) {
      await expect(successAlert).toBeVisible();
      await expect(successAlert).toContainText('thành công');
    }

    // Xác nhận tên phòng vừa tạo xuất hiện trong bảng danh sách (chỉ tìm trong thẻ td)
    const nameLocator = page.locator(`td:has-text("${roomName}")`).first();
    await expect(nameLocator).toBeVisible();
  });

  test('Thêm chi tiết thuốc cho thuốc vừa tạo thành công', async ({ page }) => {
    test.setTimeout(90000);
    await loginAsAdmin(page);

    // 1. Tạo một thuốc mới trước
    await page.goto('/medicines/create');
    const uniqueId = Date.now();
    const medicineName = `Thuốc Chi Tiết Test ${uniqueId}`;
    await page.fill('input[name="medicine_name"]', medicineName);
    
    // Chọn nhóm thuốc đầu tiên khả dụng
    const catSelect = page.locator('select[name="medicine_category_id"]');
    const firstOption = catSelect.locator('option:not([disabled]):not([value=""])').first();
    if (await firstOption.count() > 0) {
      const catValue = await firstOption.getAttribute('value');
      await catSelect.selectOption(catValue);
    }
    
    // Nhấp nút Tạo thuốc
    await page.click('button[type="submit"]:has-text("Tạo Thuốc")');

    // Đợi chuyển hướng tới trang chi tiết thuốc (/medicines/{id}/show)
    await page.waitForURL(/\/medicines\/\d+/, { timeout: 60000 });
    const showUrl = page.url();
    const urlObj = new URL(showUrl);
    const pathSegments = urlObj.pathname.split('/');
    const medicinesIndex = pathSegments.indexOf('medicines');
    const medicineId = pathSegments[medicinesIndex + 1];

    // 2. Đi tới trang quản lý chi tiết & giá của thuốc này
    await page.goto(`/medicines/${medicineId}/details`);

    // 3. Click nút "Thêm phiên bản mới"
    await page.click('a:has-text("Thêm phiên bản mới")');
    await page.waitForURL(new RegExp(`/medicines/${medicineId}/details/create`), { timeout: 20000 });

    // 4. Điền thông tin chi tiết thuốc
    await page.fill('input[name="unit"]', 'Viên');
    await page.fill('input[name="price"]', '5000');
    await page.fill('input[name="active_ingredient"]', 'Paracetamol 500mg');
    await page.fill('input[name="storage_condition"]', 'Nơi khô ráo, tránh ánh nắng');

    // 5. Submit form
    await page.click('button[type="submit"]:has-text("Tạo và Áp dụng ngay")');

    // 6. Xác nhận chuyển hướng về trang danh sách chi tiết thuốc và hiển thị thông tin chính xác
    await page.waitForURL(new RegExp(`/medicines/${medicineId}/details`), { timeout: 60000 });

    // Kiểm tra thông báo thành công
    const successAlert = page.locator('.alert-success');
    if (await successAlert.count() > 0) {
      await expect(successAlert).toBeVisible();
      await expect(successAlert).toContainText('thành công');
    }

    // Kiểm tra đơn vị tính và giá bán hiển thị trên bảng
    const unitCell = page.locator('td.fw-bold', { hasText: 'Viên' }).first();
    await expect(unitCell).toBeVisible();

    const priceCell = page.locator('text=5.000 đ').first();
    await expect(priceCell).toBeVisible();
  });

});


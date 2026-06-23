const { test, expect } = require('@playwright/test');

test.describe('Quy Trình Khám & Thanh Toán Khép Kín (Integrated Patient End-to-End Flow)', () => {

  test('Kiểm thử toàn bộ quy trình từ Tạo bệnh nhân đến Cấp phát thuốc thành công', async ({ page }) => {
    // Tăng thời gian chờ lên 120 giây do chạy toàn bộ quy trình trên DB remote
    test.setTimeout(120000);

    // ==========================================
    // 1. ĐĂNG NHẬP HỆ THỐNG
    // ==========================================
    console.log('Bước 1: Đăng nhập vào hệ thống...');
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

    // ==========================================
    // 2. TÌM PHÒNG KHÁM LÂM SÀNG DÂN DỤNG SẴN CÓ
    // ==========================================
    console.log('Bước 2: Tìm phòng khám thuộc Khoa Lâm Sàng...');
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
    console.log(`Phòng khám lâm sàng được chọn: "${targetRoomName}"`);

    // ==========================================
    // 3. TẠO HỒ SƠ BỆNH NHÂN MỚI "NGUYỄN VĂN TEST"
    // ==========================================
    console.log('Bước 3: Đăng ký hồ sơ bệnh nhân mới...');
    await page.goto('/patients/create');

    const uniqueId = Date.now();
    const testPhone = `098${String(uniqueId).substring(6)}`; // Đảm bảo số điện thoại là duy nhất
    const testFullName = 'Nguyễn Văn Test';
    const testEmail = `nguyenvan.test.${uniqueId}@example.com`;
    const testNationalId = `12${String(uniqueId).substring(3)}`; // CCCD 12 số duy nhất
    const testInsurance = `GD479${String(uniqueId).substring(8)}`;

    await page.fill('input[name="full_name"]', testFullName);
    await page.selectOption('select[name="gender"]', 'Male');
    await page.fill('input[name="age"]', '30');
    await page.fill('input[name="phone_number"]', testPhone);
    await page.fill('input[name="email"]', testEmail);
    await page.fill('input[name="address"]', 'Ninh Kiều, Cần Thơ');
    await page.fill('input[name="national_id"]', testNationalId);
    await page.fill('input[name="insurance_number"]', testInsurance);

    // Gửi form
    await page.click('button:has-text("Lưu"), input[type="submit"]');

    // Chờ điều hướng về trang danh sách và lấy mã bệnh nhân duy nhất của họ
    await page.waitForURL(/\/patients/, { timeout: 60000 });
    
    const patientRow = page.locator(`tbody#patientTable tr:has-text("${testPhone}")`).first();
    await expect(patientRow).toBeVisible({ timeout: 15000 });
    
    const patientCodeText = await patientRow.locator('td:nth-child(1)').textContent();
    const patientCode = patientCodeText ? patientCodeText.trim() : '';
    console.log(`Đã tạo Bệnh nhân: ${testFullName} | Mã BN: ${patientCode} | SĐT: ${testPhone}`);

    // ==========================================
    // 4. TẠO PHIÊN KHÁM CHO BỆNH NHÂN MỚI
    // ==========================================
    console.log(`Bước 4: Tạo phiên khám cho bệnh nhân ${patientCode}...`);
    const createConsultationBtn = patientRow.locator('a[title="Tạo phiên khám"]').first();
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

    await page.fill('input[name="chief_complaint"]', 'Đau họng, sốt nhẹ và ho khan nhiều ngày');
    await page.fill('textarea[name="symptoms"]', 'Ho khan nhiều về đêm, đau rát cổ họng, mệt mỏi.');
    await page.fill('input[name="temperature"]', '37.6');
    await page.fill('input[name="blood_pressure"]', '120/80');
    
    await page.click('button[type="submit"]:has-text("Tạo Phiên Khám")');
    await page.waitForURL(/\/consultations/, { timeout: 60000 });

    // ==========================================
    // 5. BÁC SĨ THỰC HIỆN KHÁM LÂM SÀNG & KÊ ĐƠN THUỐC
    // ==========================================
    console.log('Bước 5: Bác sĩ thực hiện khám bệnh và kê đơn...');
    await page.goto('/consultations/doctor/my-consultations');

    // Tìm phiên khám dựa trên mã bệnh nhân duy nhất vừa tạo
    const consultationRow = page.locator(`tbody#consultationTable tr:has-text("${patientCode}")`).first();
    await expect(consultationRow).toBeVisible({ timeout: 15000 });

    // Click nút "Khám bệnh & Kê đơn" (icon fa-stethoscope)
    const examineBtn = consultationRow.locator('a[title="Khám bệnh & Kê đơn"]').first();
    await examineBtn.click();

    // Chờ điều hướng đến trang Khám bệnh
    await page.waitForURL(/\/consultations\/doctor\/\d+\/examine/, { timeout: 15000 });

    // Điền chẩn đoán bệnh
    await page.fill('textarea[name="diagnosis_notes"]', 'Viêm họng cấp tính, cần nghỉ ngơi và uống nhiều nước.');

    // Chọn mã bệnh ICD-10 bằng Select2
    const select2ICD10 = page.locator('select#diagnosis_code + .select2-container .select2-selection').first();
    await select2ICD10.click();
    const firstICD10Option = page.locator('.select2-results__option').first();
    await firstICD10Option.waitFor({ state: 'visible', timeout: 10000 });
    await firstICD10Option.click();

    // Điền ngày tái khám (7 ngày sau)
    const futureDate = new Date();
    futureDate.setDate(futureDate.getDate() + 7);
    const dateString = futureDate.toISOString().split('T')[0];
    await page.fill('input[name="follow_up_date"]', dateString);

    // Điền lời dặn bác sĩ
    await page.fill('textarea[name="doctor_advice"]', 'Uống nhiều nước ấm, giữ ấm cổ họng, uống thuốc đúng giờ.');

    // Chọn Nhóm thuốc bằng Select2
    const categorySelect = page.locator('.category-select + .select2-container .select2-selection').first();
    if (await categorySelect.count() > 0) {
      await categorySelect.click();
      const firstCatOption = page.locator('.select2-results__option').first();
      await firstCatOption.waitFor({ state: 'visible', timeout: 5000 });
      await firstCatOption.click();
    }

    // Chọn Tên thuốc bằng Select2
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

    // Bấm Lưu kết quả khám
    await page.click('button[type="submit"]:has-text("Lưu kết quả khám")');

    // Chờ điều hướng về lại danh sách của bác sĩ và kiểm tra thông báo thành công
    await page.waitForURL(/\/consultations\/doctor\/my-consultations/, { timeout: 60000 });
    const successExamAlert = page.locator('.alert-success');
    if (await successExamAlert.count() > 0) {
      await expect(successExamAlert).toBeVisible();
    }
    console.log('Bác sĩ đã khám bệnh và lưu chẩn đoán, kê đơn thành công.');

    // ==========================================
    // 6. THU NGÂN XÁC NHẬN THANH TOÁN HÓA ĐƠN
    // ==========================================
    console.log(`Bước 6: Thu ngân thực hiện thanh toán hóa đơn cho bệnh nhân ${patientCode}...`);
    await page.goto('/invoices');

    // Chờ bảng hàng đợi thanh toán xuất hiện
    await page.waitForSelector('tbody#pending-table-body', { timeout: 15000 });

    // Tìm hóa đơn của bệnh nhân dựa trên mã bệnh nhân duy nhất
    const invoiceRow = page.locator(`tbody#pending-table-body tr:has-text("${patientCode}")`).first();
    await expect(invoiceRow).toBeVisible({ timeout: 15000 });

    // Bấm "Xác nhận thanh toán" (green button)
    const payBtn = invoiceRow.locator('a[title="Xác nhận thanh toán"]').first();
    await payBtn.click();

    // Chờ sang trang chi tiết hóa đơn
    await page.waitForURL(/\/invoices\/\d+\/show/, { timeout: 15000 });
    await expect(page.locator('.inv-patient-name')).toContainText(testFullName);

    // Chọn hình thức thanh toán "Cash"
    await page.selectOption('select[name="payment_method"]', 'Cash');

    // Lắng nghe dialog xác nhận thanh toán
    page.once('dialog', async dialog => {
      console.log(`Xử lý dialog thanh toán: ${dialog.message()}`);
      expect(dialog.message()).toContain('Xác nhận thanh toán');
      await dialog.accept();
    });

    // Bấm nút "Thanh toán"
    const confirmPayBtn = page.locator('button[type="submit"]:has-text("Thanh toán")');
    await confirmPayBtn.click();

    // Chờ tải lại trang và xác nhận hóa đơn đã cập nhật thanh toán thành công
    const paidInfo = page.locator('.inv-patient-row:has-text("Thanh toán:")');
    await expect(paidInfo).toBeVisible({ timeout: 20000 });
    
    const paymentForm = page.locator('form[action*="/pay"]');
    await expect(paymentForm).not.toBeVisible();
    console.log('Thu ngân đã thu tiền mặt và hoàn thành thanh toán hóa đơn thành công.');

    // ==========================================
    // 7. DƯỢC SĨ XÁC NHẬN CẤP PHÁT THUỐC
    // ==========================================
    console.log(`Bước 7: Dược sĩ xác nhận cấp phát thuốc cho bệnh nhân ${patientCode}...`);
    await page.goto('/prescriptions');

    // Tìm kiếm đơn thuốc theo mã bệnh nhân
    await page.fill('#searchKeyword', patientCode);
    await page.waitForTimeout(1000); // Chờ kết quả lọc AJAX

    // Tìm dòng đơn thuốc
    const prescriptionRow = page.locator(`tbody tr:has-text("${patientCode}")`).first();
    await expect(prescriptionRow).toBeVisible({ timeout: 15000 });

    const statusBadge = prescriptionRow.locator('td:nth-child(5) span.badge');
    await expect(statusBadge).toContainText('Chưa lấy thuốc');

    // Lắng nghe dialog xác nhận phát thuốc
    page.once('dialog', async dialog => {
      console.log(`Xử lý dialog phát thuốc: ${dialog.message()}`);
      await dialog.accept();
    });

    // Bấm nút "Xác nhận đã phát thuốc" (check button)
    const dispenseBtn = prescriptionRow.locator('button[title="Xác nhận đã phát thuốc"]').first();
    await dispenseBtn.click();

    // Đợi thông báo cấp phát thuốc thành công
    const successDispenseAlert = page.locator('.alert-success');
    await expect(successDispenseAlert).toBeVisible({ timeout: 15000 });
    await expect(successDispenseAlert).toContainText('Đã xác nhận lấy thuốc thành công!');

    // Xác nhận badge chuyển sang "Đã lấy thuốc"
    await expect(statusBadge).toContainText('Đã lấy thuốc');
    console.log(`Dược sĩ đã cấp phát thuốc thành công cho đơn thuốc của bệnh nhân "${testFullName}" (${patientCode})!`);

    console.log('=== KIỂM THỬ KHÉP KÍN E2E HOÀN THÀNH MỸ MÃN! ===');
  });

});

const { test, expect } = require('@playwright/test');

test.describe('Thanh Toán Hóa Đơn (Invoice Payment Flow)', () => {

  test('Thực hiện thanh toán hóa đơn đã có sẵn thành công', async ({ page }) => {
    // Thời gian chờ tối đa 60 giây do chạy trên DB remote
    test.setTimeout(60000);

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

    // Đợi đăng nhập thành công
    await page.waitForURL(/.*(consultations|dashboard|home)/, { timeout: 20000 });

    // 2. Điều hướng tới Trang Thu Ngân - Quản Lý Hóa Đơn
    await page.goto('/invoices');

    // Đợi bảng hóa đơn chờ thanh toán xuất hiện
    await page.waitForSelector('tbody#pending-table-body', { timeout: 15000 });

    // 3. Tìm và lấy thông tin của hóa đơn đầu tiên trong danh sách chờ thanh toán
    const firstPendingRow = page.locator('tbody#pending-table-body tr').first();
    
    // Kiểm tra xem có hóa đơn nào đang chờ hay không
    const isEmpty = await firstPendingRow.locator('td#empty-pending').count() > 0;
    if (isEmpty) {
      console.log('Hiện tại không có hóa đơn nào trong hàng đợi chờ thanh toán.');
      return; // Kết thúc sớm nếu không có dữ liệu test
    }

    const invoiceCodeText = await firstPendingRow.locator('td:nth-child(1)').textContent();
    const patientNameText = await firstPendingRow.locator('td:nth-child(3) span.fw-bold').textContent();
    
    const invoiceCode = invoiceCodeText ? invoiceCodeText.trim() : 'Không rõ';
    const patientName = patientNameText ? patientNameText.trim() : 'Không rõ';
    console.log(`Tiến hành thanh toán cho hóa đơn: ${invoiceCode} - Bệnh nhân: ${patientName}`);

    // Click nút "Xác nhận thanh toán" (nút màu xanh lá có title="Xác nhận thanh toán")
    const payBtn = firstPendingRow.locator('a[title="Xác nhận thanh toán"]').first();
    await payBtn.click();

    // 4. Đợi điều hướng sang trang chi tiết hóa đơn
    await page.waitForURL(/\/invoices\/\d+\/show/, { timeout: 15000 });

    // Kiểm tra xem trang chi tiết hóa đơn hiển thị đúng thông tin bệnh nhân và mã hóa đơn
    const patientNameOnInvoice = page.locator('.inv-patient-name');
    await expect(patientNameOnInvoice).toContainText(patientName);

    // 5. Kiểm tra trạng thái hóa đơn để tránh xung đột concurrent
    const paymentFormSelect = page.locator('select[name="payment_method"]');
    
    if (await paymentFormSelect.count() > 0 && await paymentFormSelect.isVisible()) {
      console.log(`Hóa đơn ${invoiceCode} chưa thanh toán. Tiến hành chọn phương thức và thanh toán...`);
      
      // Chọn phương thức thanh toán là "Cash" (Tiền mặt)
      await paymentFormSelect.selectOption('Cash');

      // Đăng ký bộ lắng nghe hộp thoại xác nhận (dialog) để tự động bấm OK
      page.once('dialog', async dialog => {
        console.log(`Xử lý hộp thoại confirm: ${dialog.message()}`);
        expect(dialog.message()).toContain('Xác nhận thanh toán');
        await dialog.accept();
      });

      // Bấm nút "Thanh toán" màu xanh lá ở phía dưới
      const confirmPayBtn = page.locator('button[type="submit"]:has-text("Thanh toán")');
      await confirmPayBtn.click();
    } else {
      // Hóa đơn đã được thanh toán trước khi trang load xong hoặc đã được thanh toán trước đó
      console.log(`[Bỏ qua] Hóa đơn ${invoiceCode} của bệnh nhân ${patientName} đã ở trạng thái thanh toán từ trước.`);
    }

    // 6. Xác nhận kết quả thanh toán thành công (Form thanh toán biến mất và hiển thị thông tin thanh toán)
    const paidInfo = page.locator('.inv-patient-row:has-text("Thanh toán:")');
    await expect(paidInfo).toBeVisible({ timeout: 20000 });

    const paymentForm = page.locator('form[action*="/pay"]');
    await expect(paymentForm).not.toBeVisible();

    console.log(`[Thành công] Hóa đơn ${invoiceCode} của bệnh nhân ${patientName} đã được ghi nhận thanh toán thành công.`);
  });

});

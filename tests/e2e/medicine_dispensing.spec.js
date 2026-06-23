const { test, expect } = require('@playwright/test');

test.describe('Cấp Phát Thuốc (Medicine Dispensing Flow)', () => {

  test('Thực hiện cấp phát thuốc cho đơn thuốc thành công', async ({ page }) => {
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

    // 2. Đi tới trang Danh Sách Toa Thuốc
    await page.goto('/prescriptions');
    await page.waitForSelector('tbody tr', { timeout: 15000 });

    // 3. Lấy thông tin của đơn thuốc đầu tiên trên danh sách
    const firstRow = page.locator('tbody tr').first();
    const isEmpty = await firstRow.locator('td').count() <= 1;
    if (isEmpty) {
      console.log('Không có đơn thuốc nào trên hệ thống. Kết thúc test sớm.');
      return;
    }

    const idTextText = await firstRow.locator('td:nth-child(1)').textContent();
    const patientNameText = await firstRow.locator('td:nth-child(4) span.fw-bold').textContent();
    const statusTextText = await firstRow.locator('td:nth-child(5) span.badge').textContent();
    
    const prescriptionId = idTextText ? idTextText.trim() : '';
    const patientName = patientNameText ? patientNameText.trim() : '';
    const statusText = statusTextText ? statusTextText.trim() : '';
    console.log(`Đơn thuốc chọn thử nghiệm: ID=${prescriptionId}, Bệnh nhân="${patientName}", Trạng thái="${statusText}"`);

    if (!patientName || patientName === 'Không rõ') {
      throw new Error('Không lấy được tên bệnh nhân hợp lệ từ đơn thuốc!');
    }

    // 4. Kiểm tra trạng thái đơn thuốc để thực hiện xử lý phù hợp
    if (statusText.includes('Đã lấy thuốc')) {
      // TRƯỜNG HỢP A: Đơn thuốc đã phát -> Hủy phát rồi phát lại (Đơn này chắc chắn hóa đơn đã thanh toán)
      console.log(`Đơn thuốc ${prescriptionId} đã phát. Thực hiện Hủy phát thuốc để kiểm thử...`);
      const undoBtn = firstRow.locator('button[title="Hủy xác nhận đã phát thuốc"]').first();

      page.once('dialog', async dialog => {
        console.log(`Xử lý dialog hủy phát thuốc: ${dialog.message()}`);
        await dialog.accept();
      });

      await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle', timeout: 15000 }).catch(() => {}),
        undoBtn.click()
      ]);

      // Tìm lại đơn thuốc sau khi reload và xác nhận phát lại
      await page.fill('#searchKeyword', patientName);
      await page.waitForTimeout(1000);

      const refreshedRow = page.locator('tbody tr').first();
      const dispenseBtn = refreshedRow.locator('button[title="Xác nhận đã phát thuốc"]').first();
      
      page.once('dialog', async dialog => {
        console.log(`Xử lý dialog phát lại thuốc: ${dialog.message()}`);
        await dialog.accept();
      });

      await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle', timeout: 15000 }).catch(() => {}),
        dispenseBtn.click()
      ]);

      const successDispenseAlert = page.locator('.alert-success');
      await expect(successDispenseAlert).toBeVisible({ timeout: 15000 });
      await expect(successDispenseAlert).toContainText('Đã xác nhận lấy thuốc thành công!');
      console.log(`[Thành công] Đã cấp phát lại đơn thuốc ${prescriptionId} thành công.`);
      
    } else {
      // TRƯỜNG HỢP B: Chưa lấy thuốc -> Đi thanh toán hóa đơn cho bệnh nhân này trước để thỏa mãn ràng buộc
      console.log(`Đơn thuốc ${prescriptionId} chưa lấy. Đi thanh toán hóa đơn cho bệnh nhân "${patientName}" trước...`);
      
      // Điều hướng sang trang Hóa đơn
      await page.goto('/invoices');
      await page.waitForSelector('tbody#pending-table-body', { timeout: 15000 });

      // Tìm hóa đơn của bệnh nhân này
      const invoiceRow = page.locator(`tbody#pending-table-body tr:has-text("${patientName}")`).first();
      
      if (await invoiceRow.count() > 0 && await invoiceRow.isVisible()) {
        const payBtn = invoiceRow.locator('a[title="Xác nhận thanh toán"]').first();
        await payBtn.click();

        await page.waitForURL(/\/invoices\/\d+\/show/, { timeout: 15000 });
        
        // Chọn phương thức Cash và submit thanh toán
        await page.selectOption('select[name="payment_method"]', 'Cash');

        page.once('dialog', async dialog => {
          await dialog.accept();
        });

        const confirmPayBtn = page.locator('button[type="submit"]:has-text("Thanh toán")');
        await confirmPayBtn.click();

        // Chờ tải lại trang sau khi thanh toán
        const paidInfo = page.locator('.inv-patient-row:has-text("Thanh toán:")');
        await expect(paidInfo).toBeVisible({ timeout: 15000 });
        console.log(`Đã thanh toán hóa đơn cho bệnh nhân "${patientName}" thành công.`);
      } else {
        console.log(`Không tìm thấy hóa đơn chờ thanh toán cho bệnh nhân "${patientName}". Giả định hóa đơn đã được thanh toán từ trước.`);
      }

      // Quay lại trang đơn thuốc để tiến hành cấp phát
      await page.goto('/prescriptions');
      await page.fill('#searchKeyword', patientName);
      await page.waitForTimeout(1000);

      const refreshedRow = page.locator('tbody tr').first();
      const dispenseBtn = refreshedRow.locator('button[title="Xác nhận đã phát thuốc"]').first();
      
      page.once('dialog', async dialog => {
        console.log(`Xử lý dialog phát thuốc: ${dialog.message()}`);
        await dialog.accept();
      });

      await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle', timeout: 15000 }).catch(() => {}),
        dispenseBtn.click()
      ]);

      const successDispenseAlert = page.locator('.alert-success');
      await expect(successDispenseAlert).toBeVisible({ timeout: 15000 });
      await expect(successDispenseAlert).toContainText('Đã xác nhận lấy thuốc thành công!');
      console.log(`[Thành công] Đã cấp phát thuốc thành công cho đơn của bệnh nhân "${patientName}".`);
    }

    console.log(`Hoàn thành kiểm thử cấp phát thuốc thành công!`);
  });

});

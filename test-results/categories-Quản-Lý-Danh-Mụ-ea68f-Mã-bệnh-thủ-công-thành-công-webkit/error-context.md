# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: categories.spec.js >> Quản Lý Danh Mục (Category Management) >> Tạo mới Mã bệnh thủ công thành công
- Location: tests\e2e\categories.spec.js:110:3

# Error details

```
TimeoutError: page.waitForURL: Timeout 20000ms exceeded.
=========================== logs ===========================
waiting for navigation until "load"
============================================================
```

# Page snapshot

```yaml
- generic [ref=e3]:
  - banner [ref=e4]:
    - heading "Sign in to your account" [level=1] [ref=e5]
  - generic [ref=e10]:
    - generic [ref=e11]:
      - generic [ref=e12]: Username or email
      - textbox "Username or email" [ref=e13]: bacsi_admin
    - generic [ref=e14]:
      - generic [ref=e15]: Password
      - generic [ref=e16]:
        - textbox "Password" [ref=e17]: "123456"
        - button "Show password" [ref=e18] [cursor=pointer]:
          - generic [ref=e19]: 
    - generic [ref=e20]:
      - button "Sign In" [disabled]
```

# Test source

```ts
  1   | const { test, expect } = require('@playwright/test');
  2   | 
  3   | // Helper function to log in as bacsi_admin via Keycloak OIDC
  4   | async function loginAsAdmin(page) {
  5   |   await page.goto('/login');
  6   |   
  7   |   // Check if we are already logged in (redirected to home, dashboard, or consultations)
  8   |   if (page.url().includes('home') || page.url().includes('dashboard') || page.url().includes('consultations')) {
  9   |     return;
  10  |   }
  11  |   
  12  |   const loginBtn = page.locator('#loginBtn');
  13  |   if (await loginBtn.count() > 0) {
  14  |     await loginBtn.click();
  15  |     await page.waitForSelector('#username', { timeout: 15000 });
  16  |     await page.fill('#username', 'bacsi_admin');
  17  |     await page.fill('#password', '123456');
  18  | 
  19  |     const kcLoginBtn = page.locator('#kc-login');
  20  |     if (await kcLoginBtn.count() > 0) {
  21  |       await kcLoginBtn.click();
  22  |     } else {
  23  |       await page.click('input[type="submit"]');
  24  |     }
  25  | 
  26  |     // Wait for redirection back to the app dashboard or home
> 27  |     await page.waitForURL(/.*(consultations|dashboard|home)/, { timeout: 20000 });
      |                ^ TimeoutError: page.waitForURL: Timeout 20000ms exceeded.
  28  |   }
  29  | }
  30  | 
  31  | test.describe('Quản Lý Danh Mục (Category Management)', () => {
  32  | 
  33  |   test('Tạo mới Loại thuốc thủ công thành công', async ({ page }) => {
  34  |     test.setTimeout(90000);
  35  |     await loginAsAdmin(page);
  36  | 
  37  |     // Đi tới trang thêm loại thuốc
  38  |     await page.goto('/medicine-categories/create');
  39  | 
  40  |     const uniqueId = Date.now();
  41  |     const catName = `Loại Thuốc Test ${uniqueId}`;
  42  |     const description = `Mô tả cho Loại thuốc Test ${uniqueId}`;
  43  | 
  44  |     // Điền thông tin form
  45  |     await page.fill('input[name="category_name"]', catName);
  46  |     await page.fill('textarea[name="description"]', description);
  47  |     await page.selectOption('select[name="status"]', '1');
  48  | 
  49  |     // Lưu danh mục
  50  |     await page.click('button[type="submit"]:has-text("Lưu Danh Mục")');
  51  | 
  52  |     // Đợi điều hướng về trang danh sách
  53  |     await page.waitForURL(/\/medicine-categories/, { timeout: 60000 });
  54  | 
  55  |     // Kiểm tra thông báo thành công
  56  |     const successAlert = page.locator('.alert-success');
  57  |     if (await successAlert.count() > 0) {
  58  |       await expect(successAlert).toBeVisible();
  59  |       await expect(successAlert).toContainText('thành công');
  60  |     }
  61  | 
  62  |     // Xác nhận loại thuốc vừa tạo xuất hiện trong bảng danh sách (chỉ tìm trong thẻ td để tránh trùng với alert)
  63  |     const nameLocator = page.locator(`td:has-text("${catName}")`).first();
  64  |     await expect(nameLocator).toBeVisible();
  65  |   });
  66  | 
  67  |   test('Tạo mới Thuốc thủ công thành công', async ({ page }) => {
  68  |     test.setTimeout(90000);
  69  |     await loginAsAdmin(page);
  70  | 
  71  |     // Đi tới trang tạo thuốc
  72  |     await page.goto('/medicines/create');
  73  | 
  74  |     const uniqueId = Date.now();
  75  |     const medicineName = `Thuốc Test ${uniqueId}`;
  76  |     const note = `Ghi chú cho Thuốc Test ${uniqueId}`;
  77  | 
  78  |     // Điền thông tin thuốc
  79  |     await page.fill('input[name="medicine_name"]', medicineName);
  80  |     
  81  |     // Chọn nhóm thuốc ngẫu nhiên/đầu tiên có sẵn trong dropdown (không dùng toBeVisible vì option bị ẩn)
  82  |     const catSelect = page.locator('select[name="medicine_category_id"]');
  83  |     const firstOption = catSelect.locator('option:not([disabled]):not([value=""])').first();
  84  |     if (await firstOption.count() > 0) {
  85  |       const catValue = await firstOption.getAttribute('value');
  86  |       await catSelect.selectOption(catValue);
  87  |     }
  88  | 
  89  |     await page.fill('textarea[name="note"]', note);
  90  |     await page.selectOption('select[name="status"]', '1');
  91  | 
  92  |     // Nhấp nút Tạo thuốc
  93  |     await page.click('button[type="submit"]:has-text("Tạo Thuốc")');
  94  | 
  95  |     // Đợi chuyển hướng tới trang chi tiết thuốc (/medicines/{id})
  96  |     await page.waitForURL(/\/medicines\/\d+/, { timeout: 60000 });
  97  | 
  98  |     // Kiểm tra thông báo thành công
  99  |     const successAlert = page.locator('.alert-success');
  100 |     if (await successAlert.count() > 0) {
  101 |       await expect(successAlert).toBeVisible();
  102 |       await expect(successAlert).toContainText('thành công');
  103 |     }
  104 | 
  105 |     // Xác nhận tên thuốc xuất hiện trong giao diện chi tiết
  106 |     const nameHeader = page.locator(`h3:has-text("${medicineName}"), h2:has-text("${medicineName}"), div:has-text("${medicineName}")`).first();
  107 |     await expect(nameHeader).toBeVisible();
  108 |   });
  109 | 
  110 |   test('Tạo mới Mã bệnh thủ công thành công', async ({ page }) => {
  111 |     test.setTimeout(90000);
  112 |     await loginAsAdmin(page);
  113 | 
  114 |     // Đi tới trang tạo mã bệnh
  115 |     await page.goto('/diagnoses/create');
  116 | 
  117 |     const uniqueId = Date.now();
  118 |     // diagnosis_code tối đa 15 ký tự, ví dụ: TESTA01
  119 |     const diagnosisCode = `T${String(uniqueId).substring(7)}`; // 1 chữ cái + 6 số
  120 |     const diagnosisName = `Bệnh Test ${uniqueId}`;
  121 |     const description = `Mô tả bệnh Test ${uniqueId}`;
  122 | 
  123 |     // Điền thông tin mã bệnh
  124 |     await page.fill('input[name="diagnosis_code"]', diagnosisCode);
  125 |     await page.fill('input[name="diagnosis_name"]', diagnosisName);
  126 |     await page.fill('input[name="icd_version"]', 'ICD-10');
  127 |     await page.fill('textarea[name="description"]', description);
```
const { defineConfig, devices } = require('@playwright/test');

/**
 * @see https://playwright.dev/docs/test-configuration
 */
module.exports = defineConfig({
  testDir: './tests/e2e',
  /* Chạy các test song song */
  fullyParallel: true,
  /* Bị lỗi nếu lỡ quên fdescribe/fit trên CI */
  forbidOnly: !!process.env.CI,
  /* Thử lại nếu test bị lỗi (trên CI thử 2 lần, local 0 lần) */
  retries: process.env.CI ? 2 : 0,
  /* Số lượng worker chạy song song */
  workers: process.env.CI ? 1 : undefined,
  /* Định dạng báo cáo kiểm thử */
  reporter: 'html',
  /* Cấu hình chung cho mọi browser project */
  use: {
    /* Base URL để chạy test (mặc định lấy theo URL chạy server local) */
    baseURL: process.env.PLAYWRIGHT_BASE_URL || 'http://localhost:8000',

    /* Thu thập trace khi chạy test lỗi phục vụ debug */
    trace: 'on-first-retry',

    /* Tự động chụp ảnh khi test bị lỗi */
    screenshot: 'only-on-failure',
  },

  /* Cấu hình các trình duyệt chạy test */
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'firefox',
      use: { ...devices['Desktop Firefox'] },
    },
    {
      name: 'webkit',
      use: { ...devices['Desktop Safari'] },
    },
  ],
});

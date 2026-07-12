# Báo cáo Kết quả Triển khai E2E Test với Playwright

Tài liệu này tóm tắt toàn bộ công việc thiết lập và viết kịch bản kiểm thử giao diện tự động (E2E) bằng Playwright cho hệ thống quản lý danh mục phòng khám.

## 1. File kiểm thử đã tạo
- **Đường dẫn**: `tests/e2e/categories.spec.js`
- **Mục tiêu**: Tự động hóa luồng đăng nhập (Keycloak OIDC) và kiểm thử giao diện chức năng thêm mới thủ công cho các danh mục cốt lõi.

---

## 2. Danh sách kịch bản kiểm thử (6 test cases)

1. **Tạo mới Loại thuốc thủ công**
   - Nhập tên và mô tả nhóm thuốc.
   - Bấm lưu và xác nhận hiển thị chính xác tên loại thuốc vừa tạo trong danh sách.

2. **Tạo mới Thuốc thủ công**
   - Nhập tên thương mại của thuốc.
   - Chọn loại thuốc (tự động lấy nhóm khả dụng đầu tiên từ dropdown).
   - Lưu và kiểm tra chuyển hướng thành công đến trang hiển thị thông tin chi tiết thuốc (`/medicines/{id}/show`).

3. **Tạo mới Mã bệnh thủ công**
   - Điền thông tin mã bệnh (ICD), tên bệnh, phiên bản ICD và mô tả.
   - Lưu và xác nhận hiển thị chính xác mã bệnh vừa tạo trong danh sách bệnh.

4. **Tạo mới Khoa thủ công**
   - Điền mã khoa, tên khoa và mô tả khoa.
   - Lưu và xác nhận hiển thị chính xác tên khoa vừa tạo trong danh sách.

5. **Tạo mới Phòng thủ công**
   - Điền mã phòng, tên phòng, chọn loại phòng và liên kết với khoa có sẵn.
   - Lưu và xác nhận hiển thị chính xác tên phòng vừa tạo trong danh sách.

6. **Thêm chi tiết thuốc cho thuốc vừa tạo**
   - Tạo mới thuốc $\rightarrow$ Lấy ID thuốc từ URL chuyển hướng của trang hiển thị.
   - Đi tới trang quản lý chi tiết đơn giá của thuốc đó (`/medicines/{id}/details`).
   - Thêm đơn vị tính (`Viên`), giá bán (`5000`), hoạt chất (`Paracetamol 500mg`) và điều kiện bảo quản.
   - Bấm **"Tạo và Áp dụng ngay"** $\rightarrow$ Xác nhận hệ thống hiển thị đúng đơn vị tính và giá tiền đã định dạng (`5.000 đ`) trên bảng.

---

## 3. Các lỗi kỹ thuật đã giải quyết trong quá trình viết test

* **Lỗi trùng lặp locator (Strict mode violation)**:
  - *Nguyên nhân*: Lệnh kiểm tra sự xuất hiện của chuỗi text tên danh mục tìm thấy cả ở thông báo alert thành công và dòng trong bảng dữ liệu.
  - *Giải pháp*: Thu hẹp phạm vi định vị phần tử bằng cách chỉ tìm kiếm trong thẻ `td` (`page.locator('td:has-text(...)').first()`).

* **Lỗi tương tác Dropdown Option**:
  - *Nguyên nhân*: Assert phần tử `<option>` bằng `toBeVisible()` bị lỗi do Playwright coi các option là ẩn khi dropdown chưa được click mở rộng.
  - *Giải pháp*: Bỏ assertion hiển thị trực tiếp và thay thế bằng việc kiểm tra số lượng phần tử khả dụng (`count() > 0`) trước khi select.

* **Lỗi 404 khi truy cập trang chi tiết thuốc**:
  - *Nguyên nhân*: Trích xuất ID thuốc bằng cách sử dụng `.split('/').pop()` trên URL `/medicines/{id}/show` trả về chuỗi `"show"` thay vì ID số, dẫn đến điều hướng sai URL.
  - *Giải pháp*: Dùng URL Parser để trích xuất chuẩn xác phân đoạn path đứng liền sau từ khóa `"medicines"`.

* **Tối ưu hóa thời gian chờ (Timeout)**:
  - *Nguyên nhân*: Quá trình đăng nhập và chuyển hướng trung gian qua Keycloak OIDC thỉnh thoảng bị chậm trên Webkit (Safari).
  - *Giải pháp*: Nâng thời gian chờ đợi phản hồi (`waitForURL`) lên `40000ms`.

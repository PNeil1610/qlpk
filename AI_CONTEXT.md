# AI_CONTEXT.md - Bản đồ Tư duy Nghiệp vụ & Kỹ thuật Dự án QLPK

Tài liệu này được thiết kế để cung cấp cái nhìn toàn diện về hệ thống **Quản lý Phòng khám (QLPK)**, giúp các AI assistant hiểu nhanh cấu trúc và luồng hoạt động mà không cần quét lại toàn bộ codebase.

---

# 1. Tổng quan Dự án & Phạm vi Nghiệp vụ (Domain Overview)
- **Mục tiêu:** Tối ưu hóa quy trình khám chữa bệnh tại phòng khám đa khoa, từ tiếp đón, khám lâm sàng đến kê đơn và thanh toán.
- **Các Module chính:**
    - **Quản lý Tiếp đón:** Đăng ký bệnh nhân mới, lập phiếu khám, phân bổ vào các phòng chức năng.
    - **Quản lý Khám bệnh (EMR):** Bác sĩ ghi nhận triệu chứng, chẩn đoán (ICD-10), chỉ định cận lâm sàng và kê đơn thuốc.
    - **Quản lý Dược phẩm:** Danh mục thuốc, đơn giá theo phiên bản, quản lý đơn vị tính.
    - **Quản lý Tài chính:** Tra cứu hóa đơn, tính toán chi phí khám + thuốc, xác nhận thanh toán.
    - **Hệ thống Phân quyền (RBAC):** Quản lý nhân sự và quyền hạn truy cập theo vai trò.

# 2. Tech Stack & Infrastructure
- **Ngôn ngữ & Framework:** PHP 8.1+, **Laravel 10** (MVC Architecture).
- **Cơ sở dữ liệu:** **MySQL** (Sử dụng Eloquent ORM để quản lý quan hệ dữ liệu).
- **Frontend:** Laravel Blade Template, Bootstrap 5, Vite, jQuery, Select2, Google Font **Plus Jakarta Sans**, CSS Premium chuyên dụng (`custom-premium-style.css`).
- **Containerization:** **Laravel Sail** (Docker Compose) cung cấp môi trường phát triển đồng bộ.
- **Kiến trúc:** Monolithic Web Application với RESTful Web Routes.

# 3. Data Models & Database Schema (Quan trọng)
Hệ thống sử dụng cơ sở dữ liệu quan hệ (RDBMS) với các thực thể chính:

- **Users:** Lưu thông tin tài khoản đăng nhập và phân quyền.
- **Staff:** Thông tin chi tiết nhân viên (Bác sĩ, Y tá...), liên kết 1-1 với `Users`. Có `room_id` để xác định vị trí làm việc.
- **Patient:** Hồ sơ bệnh nhân, liên kết với `Users` (nếu bệnh nhân có tài khoản).
- **Consultation (Phiên khám):** Thực thể trung tâm, liên kết `Patient`, `Diagnosis` (Mã bệnh), `Room` (Phòng khám), và `Staff` (Bác sĩ thực hiện qua `doctor_id`). 
    - *Mối quan hệ:* 1 Bệnh nhân có nhiều Phiên khám; 1 Phiên khám có 1 Đơn thuốc; 1 Phiên khám có 1 Hóa đơn.
    - *Lưu ý:* `doctor_id` được tự động set vào bảng `consultation` khi bác sĩ nhấn "Lưu Kết Luận" từ giao diện khám bệnh.
- **Room & Department:** Quản lý cơ cấu tổ chức phòng ban và phòng khám.
- **Prescription & PrescriptionDetail:** Thông tin đơn thuốc và chi tiết từng loại thuốc (số lượng, liều dùng).
- **Invoice & InvoiceDetail:** Thông tin thanh toán, tự động tổng hợp chi phí từ Phiên khám và Đơn thuốc.
- **Medicine & MedicineDetail:** Quản lý danh mục thuốc và lịch sử giá bán (Versioning qua `MedicineDetail`).

# 4. Phân Quyền Hệ Thống (RBAC)
Hệ thống quản lý quyền qua bảng `roles` và `permissions` thông qua Middleware:
- **ADMIN:** Toàn quyền hệ thống.
- **DOCTOR:** Xem danh sách chờ tại phòng được chỉ định, thực hiện khám bệnh, kê đơn thuốc.
- **RECEPTIONIST:** Tiếp đón bệnh nhân, tạo mới phiếu khám, xếp phòng.
- **CASHIER:** Tra cứu và thu phí hóa đơn.
- **PATIENT:** Xem hồ sơ bệnh án cá nhân và lịch sử khám.

# 5. Chi tiết Cấu trúc Thư mục
```text
qlpk/
├── app/
│   ├── Http/Controllers/   # Xử lý Logic nghiệp vụ (Consultation, Invoice, Patient...)
│   ├── Http/Middleware/    # Kiểm tra quyền (RoleMiddleware) và xác thực.
│   ├── Models/             # Định nghĩa Schema và Quan hệ (Eloquent).
│   └── Providers/          # Đăng ký các dịch vụ hệ thống.
├── config/                 # Cấu hình Database, Auth, App...
├── database/
│   ├── migrations/         # Định nghĩa cấu trúc các bảng MySQL.
│   └── seeders/            # Dữ liệu mẫu (Roles, Admin, Medicine Categories).
├── resources/
│   ├── views/              # Giao diện Blade (Doctor dashboard, Billing, Admin).
│   └── js/css/             # Assets quản lý bởi Vite.
├── routes/
│   ├── web.php             # Các route chung (Auth, Home, Profile).
│   ├── admin.php           # Quản trị (CRUD) cho ADMIN và RECEPTIONIST.
│   ├── doctor.php          # Nghiệp vụ khám bệnh của DOCTOR.
│   ├── cashier.php         # Nghiệp vụ thanh toán của CASHIER.
│   └── patient.php         # Tự phục vụ cho PATIENT.
└── docker-compose.yml      # Cấu hình các dịch vụ Docker (Sail).
```

# 6. Breakdown Code Cốt Lõi (Core File Analysis)

- **resources/views/auth/login.blade.php:** Giao diện đăng nhập được thiết kế lại độc lập với layout chính, mang phong cách hiện đại, trực quan và tối ưu cho UI/UX hệ thống phòng khám.
- **resources/views/layouts/app.blade.php:** Layout nền tảng được cấu trúc lại hoàn toàn cho Responsive (Sidebar cố định ở desktop `>= 992px`, và Hamburger Offcanvas Sidebar Drawer ở di động `< 992px`). Tích hợp Google Font `Plus Jakarta Sans` và liên kết file CSS Premium.
- **public/css/custom-premium-style.css:** Định nghĩa hệ thống theme Premium (bo góc mềm mại `12px` - `16px`, soft shadow). Cấu hình cuộn ngang cho bảng (`overflow-x: auto`) và triệt tiêu bóp nghẹt chữ/nút hành động (`white-space: nowrap !important`) cùng thanh scrollbar Indigo nổi bật để vuốt kéo dễ dàng trên di động.
- **app/Models/User.php:** Quản lý Auth và RBAC (hàm `hasRole`, `hasPermission`).
- **app/Http/Controllers/Admin/ConsultationController.php:** Quản lý phiên khám (CRUD) cho Admin.
- **app/Http/Controllers/Doctor/ExaminationController.php:** 
    - `index()`: Lọc bệnh nhân theo `room_id` của bác sĩ đang login.
    - `examine()`: Logic tích hợp cho phép bác sĩ vừa xem bệnh án vừa kê đơn thuốc trên cùng một view. Truyền `$history` (lịch sử khám của bệnh nhân) và `$doctorHistory` (lịch sử khám của bác sĩ) sang view. Lịch sử được hiển thị dạng 2 tab ở cột trái. Bác sĩ có thể nhấn "Xem" để chuyển sang trang chi tiết và sử dụng lại toa thuốc.
- **app/Http/Controllers/InvoiceController.php:** 
    - `lookup()`: Logic tra cứu đa năng (theo mã bệnh nhân, mã phiên khám hoặc SĐT).
    - `store()`: Tự động tính toán phí khám và phí thuốc dựa trên dữ liệu từ `Consultation`.
- **app/Models/Consultation.php:** Định nghĩa các trạng thái phiên khám (`STATUS_NEW`, `STATUS_PENDING`, `STATUS_COMPLETED`...) và logic chuyển trạng thái.
- **app/Http/Controllers/NotificationController.php:** Quản lý gửi nhận thông báo nội bộ cho nhân viên phòng khám.
- **resources/views/partials/sidebar.blade.php:** Tích hợp tính năng tự động đếm thông báo chưa đọc (`ajaxUnreadCount`) và hiển thị chấm đỏ (red dot badge) ngay tại icon cái chuông (`#notif-dot`) kết hợp hiển thị số lượng chưa đọc. Tối ưu hóa trải nghiệm bằng cách tải trực tiếp số lượng chưa đọc từ máy chủ (Server-side rendering qua Blade) khi tải trang để chấm đỏ xuất hiện tức thì, sau đó tiếp tục đồng bộ và cập nhật tự động mỗi 60 giây qua AJAX. Tương thích hoàn hảo cả khi làm Sidebar cố định lẫn khi nằm trong Offcanvas di động nhờ đồng bộ hóa các bộ lọc CSS.

# 7. Luồng Nghiệp vụ Cốt lõi (Core Business Flows)

### A. Luồng Tiếp đón & Xếp phòng
1. User (Receptionist) tạo bệnh nhân hoặc tra cứu bệnh nhân cũ (`PatientController@store`).
2. Tạo Phiếu khám (`ConsultationController@store`), chọn phòng khám mục tiêu.
3. Bản ghi được tạo trong bảng `consultation_room` với trạng thái `PENDING`.

### B. Luồng Khám bệnh & Kê đơn
1. Bác sĩ đăng nhập, vào Dashboard phòng mình (`doctor/my-consultations`).
2. Chọn bệnh nhân đang chờ -> Vào giao diện Khám bệnh (`doctor/examine.blade.php`).
3. Giao diện chia 2 cột:
    - **Cột trái:** Thông tin hành chính + Toa thuốc hiện tại đã kê + **Lịch sử khám bệnh** (danh sách phiên cũ của BN).
    - **Cột phải:** Form kết luận chuyên môn + Form kê đơn thuốc mới.
        - **Giao diện kê đơn:** Thiết kế dạng lưới gọn gàng (compact 1 dòng/thuốc). Toàn bộ logic JS được tách riêng ra `public/js/prescription-form.js`.
        - **Tìm kiếm thông minh:** Sử dụng 2 ô Select2 AJAX liên hoàn (khi chọn "Nhóm thuốc", ô "Tên thuốc" tự động lọc và xổ ra danh sách tương ứng, hoặc gõ chữ để tìm trực tiếp).
        - **Cấu hình liều dùng:** Nhập riêng lẻ liều theo 4 buổi (Sáng, Trưa, Chiều, Tối) + Số ngày uống -> Hệ thống **tự động tính Tổng SL**. Có nút bấm chọn nhanh thời điểm uống (Trước, Trong, Sau ăn).
        - **Auto-fill:** Khi nhấn "Thêm thuốc", tự động copy "Nhóm thuốc" và "Số ngày" từ dòng liền trước, giúp bác sĩ thao tác rảnh tay, kê đơn hàng loạt cực nhanh.
4. **Xem lịch sử & Sử dụng lại toa thuốc:**
    - Ở cột lịch sử, hệ thống hiển thị **2 tab**:
        - **Tab "Lịch sử BN"**: Tất cả các phiếu khám đã hoàn thành của bệnh nhân hiện tại ở MỌI phòng, kèm tên bác sĩ đã khám.
        - **Tab "Lịch sử của tôi"**: Tất cả các phiếu khám mà bác sĩ đang login đã thực hiện (theo `doctor_id`) ở mọi phòng khác nhau, kèm thông tin bệnh nhân.
    - Bác sĩ nhấn link "Phiếu #xxx" tại một phiếu khám cũ -> Hệ thống chuyển trang sang `consultations/show.blade.php` (cùng tab) với tham số `?reuse_mode=1`.
    - Trang chi tiết hiển thị đầy đủ thông tin phiên khám cũ bao gồm danh sách thuốc đã kê, chẩn đoán và tên bác sĩ thực hiện để cung cấp ngữ cảnh rõ ràng.
    - Bác sĩ nhấn **"Sử dụng lại toa thuốc"** -> Dữ liệu thuốc và lời dặn được lưu vào `localStorage` -> Hệ thống tự động quay lại trang khám bệnh.
    - Trang khám bệnh khi load phát hiện dữ liệu trong `localStorage`, tự động điền vào form kê đơn và cuộn xuống phần kê đơn.
5. Nhập triệu chứng, chẩn đoán và lưu (`ConsultationController@update`).
6. Kết thúc khám: Phiên khám chuyển trạng thái sang "Chờ thanh toán" (`WAIT_PAYMENT`).

### C. Luồng Thanh toán & Hoàn thành
1. Thu ngân tra cứu phiên khám/bệnh nhân (`InvoiceController@lookup`).
2. Hệ thống tổng hợp `consultation_fee` + `medicine_fee`.
3. Thu ngân xác nhận thanh toán (`pay()`).
4. **Hậu xử lý:** Cập nhật Hóa đơn thành `PAID` và Phiên khám thành `COMPLETED`.

### D. Luồng Thông báo nội bộ
1. Hệ thống cho phép gửi thông báo nội bộ tới toàn bộ nhân viên (All), theo nhóm vai trò (Role), hoặc cá nhân cụ thể (User).
2. Khi tải trang, hệ thống lập tức kiểm tra số lượng thông báo chưa đọc và hiển thị chấm đỏ trực tiếp trên quả chuông của sidebar (Server-side rendering), tránh hiện tượng trễ/nháy giao diện. Số lượng chưa đọc cụ thể cũng được hiển thị dạng badge bên phải chữ "Thông báo".
3. Sidebar thiết lập luồng polling cứ mỗi 60 giây qua AJAX để kiểm tra các thông báo mới phát sinh trong phiên hoạt động của người dùng.
4. Khi nhân viên click xem chi tiết thông báo (`NotificationController@show`), hệ thống tự động ghi nhận là đã đọc (`NotificationRead::firstOrCreate`) và đồng bộ cập nhật ẩn chấm đỏ/cập nhật số lượng trên sidebar ngay lập tức qua cơ chế AJAX.

# 8. Môi trường & Triển khai (Docker Setup)
- **Docker Services (via Sail):**
    - `laravel.test`: Chạy PHP 8.2/8.1 và Nginx/Swoole.
    - `mysql`: Database chính.
    - `redis`: Caching và Session.
    - `mailpit`: Testing email.
- **Biến môi trường (.env):**
    - `DB_CONNECTION=mysql`
    - `DB_DATABASE=qlpk`
    - `APP_KEY`: Khóa bảo mật Laravel.
    - `VITE_*`: Các biến cấu hình cho frontend build.

# 9. Lưu ý Kỹ thuật & Các lỗi thường gặp (Troubleshooting)

- **Định dạng Ngày tháng:** Luôn đảm bảo các trường ngày tháng (`consultation_date`, `prescribed_at`, `invoice_date`) được khai báo trong thuộc tính `$casts` của Model là `datetime` hoặc `date`.
- **Lỗi format() trên String:** Khi hiển thị ngày tháng ở View, nếu không chắc chắn Model đã cast dữ liệu, hãy sử dụng `\Carbon\Carbon::parse($date)->format(...)` để tránh lỗi "Call to a member function format() on string".
- **Routing Domain-driven:** Routing đã được tách thành `web.php` (chung), `admin.php`, `doctor.php`, `cashier.php`, và `patient.php` để dễ bảo trì và áp dụng middleware riêng theo vai trò, giải quyết lỗi liên quan đến phân quyền.
- **Xem lịch sử & Tái sử dụng (Reuse):** Hệ thống hỗ trợ xem lịch sử trong cùng tab (điều hướng chuẩn) để đảm bảo trải nghiệm liền mạch. Khi bác sĩ chọn "Sử dụng lại", dữ liệu được lưu tạm vào `localStorage` và tự động điền vào form khi quay lại trang khám.
    - View liên quan: `doctor/examine.blade.php` (trang chính), `consultations/show.blade.php?reuse_mode=1` (trang chi tiết).
    - Dữ liệu lưu: `localStorage['reuse_prescription_data']` gồm `medicines[]`, `advice`, `timestamp`.
    - Dữ liệu tự xóa sau khi được áp dụng hoặc sau 5 phút nếu không được dùng đến.
- **Tích hợp Select2:** Hệ thống sử dụng Select2 AJAX cho việc tìm kiếm Mã bệnh (ICD-10) và Thuốc để tối ưu hiệu năng khi danh mục lớn.
- **Responsive & Premium UI Optimizations:**
    - **Khóa cột không co chữ trong bảng biểu:** Trên di động, để tránh nội dung bị trình duyệt tự ý bóp nghẹt làm vỡ chữ hay vỡ hàng nút bấm, hệ thống áp dụng `white-space: nowrap !important` kết hợp `overflow-x: auto` trên container `.table-responsive` giúp kéo vuốt qua lại vô cùng dễ dàng.
    - **Khối thông tin thu gọn di động:** Vùng xem chi tiết hồ sơ bệnh nhân ở cột trái của bác sĩ tự động thu vào Accordion `#leftColumnCollapse` khi ở màn hình nhỏ, giúp bác sĩ không phải cuộn nhiều.
    - **Kê đơn dạng thẻ dọc di động:** Phần kê đơn thuốc tự chuyển đổi linh hoạt từ hàng ngang dài sang cấu trúc Mini Card đứng trên di động/tablet để dễ chạm bấm ngón tay và có liều dùng dạng lưới thoáng đãng.

---
*Ghi chú: Luôn kiểm tra `routes/web.php` để nắm bắt các URL mới và `app/Models` để cập nhật các thay đổi về schema.*

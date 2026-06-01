# AI_CONTEXT.md - Bản đồ Nghiệp vụ & Kỹ thuật Chi tiết Dự án QLPK (Cẩm nang Hướng dẫn Hệ thống)

Tài liệu này được biên soạn siêu chi tiết, đóng vai trò là **nguồn thông tin chuẩn duy nhất (Single Source of Truth)** về hệ thống **Quản lý Phòng khám (QLPK)**. Tài liệu này cung cấp đầy đủ thông tin về Cơ sở dữ liệu, Danh sách Routes, Sơ đồ Phân quyền (RBAC), Quy trình Nghiệp vụ từng bước, Tài khoản Demo và các giải pháp UI/UX Premium phục vụ trực tiếp cho việc biên soạn tài liệu hướng dẫn sử dụng (User Manual) và tài liệu kỹ thuật (Technical Docs).

---

# 1. Tổng quan Hệ thống & Các Phân hệ (System Modules)

Hệ thống QLPK được thiết kế để số hóa toàn diện hoạt động vận hành của một phòng khám đa khoa/chuyên khoa, tối ưu hóa quy trình từ lúc bệnh nhân bước vào tiếp đón cho đến khi khám xong, nhận thuốc và thanh toán ra về.

*   **Module Tiếp Đón (Reception):** Quản lý hồ sơ bệnh nhân, đăng ký mới, tiếp nhận thông tin sinh hiệu sơ bộ (nhiệt độ, huyết áp), tạo phiếu khám (Consultation) và phân bổ (xếp hàng chờ) vào các phòng khám chuyên khoa phù hợp.
*   **Phân hệ Bác sĩ / Khám bệnh lâm sàng (Electronic Medical Record - EMR):** Giao diện thông minh hai cột dành cho Bác sĩ. Hỗ trợ xem hồ sơ bệnh án lịch sử, nhập triệu chứng, chẩn đoán bệnh theo mã chuẩn quốc tế **ICD-10**, chỉ định cận lâm sàng và thực hiện kê đơn thuốc điện tử.
*   **Phân hệ Quản lý Dược (Pharmacy):** Quản lý danh mục thuốc đa cấp (Medicine và MedicineDetail), kiểm soát đơn giá bán theo các phiên bản cập nhật, quản lý xuất kho/cấp phát thuốc cho bệnh nhân dựa trên đơn thuốc đã hoàn thành thanh toán.
*   **Phân hệ Tài chính / Thu ngân (Billing & Cashier):** Tự động tổng hợp chi phí khám, phí cận lâm sàng và tiền thuốc từ phiên khám để lập hóa đơn (Invoice). Hỗ trợ áp dụng phụ phí, chiết khấu và ghi nhận thanh toán đa phương thức.
*   **Phân hệ Quản lý Nhân sự & Phân quyền (RBAC):** Quản lý danh sách nhân viên (Staff), liên kết tài khoản (Users), phân quyền vai trò (Roles) và các quyền chi tiết (Permissions), giám sát phân bổ vị trí làm việc (Room).
*   **Hệ thống Thông báo nội bộ (Internal Notification):** Gửi nhận thông báo thời gian thực giữa các phòng ban, đồng bộ hóa số lượng chưa đọc thông qua Blade SSR và cơ chế Polling AJAX tại Sidebar.

---

# 2. Kiến trúc Công nghệ (Tech Stack & Architecture)

*   **Backend:** **PHP 8.1+**, framework **Laravel 10** áp dụng mô hình kiến trúc MVC truyền thống nhưng phân tách lớp Route và Controller khoa học.
*   **Database:** **MySQL 8.0** kết hợp Eloquent ORM tối ưu quan hệ dữ liệu phức tạp.
*   **Frontend:**
    *   **Laravel Blade Template** (giao diện server-side rendering).
    *   **Bootstrap 5** (Layout Responsive).
    *   **Vite** (bundler tài nguyên tĩnh).
    *   **jQuery, Select2** (tìm kiếm tương tác AJAX thông minh cho Thuốc và ICD-10).
    *   **Google Fonts:** **Plus Jakarta Sans** mang lại giao diện hiện đại và thanh lịch.
    *   **CSS Premium chuyên dụng:** `public/css/custom-premium-style.css` định nghĩa hệ thống theme mềm mại, Soft Shadows, Glassmorphism nhẹ và các tối ưu Responsive trên Mobile.
*   **Containerization & DevOps:** **Laravel Sail** (Docker Compose) chứa các dịch vụ:
    *   `laravel.test` (PHP 8.2 / Nginx).
    *   `mysql` (Database).
    *   `redis` (Cache & Session).
    *   `mailpit` (Trình thu thập và kiểm thử Email).

---

# 3. Từ điển Dữ liệu & Database Schema Chi tiết

Hệ thống quản trị cơ sở dữ liệu quan hệ (RDBMS) gồm các thực thể chính được thiết kế chuẩn hóa:

### 3.1. Bảng `users` (Tài khoản người dùng)
Quản lý thông tin xác thực và tài khoản đăng nhập của cả nhân viên phòng khám và bệnh nhân.
*   `id` (BigInt, Primary Key, Auto Increment)
*   `username` (Varchar 255, Unique): Tên đăng nhập.
*   `password_hash` (Varchar 255): Mật khẩu đã mã hóa (sử dụng hàm `Hash::make`).
*   `full_name` (Varchar 255): Họ và tên đầy đủ.
*   `email` (Varchar 255, Nullable, Unique)
*   `phone_number` (Varchar 255, Nullable): Số điện thoại liên lạc.
*   `address` (Text, Nullable): Địa chỉ liên hệ.
*   `national_id` (Varchar 255, Nullable): Số CCCD/CMND.
*   `avatar_url` (Varchar 255, Nullable): Link ảnh đại diện.
*   `last_login_at` (Datetime, Nullable): Thời gian đăng nhập cuối cùng.
*   `status` (TinyInt, Default: 1): Trạng thái hoạt động (`1`: Active, `0`: Blocked).
*   `created_at` & `updated_at` (Timestamp)

### 3.2. Bảng `staff` (Nhân sự phòng khám)
Liên kết 1-1 với bảng `users` để định danh bác sĩ, điều dưỡng, thu ngân, quản trị viên.
*   `id` (BigInt, Primary Key, Auto Increment)
*   `user_id` (BigInt, Foreign Key liên kết `users.id`): Liên kết tài khoản.
*   `staff_code` (Varchar 50, Unique): Mã nhân viên (ví dụ: `BS001`, `TN002`).
*   `staff_type` (Varchar 50): Phân loại (`Doctor`, `Nurse`, `Cashier`, `Pharmacist`, `Admin`).
*   `full_name` (Varchar 255): Họ tên nhân viên.
*   `yob` (Integer, Nullable): Năm sinh.
*   `age` (Integer, Nullable): Tuổi.
*   `gender` (Varchar 10, Nullable): Giới tính.
*   `email` (Varchar 255, Nullable)
*   `phone_number` (Varchar 50, Nullable)
*   `address` (Text, Nullable)
*   `national_id` (Varchar 50, Nullable)
*   `academic_degree` (Varchar 100, Nullable): Học vị (Thạc sĩ, Tiến sĩ, Cử nhân...).
*   `specialty` (Varchar 255, Nullable): Chuyên khoa (Đa khoa, Nội khoa, Tai Mũi Họng...).
*   `medical_license` (Varchar 100, Nullable): Số chứng chỉ hành nghề (dành cho Bác sĩ).
*   `department_id` (BigInt, Foreign Key liên kết `departments.id`): Thuộc Khoa nào.
*   `room_id` (BigInt, Foreign Key liên kết `rooms.id`, Nullable): Phòng làm việc/phòng khám được phân bổ.
*   `position` (Varchar 100, Nullable): Chức vụ (Trưởng khoa, Nhân viên...).
*   `career_start_year` (Integer, Nullable): Năm bắt đầu hành nghề.
*   `status` (TinyInt, Default: 1): Trạng thái hoạt động.

### 3.3. Bảng `patient` (Hồ sơ bệnh nhân)
Quản lý thông tin hành chính cốt lõi của người bệnh.
*   `id` (BigInt, Primary Key, Auto Increment)
*   `user_id` (BigInt, Foreign Key liên kết `users.id`, Nullable): Liên kết tài khoản cá nhân của bệnh nhân (nếu có để tự tra cứu lịch sử).
*   `patient_code` (Varchar 50, Unique): Mã định danh bệnh nhân (ví dụ: `BN2026001`).
*   `full_name` (Varchar 255): Họ và tên.
*   `gender` (Varchar 10): Giới tính (`Male` - Nam, `Female` - Nữ, `Other` - Khác).
*   `age` (Integer, Nullable): Tuổi hiện tại.
*   `email` (Varchar 255, Nullable)
*   `phone_number` (Varchar 50, Unique): Số điện thoại (dùng để tra cứu nhanh).
*   `address` (Text, Nullable): Địa chỉ thường trú.
*   `national_id` (Varchar 50, Nullable): CCCD/CMND.
*   `insurance_number` (Varchar 100, Nullable): Số thẻ BHYT.
*   `emergency_contact` (Varchar 255, Nullable): Số điện thoại người thân khi khẩn cấp.
*   `status` (TinyInt, Default: 1): Trạng thái hoạt động.

### 3.4. Bảng `consultation` (Phiên khám bệnh - Thực thể trung tâm)
Trái tim của EMR, liên kết tất cả các tiến trình lâm sàng và tài chính của một lượt khám.
*   `id` (BigInt, Primary Key, Auto Increment)
*   `patient_id` (BigInt, Foreign Key liên kết `patient.id`): Bệnh nhân được khám.
*   `doctor_id` (BigInt, Foreign Key liên kết `staff.id`, Nullable): Bác sĩ đảm nhiệm kết luận khám.
*   `order_number` (Integer): Số thứ tự khám trong ngày.
*   `consultation_date` (Datetime): Ngày giờ thực hiện đăng ký/khám.
*   `chief_complaint` (Text, Nullable): Lý do vào viện (Bệnh nhân tự khai).
*   `symptoms` (Text, Nullable): Triệu chứng lâm sàng ghi nhận bởi tiếp đón/điều dưỡng.
*   `temperature` (Decimal 3,1, Nullable): Nhiệt độ cơ thể (được cast thành decimal).
*   `blood_pressure` (Varchar 50, Nullable): Huyết áp (ví dụ: `120/80 mmHg`).
*   `diagnosis_code` (Varchar 15, Foreign Key liên kết `diagnosis.diagnosis_code`, Nullable): Mã chẩn đoán chính ICD-10.
*   `diagnosis_notes` (Text, Nullable): Mô tả chi tiết chẩn đoán của bác sĩ, hướng điều trị lâm sàng.
*   `follow_up_date` (Date, Nullable): Lịch hẹn tái khám.
*   `status` (TinyInt, Default: 0): Trạng thái phiên khám:
    *   `0` (`STATUS_NEW`): Mới tạo / Đăng ký.
    *   `1` (`STATUS_PENDING`): Chờ khám (trong phòng khám).
    *   `2` (`STATUS_PROCESSING`): Đang khám (Bác sĩ đang mở bệnh án).
    *   `3` (`STATUS_WAIT_PAYMENT`): Chờ thanh toán (Đã có kết luận/đơn thuốc, chờ thu ngân thu tiền).
    *   `4` (`STATUS_COMPLETED`): Hoàn thành (Đã đóng tiền và cấp phát xong).

### 3.5. Bảng `departments` (Khoa lâm sàng/cận lâm sàng)
*   `id`, `dept_code` (Mã khoa, e.g. `KLS`), `dept_name` (Họ tên khoa), `status` (Trạng thái).

### 3.6. Bảng `rooms` (Phòng chức năng)
*   `id`, `room_code` (e.g. `PK-SINGLE`), `room_name`, `room_type` (Phân loại phòng, e.g. Phòng khám, Phòng siêu âm, Phòng thu ngân...), `department_id`, `status`.

### 3.7. Bảng liên kết trung gian `consultation_room` (Xếp phòng chờ khám)
Mối quan hệ nhiều - nhiều giữa Phiên khám (`consultation`) và Phòng (`rooms`). Cho biết phiên khám đó đã/đang được xếp vào hàng đợi phòng nào.
*   `consultation_id` (BigInt, Foreign Key)
*   `room_id` (BigInt, Foreign Key)
*   `notes` (Text, Nullable): Ghi chú lý do chuyển phòng/yêu cầu khám.
*   `status` (TinyInt, Default: 1): Trạng thái hàng đợi tại phòng khám này (`1`: Chờ khám, `2`: Đang khám, `3`: Đã khám xong).

### 3.8. Bảng `prescription` (Đơn thuốc điện tử)
*   `id` (BigInt, Primary Key, Auto Increment)
*   `consultation_id` (BigInt, Foreign Key liên kết `consultation.id`)
*   `prescribed_by` (BigInt, Foreign Key liên kết `staff.id`): Bác sĩ kê đơn.
*   `prescribed_at` (Datetime): Thời điểm kê đơn.
*   `doctor_advice` (Text, Nullable): Lời dặn dò chung của bác sĩ về chế độ ăn uống, sinh hoạt.
*   `dispensed_at` (Datetime, Nullable): Thời điểm dược sĩ cấp phát thuốc thực tế.
*   `dispensed_by` (BigInt, Foreign Key liên kết `staff.id`, Nullable): Dược sĩ thực hiện cấp thuốc.
*   `status` (TinyInt, Default: 0): Trạng thái đơn thuốc (`0`: Chờ phát, `1`: Đã cấp phát).

### 3.9. Bảng `prescription_detail` (Chi tiết đơn thuốc)
Chứa thông tin chi tiết từng loại thuốc được kê trong đơn thuốc.
*   `prescription_id` (BigInt, Foreign Key)
*   `medicine_id` (BigInt, Foreign Key liên kết `medicine.id`)
*   `quantity` (Integer): Tổng số lượng thuốc được cấp.
*   `unit` (Varchar 50): Đơn vị tính (Viên, Gói, Chai...).
*   `duration_days` (Integer): Số ngày uống thuốc.
*   `dosage` (Varchar 255): Liều dùng hàng ngày chi tiết (ví dụ: `Sáng: 1, Trưa: 0, Chiều: 1, Tối: 0`).
*   `usage_instructions` (Text, Nullable): Lời dặn chi tiết cách dùng (ví dụ: `Uống sau ăn 30 phút`).
*   `status` (TinyInt, Default: 1)

### 3.10. Bảng `invoice` (Hóa đơn thu phí)
Quản lý toàn bộ thông tin thanh toán tài chính của phiên khám.
*   `id` (BigInt, Primary Key, Auto Increment)
*   `invoice_code` (Varchar 50, Unique): Mã hóa đơn sinh tự động dạng `HD-YYYYMMDD-XXXX`.
*   `consultation_id` (BigInt, Foreign Key liên kết `consultation.id`)
*   `invoice_date` (Datetime): Ngày tạo hóa đơn.
*   `consultation_fee` (Decimal 12,2): Phí khám bệnh lâm sàng gốc (mặc định: `100,000 VND`).
*   `lab_test_fee` (Decimal 12,2, Default: 0): Phí chỉ định xét nghiệm/cận lâm sàng.
*   `medicine_fee` (Decimal 12,2, Default: 0): Tổng tiền thuốc từ đơn thuốc (được cộng dồn tự động).
*   `other_fee` (Decimal 12,2, Default: 0): Phụ phí phát sinh do thu ngân điền thủ công.
*   `discount` (Decimal 12,2, Default: 0): Số tiền chiết khấu, giảm giá cho bệnh nhân.
*   `total_amount` (Decimal 12,2): Tổng tiền thực tế cần thanh toán (`= consultation_fee + lab_test_fee + medicine_fee + other_fee - discount`).
*   `payment_method` (Varchar 50, Nullable): Phương thức thanh toán (`Cash` - Tiền mặt, `Card` - Thẻ, `Transfer` - Chuyển khoản ngân hàng, `Insurance` - Bảo hiểm bảo lãnh, `Other`).
*   `paid_at` (Datetime, Nullable): Ngày giờ nộp tiền thực tế.
*   `cashier_id` (BigInt, Foreign Key liên kết `staff.id`, Nullable): Thu ngân thực hiện thu tiền.
*   `status` (TinyInt, Default: 0): Trạng thái hóa đơn:
    *   `0` (`STATUS_PENDING`): Chờ thanh toán.
    *   `1` (`STATUS_PAID`): Đã thanh toán.
    *   `2` (`STATUS_CANCELLED`): Đã hủy hóa đơn.

### 3.11. Bảng `invoice_detail` (Chi tiết hóa đơn)
Phân rã chi tiết các khoản tiền trong hóa đơn để in ra phiếu thu.
*   `id`, `invoice_id`, `item_type` (Loại dịch vụ, e.g. `CONSULTATION`, `MEDICINE`, `LAB`, `OTHER`), `item_name` (Tên khoản thu), `quantity`, `unit_price`, `discount`, `line_total`, `status`.

### 3.12. Bảng `medicine` & `medicine_detail` (Quản lý thuốc & Giá bán theo phiên bản)
Thiết kế tối ưu theo cơ chế Versioning nhằm lưu vết lịch sử thay đổi giá bán hoặc quy cách đóng gói của thuốc mà không làm sai lệch các hóa đơn cũ trong quá khứ.
*   **Bảng `medicine`:** Chứa thông tin gốc của thuốc.
    *   `id`, `medicine_category_id` (Foreign Key), `medicine_code` (Mã thuốc), `medicine_name` (Tên thương mại), `note`, `status`.
*   **Bảng `medicine_detail`:** Lưu thông tin biến động giá và đặc tính chi tiết theo thời gian.
    *   `id`, `medicine_id` (Foreign Key), `order_number` (Số phiên bản cập nhật), `unit` (Đơn vị tính hiện tại), `price` (Đơn giá bán hiện hành), `active_ingredient` (Dược chất hoạt tính/hàm lượng), `storage_condition` (Điều kiện bảo quản), `status` (`1` là phiên bản giá đang có hiệu lực).

### 3.13. Bảng `system_notifications` & `notification_reads` (Thông báo nội bộ)
*   **Bảng `system_notifications`:** `id`, `title` (Tiêu đề), `content` (Nội dung thông báo), `sender_id` (Người gửi), `recipient_id` (Người nhận cụ thể, Nullable), `type` (Phạm vi gửi: `All` - Tất cả, `Role` - Theo vai trò cụ thể, `User` - Chỉ định cá nhân), `target_role` (Mã vai trò mục tiêu nếu gửi theo nhóm), `created_at`.
*   **Bảng `notification_reads`:** `user_id`, `notification_id`, `read_at` (Thời gian click đọc thông báo - để quản lý trạng thái đã đọc/chưa đọc).

---

# 4. Phân Quyền Hệ Thống (RBAC Matrix)

Hệ thống kiểm soát truy cập nghiêm ngặt dựa trên vai trò người dùng (Role-Based Access Control) thông qua Custom Middleware `role:ROLE_CODE_1,ROLE_CODE_2...` kiểm tra trên trường liên kết trong bảng `users_roles`.

| Vai trò (Role Code) | Mô tả chi tiết | Phạm vi nghiệp vụ được cấp phép |
| :--- | :--- | :--- |
| **ADMIN** | Quản trị viên tối cao | Toàn quyền kiểm soát hệ thống. CRUD danh mục dùng chung (Mã bệnh ICD-10, Loại thuốc, Thuốc, Cấu hình phòng khám, Khoa, Phòng), quản lý tài khoản người dùng (khóa/mở, reset mật khẩu), xem Dashboard thống kê doanh thu và lượt khám toàn phòng khám. |
| **RECEPTIONIST** | Tiếp đón viên | Đăng ký hồ sơ hành chính bệnh nhân, tạo phiên khám lâm sàng ban đầu, xếp bệnh nhân vào hàng đợi của các phòng khám cụ thể. Xem thông tin lịch sử tiếp đón. |
| **DOCTOR** | Bác sĩ khám chữa bệnh | Xem danh sách chờ khám của phòng mình làm việc. Vào giao diện EMR khám bệnh: xem lịch sử bệnh án, chẩn đoán, kê đơn thuốc điện tử và chỉ định hẹn tái khám. Xem lịch sử khám toàn khoa. Quản lý toa thuốc mẫu cá nhân để kê đơn siêu tốc. |
| **CASHIER** | Nhân viên thu ngân | Tra cứu hóa đơn chờ thanh toán của bệnh nhân theo nhiều tiêu chí (Mã BN, SĐT). Điều chỉnh phụ phí, áp dụng chiết khấu và thực hiện thanh toán hóa đơn. In hóa đơn/phiếu thu tài chính. |
| **PHARMACIST** | Dược sĩ cấp phát | Tra cứu đơn thuốc của bệnh nhân đã hoàn thành thanh toán. Kiểm tra số lượng thực tế, thực hiện đóng gói và bấm xác nhận hoàn thành cấp phát thuốc vật lý cho bệnh nhân. |
| **PATIENT** | Bệnh nhân | Tự phục vụ: Đăng nhập xem hồ sơ sức khỏe cá nhân, danh sách các phiên khám đã thực hiện, chi tiết kết luận chẩn đoán của bác sĩ và hướng dẫn sử dụng đơn thuốc. |

---

# 5. Chi tiết Cấu trúc Thư mục Quan trọng

```text
qlpk/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/                  # Xử lý các nghiệp vụ quản trị tối cao của ADMIN & RECEPTIONIST
│   │   │   ├── ConsultationController.php    # Quản lý vòng đời phiên khám (CRUD Admin)
│   │   │   ├── PatientController.php         # Tạo, sửa hồ sơ bệnh nhân hành chính
│   │   │   ├── DiagnosisController.php       # Quản lý mã bệnh chuẩn ICD-10 (Có tính năng Import Excel)
│   │   │   ├── StaffController.php           # Quản lý hồ sơ nhân viên và liên kết tài khoản
│   │   │   ├── MedicineController.php        # CRUD Thuốc và các phiên bản giá bán
│   │   │   └── SystemSettingController.php   # Quản lý thông tin phòng khám, logo Base64
│   │   ├── Doctor/                 # Phân hệ lâm sàng chuyên sâu cho Bác sĩ
│   │   │   ├── ExaminationController.php     # Logic danh sách chờ, khám bệnh, lưu kết luận và xem bệnh án cũ
│   │   │   └── PrescriptionTemplateController.php # Tạo và áp dụng các toa thuốc mẫu
│   │   ├── Cashier/                # Xử lý tài chính
│   │   │   └── InvoiceController.php         # Tra cứu hóa đơn, tính tiền, cập nhật phụ phí và thu ngân thanh toán
│   │   ├── Pharmacist/             # Phân hệ nhà thuốc
│   │   │   └── PrescriptionController.php    # Xác nhận và ghi nhận thời gian cấp phát thuốc thực tế
│   │   ├── Patient/                # Cổng thông tin bệnh nhân
│   │   │   └── MyRecordController.php        # Tra cứu hồ sơ sức khỏe cá nhân
│   │   ├── NotificationController.php        # Quản lý thông báo đa kênh, đếm AJAX chưa đọc
│   │   └── ProfileController.php             # Cập nhật thông tin cá nhân và đổi mật khẩu dùng chung
│   ├── Http/Middleware/
│   │   └── RoleMiddleware.php      # Middleware kiểm tra vai trò (RBAC) để chặn truy cập trái phép
│   └── Models/                     # Định nghĩa Schema dữ liệu và các mối quan hệ Eloquent ORM
│       ├── User.php                # Thực thể tài khoản, kiểm tra hasRole(), hasPermission()
│       ├── Staff.php               # Thông tin chi tiết nhân sự, liên kết User, Room, Department
│       ├── Patient.php             # Hồ sơ bệnh nhân, scope tìm kiếm nhanh
│       ├── Consultation.php        # Thực thể trung tâm, định nghĩa hằng số trạng thái (STATUS_NEW, PENDING...)
│       ├── Invoice.php             # Hóa đơn thu phí, hàm tự động tạo mã HD-YYYYMMDD-XXXX
│       ├── Medicine.php            # Thực thể thuốc gốc
│       ├── MedicineDetail.php      # Phiên bản đóng gói, đơn vị và đơn giá thuốc hiện hành
│       └── SystemNotification.php  # Cấu hình gửi thông báo toàn hệ thống
├── database/
│   ├── migrations/                 # Lịch sử cấu trúc cơ sở dữ liệu MySQL
│   └── seeders/                    # Các kịch bản dữ liệu mẫu (Roles, Admin, Clinic duy nhất)
├── public/
│   ├── css/
│   │   └── custom-premium-style.css # File CSS Custom Premium chính (Theme, Border-radius, Responsive Table)
│   └── js/
│       └── prescription-form.js    # Tách biệt logic Javascript xử lý tương tác kê đơn thuốc thông minh tại view Doctor
├── resources/
│   ├── views/                      # Hệ thống giao diện hiển thị HTML/Blade
│   │   ├── auth/login.blade.php    # Trang đăng nhập thiết kế độc lập, tối ưu trải nghiệm
│   │   ├── layouts/app.blade.php   # Layout nền tảng (Responsive Sidebar, Mobile Offcanvas Sidebar Drawer)
│   │   ├── partials/sidebar.blade.php # Sidebar thông minh tích hợp ajaxUnreadCount đếm thông báo
│   │   ├── doctor/                 # Giao diện của Bác sĩ (Khám bệnh lâm sàng, Lịch sử của tôi)
│   │   ├── invoices/               # Giao diện của Thu ngân (Danh sách, Chỉnh sửa hóa đơn, Biên nhận in ấn)
│   │   └── welcome.blade.php       # Trang giới thiệu / Chuyển hướng tự động
└── routes/                         # Quản lý định tuyến chia tách theo Phân hệ
    ├── web.php                     # Route dùng chung (Auth, Profile, Notification)
    ├── admin.php                   # Hàng loạt route nghiệp vụ CRUD cấu hình cho ADMIN & RECEPTIONIST
    ├── doctor.php                  # Hàng loạt route chuyên môn của DOCTOR (My-consultations, Examine, Templates)
    ├── cashier.php                 # Nhóm route xử lý tiền nong của CASHIER
    ├── pharmacist.php              # Route xác nhận cấp phát thuốc của PHARMACIST
    └── patient.php                 # Cổng xem bệnh án cá nhân của PATIENT
```

---

# 6. Danh mục Routes & Các Endpoint Nghiệp vụ Chi tiết

Dưới đây là bảng tra cứu toàn bộ danh sách routes trong hệ thống để phục vụ việc liên kết kiểm tra phân quyền và ánh xạ các màn hình chức năng:

### 6.1. Routes Dùng Chung (Shared Web Routes - `routes/web.php`)
Các tuyến đường này yêu cầu đăng nhập chung (`auth` middleware) và phục vụ cho tất cả các tài khoản nhân viên.

*   `GET /` `->` Tự động chuyển hướng về trang đăng nhập `/login`.
*   `GET /login` & `POST /login` `->` Route xác thực mặc định của Laravel Auth.
*   `GET /home` `[HomeController@index]` `->` Trang chủ điều phối chung sau khi đăng nhập thành công.
*   `GET /profile` `[ProfileController@index]` `->` Xem thông tin tài khoản cá nhân hiện hành.
*   `POST /profile/update` `[ProfileController@update]` `->` Cập nhật thông tin cơ bản.
*   `GET /profile/change-password` `[ProfileController@changePassword]` `->` Giao diện đổi mật khẩu.
*   `POST /profile/change-password` `[ProfileController@updatePassword]` `->` Lưu mật khẩu mới.
*   `GET /notifications` `[NotificationController@index]` `->` Danh sách thông báo nội bộ nhận được.
*   `GET /notifications/create` `[NotificationController@create]` `->` Soạn thông báo mới (Chỉ dành cho nhân viên).
*   `POST /notifications` `[NotificationController@store]` `->` Gửi thông báo đến đối tượng chỉ định.
*   `GET /notifications/{id}` `[NotificationController@show]` `->` Đọc chi tiết thông báo (Ghi nhận trạng thái đã đọc thông qua bảng trung gian).
*   `DELETE /notifications/{id}` `[NotificationController@destroy]` `->` Xóa thông báo khỏi danh sách.
*   `GET /notifications/ajax/unread-count` `[NotificationController@ajaxUnreadCount]` `->` Endpoint AJAX trả về số lượng thông báo chưa đọc dạng JSON để sidebar cập nhật chấm đỏ.

---

### 6.2. Routes Tiếp Đón & Quản Trị Hệ Thống (`routes/admin.php`)
Được bảo vệ bởi Middleware `role:ADMIN,RECEPTIONIST` hoặc `role:ADMIN` và `role:ADMIN,DOCTOR,PHARMACIST`.

#### A. Quyền của Tiếp Đón viên & Admin (`role:ADMIN,RECEPTIONIST`):
*   `GET /patients` `[PatientController@index]` `->` Danh sách hồ sơ bệnh nhân toàn phòng khám.
*   `GET /patients/create` `[PatientController@create]` `->` Giao diện tạo mới hồ sơ bệnh nhân.
*   `POST /patients` `[PatientController@store]` `->` Lưu hồ sơ bệnh nhân mới vào hệ thống.
*   `GET /patients/{id}/edit` `[PatientController@edit]` `->` Giao diện cập nhật hồ sơ bệnh nhân.
*   `PUT /patients/{id}` `[PatientController@update]` `->` Cập nhật hồ sơ bệnh nhân.
*   `GET /patients/search` `[PatientController@search]` `->` Tìm kiếm bệnh nhân theo từ khóa (Mã BN, Họ Tên, SĐT).
*   `GET /patients/ajax/search` `[PatientController@ajaxSearch]` `->` Trả về danh sách bệnh nhân AJAX dạng JSON phục vụ Auto-complete.
*   `GET /consultations` `[ConsultationController@index]` `->` Quản lý danh sách các phiên khám toàn diện.
*   `GET /consultations/create` `[ConsultationController@create]` `->` Tạo mới một phiên khám bệnh ban đầu, gán chỉ số sinh hiệu (nhiệt độ, huyết áp).
*   `POST /consultations` `[ConsultationController@store]` `->` Ghi nhận phiên khám mới vào DB.
*   `GET /consultations/{id}/edit` `[ConsultationController@edit]` `->` Sửa thông tin hành chính/sinh hiệu của phiên khám.
*   `PUT /consultations/{id}` `[ConsultationController@update]` `->` Cập nhật thông tin phiên khám.
*   `DELETE /consultations/{id}` `[ConsultationController@destroy]` `->` Hủy/xóa phiên khám (Chỉ áp dụng khi chưa khám).
*   `GET /consultation-rooms` `[ConsultationRoomController@index]` `->` Quản lý danh sách xếp hàng chờ khám tại các phòng.
*   `POST /consultation-rooms` `[ConsultationRoomController@store]` `->` Xếp một phiên khám vào hàng đợi của một phòng cụ thể (`consultation_room`).
*   `DELETE /consultation-rooms/{consultation_id}/{room_id}` `[ConsultationRoomController@destroy]` `->` Rút bệnh nhân khỏi hàng đợi của phòng đó.

#### B. Quyền Hạn Tối Cao của Admin (`role:ADMIN`):
*   `GET /dashboard` `[DashboardController@index]` `->` Trang tổng quan số liệu thống kê.
*   `GET /dashboard/stats` `[DashboardController@getStats]` `->` Trả về JSON thống kê lượt khám, doanh thu ngày/tháng để vẽ biểu đồ.
*   `GET /diagnoses/import` & `POST /diagnoses/import` `->` Nhập danh sách mã bệnh ICD-10 hàng loạt từ file Excel.
*   `GET /diagnoses/create` & `POST /diagnoses` `->` Tạo thủ công mã bệnh ICD-10 mới.
*   `PUT /diagnoses/{id}` & `DELETE /diagnoses/{id}` `->` Chỉnh sửa, xóa mã chẩn đoán.
*   `GET /staff` `[StaffController@index]` `->` Quản lý danh sách nhân sự (Bác sĩ, Y tá, Thu ngân...).
*   `GET /staff/create` & `POST /staff` `->` Khai báo hồ sơ nhân sự mới, liên kết với tài khoản `users`.
*   `GET /staff/{id}/edit` & `PUT /staff/{id}` `->` Thay đổi phòng làm việc, chuyên khoa, thông tin của nhân sự.
*   `GET /medicines` `[MedicineController@index]` `->` Xem danh mục thuốc toàn phòng khám.
*   `GET /medicines/create` & `POST /medicines` `->` Tạo loại thuốc gốc mới.
*   `POST /medicines/{id}/details` `[MedicineController@storeDetail]` `->` Cập nhật phiên bản đơn giá bán mới cho thuốc (Versioning).
*   `GET /users` `[UserController@index]` `->` Quản lý tài khoản đăng nhập toàn hệ thống.
*   `PATCH /users/{id}/toggle-status` `[UserController@toggleStatus]` `->` Khóa hoặc kích hoạt lại tài khoản nhân viên/bệnh nhân.
*   `POST /users/{id}/reset-password` `[UserController@resetPassword]` `->` Đặt lại mật khẩu mặc định nhanh chóng cho nhân viên khi quên.
*   `GET /settings` `[SystemSettingController@index]` `->` Xem thông tin cấu hình phòng khám (Tên phòng khám, Địa chỉ, Số điện thoại, Logo đại diện).
*   `POST /settings` & `PUT /settings/{id}` `->` Cập nhật thông tin liên hệ phòng khám hoặc thay đổi logo Base64 trực tiếp hiển thị trên các hóa đơn in ấn.

#### C. Xem danh mục dùng chung (`role:ADMIN,DOCTOR,PHARMACIST` hoặc `role:ADMIN,DOCTOR`):
*   `GET /prescriptions` `[PrescriptionController@index]` `->` Xem lịch sử kê đơn toàn phòng khám.
*   `GET /prescriptions/{id}/show` `[PrescriptionController@show]` `->` Xem chi tiết đơn thuốc đã kê.
*   `GET /diagnoses` `[DiagnosisController@index]` `->` Tra cứu mã bệnh ICD-10.
*   `GET /medicines/ajax/search` `->` Endpoint AJAX cho Select2 gõ từ khóa tìm kiếm nhanh tên thuốc.
*   `GET /diagnoses/ajax/search` `->` Endpoint AJAX tìm kiếm nhanh mã/tên bệnh ICD-10.

---

### 6.3. Routes Nghiệp Vụ Lâm Sàng của Bác Sĩ (`routes/doctor.php`)
Được bảo vệ nghiêm ngặt bởi Middleware `role:DOCTOR`.

*   `GET /consultations/doctor/my-consultations` `[ExaminationController@index]` `->` Dashboard chính của Bác sĩ. Hiển thị danh sách các bệnh nhân đang xếp hàng chờ khám tại phòng mà Bác sĩ được chỉ định (`room_id` liên kết bảng `staff`).
*   `GET /consultations/doctor/ajax/search` `[ExaminationController@ajaxSearch]` `->` AJAX tìm nhanh bệnh nhân trong hàng chờ khám của phòng mình làm việc.
*   `GET /consultations/doctor/{id}/examine` `[ExaminationController@examine]` `->` **Giao diện Khám bệnh Lâm sàng chính (EMR View)**. Đồng thời cập nhật trạng thái phiên khám thành `2` (`STATUS_PROCESSING`).
*   `PUT /doctor/consultations/{id}/conclusion` `[ExaminationController@saveConclusion]` `->` **Lưu kết luận khám bệnh**. Bác sĩ gửi dữ liệu chẩn đoán chính (mã ICD-10), ghi chú chẩn đoán, và hẹn ngày tái khám. Cập nhật trạng thái phiên khám thành `3` (`STATUS_WAIT_PAYMENT`).
*   `GET /doctor/my-history` `[ExaminationController@myHistory]` `->` Tra cứu lịch sử tất cả các ca khám do chính bác sĩ hiện tại thực hiện.
*   `GET /my-clinic` `[ExaminationController@myRoomPatients]` `->` Xem danh sách theo dõi bệnh nhân nội trú/phòng khám hiện tại.
*   `GET /templates` `[PrescriptionTemplateController@index]` `->` Quản lý danh sách các Toa thuốc mẫu (mẫu kê đơn định sẵn).
*   `POST /templates/from-prescription/{id}` `[PrescriptionTemplateController@storeFromPrescription]` `->` Tạo nhanh một đơn thuốc mẫu dựa trên đơn thuốc thực tế vừa kê cho bệnh nhân.
*   `POST /templates` & `PUT /templates/{id}` `->` Tạo mới hoặc cập nhật toa thuốc mẫu thủ công.

---

### 6.4. Routes Nghiệp Vụ Thu Ngân (`routes/cashier.php`)
Được bảo vệ bởi Middleware `role:ADMIN,CASHIER`.

*   `GET /invoices` `[InvoiceController@index]` `->` Giao diện thu ngân. Chia 2 khu vực rõ ràng: **Hàng đợi chờ đóng tiền** (hóa đơn trạng thái `0`) và **Lịch sử hóa đơn đã thu** (hóa đơn trạng thái `1` hoặc `2`).
*   `GET /invoices/ajax/pending` `[InvoiceController@ajaxPending]` `->` Lấy danh sách hàng đợi hóa đơn chờ thanh toán qua AJAX để tự động làm mới giao diện thu ngân.
*   `GET /invoices/{id}/edit` `[InvoiceController@edit]` `->` Giao diện chỉnh sửa hóa đơn chưa thanh toán. Cho phép thu ngân nhập thêm Phụ phí (`other_fee`), Ghi chú phụ phí (`other_fee_note`), hoặc Tiền chiết khấu giảm giá (`discount`).
*   `PUT /invoices/{id}` `[InvoiceController@update]` `->` Cập nhật phụ phí/chiết khấu và tự động tính toán lại tổng tiền `total_amount` bằng DB Transaction.
*   `GET /invoices/{id}/show` `[InvoiceController@show]` `->` Màn hình xem chi tiết hóa đơn, đồng thời là mẫu in biên nhận tài chính chuyên nghiệp (tích hợp đầy đủ thông tin phòng khám từ cấu hình hệ thống).
*   `PUT /invoices/{id}/pay` `[InvoiceController@pay]` `->` **Xác nhận Thanh toán Hóa đơn**. Thu ngân bấm xác nhận phương thức trả tiền (`Cash`, `Card`, `Transfer`...), hệ thống cập nhật hóa đơn thành `STATUS_PAID` (`1`) và tự động nâng trạng thái phiên khám liên quan thành `STATUS_COMPLETED` (`4`).

---

### 6.5. Routes Nghiệp Vụ Cấp Phát Thuốc (`routes/pharmacist.php`)
Được bảo vệ bởi Middleware `role:ADMIN,PHARMACIST`.

*   `PATCH /prescriptions/{id}/status` `[PrescriptionController@updateStatus]` `->` Dược sĩ bấm xác nhận đã cấp phát thuốc vật lý cho bệnh nhân. Đơn thuốc chuyển trạng thái từ Chờ phát sang Đã phát, cập nhật `dispensed_at` và `dispensed_by`.

---

### 6.6. Routes Cổng Thông Tin Bệnh Nhân (`routes/patient.php`)
Được bảo vệ bởi Middleware `role:PATIENT`.

*   `GET /my-medical-record` `[MyRecordController@index]` `->` Bệnh nhân đăng nhập tự tra cứu toàn bộ lịch sử bệnh án cá nhân qua các thời kỳ khám, xem chi tiết từng đơn thuốc, chẩn đoán của bác sĩ và hướng dẫn liều dùng.

---

# 7. Quy trình Nghiệp vụ Thực tế từng bước (End-to-End Business Flow)

Quy trình chuẩn hóa hoạt động của phòng khám được thực hiện tuần tự qua các vai trò nhân viên:

```mermaid
sequenceDiagram
    autonumber
    actor Receptionist as Tiếp đón
    actor Doctor as Bác sĩ (Khám)
    actor Cashier as Thu ngân (Thanh toán)
    actor Pharmacist as Dược sĩ (Phát thuốc)
    actor Patient as Bệnh nhân

    Patient->>Receptionist: Đến khám & Cung cấp thông tin
    Note over Receptionist: Tra cứu SĐT bệnh nhân cũ hoặc tạo mới hồ sơ
    Receptionist->>Receptionist: Đăng ký Bệnh nhân (PatientController@store)
    Receptionist->>Receptionist: Lập Phiếu khám & Chỉ số sinh hiệu (ConsultationController@store)
    Receptionist->>Receptionist: Xếp bệnh nhân vào phòng chờ khám (ConsultationRoomController@store)
    
    Note over Doctor: Danh sách hàng chờ phòng mình tự động hiển thị ca khám mới
    Doctor->>Doctor: Click khám bệnh (ExaminationController@examine) - Trạng thái -> Đang khám
    Note over Doctor: View EMR 2 cột: Cột trái xem lịch sử & Cột phải điền chẩn đoán, kê đơn
    Doctor->>Doctor: Xem lịch sử bệnh án cũ, chọn "Sử dụng lại toa cũ" nếu cần
    Doctor->>Doctor: Tìm kiếm thuốc bằng Select2 AJAX, nhập liều lượng thông minh
    Doctor->>Doctor: Nhập chẩn đoán chính ICD-10 & Hướng điều trị
    Doctor->>Doctor: Lưu kết luận (ExaminationController@saveConclusion) - Phiên khám -> Chờ thanh toán
    Note over Doctor: Hệ thống tự động tạo hóa đơn (Invoice) chờ thu ngân

    Note over Cashier: Danh sách hàng chờ thanh toán tự động xuất hiện hóa đơn mới
    Cashier->>Cashier: Tra cứu & Chọn hóa đơn của bệnh nhân
    Cashier->>Cashier: Nhập phụ phí / chiết khấu giảm giá nếu có (InvoiceController@update)
    Cashier->>Cashier: Xác nhận phương thức nộp tiền & Bấm "Thanh toán" (InvoiceController@pay)
    Note over Cashier: Hóa đơn -> Đã thanh toán, Phiên khám -> Hoàn thành. In biên nhận.

    Note over Pharmacist: Nhận thông tin đơn thuốc của phiên khám đã thanh toán thành công
    Pharmacist->>Pharmacist: Soạn thuốc thực tế theo danh sách
    Pharmacist->>Pharmacist: Xác nhận cấp phát thuốc (PrescriptionController@updateStatus)
    Pharmacist->>Patient: Trao thuốc và hướng dẫn cách dùng thuốc vật lý
    Patient->>Patient: Ra về & Đăng nhập cổng tự tra cứu (MyRecordController@index) nếu cần
```

### 7.1. Chi tiết Hành động Nghiệp vụ:
1.  **Bước 1: Tiếp đón & Phân buồng khám**
    *   Tiếp đón viên dùng giao diện `patients/create` nhập Họ tên, SĐT, Giới tính, Tuổi, Thẻ BHYT (nếu có).
    *   Tạo phiếu khám mới (`consultations/create`), đo nhanh Nhiệt độ (ví dụ: `37.2` độ C), Huyết áp (`120/80 mmHg`), nhập lý do khám ("Đau họng kèm ho khan").
    *   Chọn phòng khám mục tiêu trong danh sách (ví dụ: "Phòng Khám Duy Nhất"). Bản ghi trong bảng `consultation_room` được ghi nhận trạng thái chờ. Bệnh nhân nhận số thứ tự khám `order_number`.
2.  **Bước 2: Lâm sàng & Kê đơn thuốc tại buồng Bác sĩ**
    *   Bác sĩ thấy bệnh nhân có số thứ tự trong hàng chờ tại màn hình `/consultations/doctor/my-consultations`, nhấn "Khám bệnh". Hệ thống ngay lập tức chuyển trạng thái phiên khám sang `2` (`Đang khám`) để tránh các bác sĩ khác mở trùng.
    *   **Giao diện EMR thông minh (View 2 cột):**
        *   **Cột bên trái:**
            *   Hiển thị thông tin hành chính bệnh nhân gọn gàng. Trên thiết bị di động, phần thông tin này tự động co lại trong Accordion thu gọn (`#leftColumnCollapse`) để tiết kiệm không gian làm việc.
            *   Lịch sử khám bệnh gồm **2 tab**:
                *   *Tab "Lịch sử BN":* Danh sách toàn bộ các phiên khám cũ của bệnh nhân ở mọi phòng ban kèm chi tiết chẩn đoán, đơn thuốc đã kê.
                *   *Tab "Lịch sử của tôi":* Các ca bệnh mà chính bác sĩ này đã khám trong quá khứ để tham chiếu thói quen kê đơn.
            *   **Tái sử dụng đơn thuốc:** Bác sĩ bấm vào một "Phiếu khám cũ #xxx" -> Hệ thống chuyển hướng mượt mà sang giao diện xem chi tiết `consultations/show.blade.php?reuse_mode=1` ngay tại tab hiện hành. Bác sĩ bấm nút **"Sử dụng lại toa thuốc"**, hệ thống đóng gói danh sách thuốc kèm lời dặn lưu tạm vào `localStorage['reuse_prescription_data']` rồi tự động quay lại buồng khám cũ. Form kê đơn tự động phát hiện dữ liệu tạm này, tự động điền đầy đủ thuốc và liều lượng rồi cuộn màn hình xuống vùng kê đơn cho bác sĩ. Dữ liệu tạm tự hủy ngay sau khi áp dụng hoặc sau 5 phút để bảo mật.
        *   **Cột bên phải:**
            *   Form kết luận chuyên môn: Nhập triệu chứng thực tế, ghi chú chẩn đoán. Ô tìm kiếm mã ICD-10 sử dụng Select2 tìm theo mã hoặc tên bệnh siêu nhanh.
            *   Form kê đơn thuốc điện tử: Thiết kế compact tối giản 1 dòng/thuốc. Khi gõ tìm kiếm thuốc bằng Select2, hệ thống tự động lọc theo Nhóm thuốc đã chọn. Bác sĩ nhập liều dùng riêng biệt theo 4 buổi sáng, trưa, chiều, tối và số ngày dùng -> Hệ thống tự động nhân số lượng để điền vào tổng số lượng thuốc cấp phát. Nút bấm chọn nhanh thời điểm uống (Trước ăn, Trong ăn, Sau ăn) giúp bác sĩ không phải gõ tay.
            *   *Tính năng Auto-fill rảnh tay:* Khi bác sĩ bấm nút "Thêm dòng thuốc tiếp theo", hệ thống tự động sao chép nhóm thuốc và số ngày dùng từ dòng liền trước, giúp bác sĩ kê đơn hàng loạt cực nhanh.
    *   Bác sĩ nhấn **"Lưu Kết Luận"**. Phiên khám lưu thông tin bác sĩ thực hiện khám (`doctor_id`) và tự động chuyển trạng thái thành `3` (`Chờ thanh toán`). Hệ thống đồng thời tính toán tổng tiền thuốc và tạo một hóa đơn `Invoice` ở trạng thái chờ tương ứng.
3.  **Bước 3: Điều chỉnh tài chính & Thu tiền tại quầy Thu ngân**
    *   Thu ngân mở màn hình `/invoices`, thấy bệnh nhân vừa khám xong xuất hiện ở hàng đợi Chờ thanh toán. Click chọn hóa đơn.
    *   Xem chi tiết các khoản phí: Tiền khám gốc (`100,000 VND`), tiền thuốc do bác sĩ kê đơn tự động tổng hợp.
    *   Nếu có chi phí phát sinh ngoài (ví dụ: tiền băng gạc, tiền dụng cụ hỗ trợ), thu ngân bấm nút "Chỉnh sửa hóa đơn" để thêm Phụ phí khác (`other_fee`) kèm ghi chú rõ ràng, hoặc áp dụng Chiết khấu giảm giá (`discount`) nếu bệnh nhân có thẻ ưu đãi. Hệ thống sử dụng DB Transaction tính toán lại chính xác tổng tiền thực thu.
    *   Bấm **"Xác nhận Thanh toán"** và chọn phương thức đóng tiền (ví dụ: `Transfer` - Chuyển khoản ngân hàng). Hệ thống cập nhật hóa đơn thành Đã thanh toán (`paid_at` cập nhật thời gian thực, `cashier_id` ghi nhận thu ngân thu tiền). Phiên khám liên quan tự động chuyển sang trạng thái cuối cùng `4` (`Hoàn thành`).
    *   In biên nhận tài chính trao cho bệnh nhân. Giao diện in ấn sạch đẹp, ẩn toàn bộ sidebar/header của phần mềm để tiết kiệm giấy và giữ tính thẩm mỹ.
4.  **Bước 4: Cấp phát thuốc thực tế**
    *   Dược sĩ phòng dược thấy đơn thuốc của bệnh nhân đã hoàn thành thanh toán trên phần mềm.
    *   Thực hiện soạn thuốc vật lý theo danh mục, liều lượng ghi trên đơn.
    *   Nhấp **"Xác nhận Cấp thuốc"** trên màn hình để chuyển đơn thuốc sang trạng thái Đã phát, hệ thống ghi nhận thời điểm phát thuốc thực tế. Dược sĩ trao thuốc kèm lời dặn hướng dẫn sử dụng. Bệnh nhân hoàn thành quy trình khám chữa bệnh tại phòng khám.

---

# 8. Hướng dẫn Thiết lập Ban đầu & Tài khoản Thử nghiệm (Initial Setup)

Để triển khai nhanh hệ thống phục vụ việc trình diễn hoặc viết hướng dẫn sử dụng, hãy sử dụng các tài khoản mẫu được cấu hình sẵn trong Seeders:

### 8.1. Các lệnh khởi tạo môi trường (Laravel Sail):
```bash
# 1. Khởi động các container Docker ở chế độ chạy nền
./vendor/bin/sail up -d

# 2. Cài đặt các thư viện frontend
./vendor/bin/sail npm install

# 3. Biên dịch tài nguyên tĩnh (Vite)
./vendor/bin/sail npm run dev

# 4. Chạy Migrations kèm kịch bản Seed dữ liệu mẫu đầy đủ
./vendor/bin/sail artisan migrate:fresh --seed

# 5. Chạy Seeder cấu hình phòng khám đơn (Single Doctor Clinic) chuyên dụng
./vendor/bin/sail artisan db:seed --class=SingleDoctorClinicSeeder
```

### 8.2. Danh sách Tài khoản Đăng nhập Mẫu (Thử nghiệm hệ thống):

Hệ thống cung cấp sẵn các tài khoản với phân quyền tương ứng để kiểm thử từng quy trình nghiệp vụ:

| Tên Đăng Nhập (Username) | Mật khẩu Mẫu | Vai trò được gán (Role) | Mô tả & Phòng khám Phân bổ |
| :--- | :--- | :--- | :--- |
| **`bacsi_admin`** | **`123456`** | **DOCTOR, ADMIN** | Tài khoản đặc quyền kép. Vừa có quyền quản trị tối cao của Admin để cấu hình hệ thống, vừa được gán vai trò Bác sĩ, làm việc tại phòng khám duy nhất **`Phòng Khám Duy Nhất` (PK-SINGLE)** thuộc khoa lâm sàng **`Khoa Lâm Sàng` (KLS)**. Thích hợp để demo trọn gói nhanh. |
| **`admin_demo`** | **`123456`** | **ADMIN** | Quản trị viên tối cao của hệ thống. Dùng để quản lý nhân viên, cấu hình phòng, import danh mục thuốc và xem dashboard doanh thu. |
| **`benhnhan_demo`** | **`123456`** | **PATIENT** | Bệnh nhân mẫu **Nguyễn Văn Bệnh Nhân** (Mã BN: `BN2026001`). Dùng để đăng nhập Cổng thông tin bệnh nhân xem lịch sử bệnh án. |

---

# 9. Giải pháp UI/UX Premium & Thiết kế Responsive trên Thiết bị Di động

Giao diện hệ thống được chăm chút tỉ mỉ, mang phong cách thiết kế cao cấp (Premium UI/UX) và tối ưu hóa tối đa cho các thiết bị di động, tablet màn hình nhỏ:

1.  **Hệ thống Theme Premium mềm mại:**
    *   Sử dụng màu nền xám nhẹ, các khối chức năng bo góc bo cong lớn (`12px` - `16px`) tạo cảm giác hiện đại, sang trọng.
    *   Đổ bóng mềm (Soft Shadows) phân tách chiều sâu các lớp giao diện.
    *   Tích hợp font chữ thời thượng **Plus Jakarta Sans** cho nét chữ thanh mảnh, dễ đọc dưới ánh sáng phòng khám.
2.  **Layout Responsive thông minh vượt trội:**
    *   *Desktop (>= 992px):* Sidebar điều hướng cố định bên trái giúp nhân viên thao tác một chạm nhanh chóng giữa các chức năng.
    *   *Mobile & Tablet (< 992px):* Sidebar tự động chuyển đổi thành dạng Hamburger Offcanvas Drawer (Ngăn kéo vuốt từ cạnh trái màn hình), giúp giải phóng toàn bộ không gian hiển thị cho bảng dữ liệu và form kê đơn.
3.  **Tối ưu hóa Bảng biểu chống vỡ chữ (No-wrap Table):**
    *   Tại các bảng danh sách bệnh nhân hoặc hóa đơn, để tránh hiện tượng chữ bị trình duyệt tự ý bóp nghẹt xuống dòng làm méo mó các nút hành động, hệ thống áp dụng thuộc tính `white-space: nowrap !important` cho các cột.
    *   Kết hợp bao bọc bảng trong container `.table-responsive` hỗ trợ thuộc tính cuộn ngang mượt mà (`overflow-x: auto`), bổ sung thanh cuộn Scrollbar màu Indigo nổi bật để người dùng vuốt kéo bằng ngón tay cực kỳ trơn tru trên màn hình cảm ứng di động.
4.  **Kê đơn dạng thẻ dọc thông minh (Mini-card Mobile Prescription):**
    *   Phần lưới kê đơn thuốc trên máy tính hiển thị dạng hàng ngang dài. Khi chuyển sang màn hình di động, hệ thống tự động biên dịch linh hoạt cấu trúc dòng thành các **Thẻ Mini Card** xếp dọc độc lập, giúp bác sĩ dễ dàng chạm bấm, hiệu chỉnh liều lượng bằng ngón tay một cách trực quan mà không bị lệch dòng.
5.  **Giải pháp chống trễ chấm đỏ thông báo (AJAX & Blade SSR Synchronized):**
    *   Để triệt tiêu hiện tượng giật/trễ/nhấp nháy giao diện khi trang tải xong mới chạy AJAX đếm thông báo, hệ thống áp dụng cơ chế kết hợp:
        *   Khi tải trang, số lượng thông báo chưa đọc được tính toán trực tiếp từ server và render ngay tại Sidebar (Server-side rendering qua Blade), chấm đỏ hiển thị tức thì.
        *   Trong quá trình nhân viên làm việc, một luồng polling AJAX tự động ngầm kích hoạt mỗi **60 giây** để cập nhật số lượng thông báo mới phát sinh trong phiên mà không cần tải lại trang.
        *   Khi click đọc chi tiết thông báo, AJAX lập tức cập nhật giảm số lượng hiển thị và ẩn chấm đỏ ngay lập tức tại Sidebar giúp trải nghiệm liền mạch tuyệt đối.

---
*Lưu ý hướng dẫn viết tài liệu: Khi viết hướng dẫn cho người dùng cuối, hãy chụp ảnh màn hình giao diện thực tế tại các buồng khám (Doctor Examine), màn hình thanh toán (Invoice Show) và biên nhận in ấn để tăng tính trực quan cho tài liệu hướng dẫn.*

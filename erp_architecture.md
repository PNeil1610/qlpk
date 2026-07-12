# Sơ đồ Kiến trúc Hệ thống & Cơ sở dữ liệu ERP (QLPK)

Tài liệu này chứa các sơ đồ kiến trúc hệ thống, sơ đồ quan hệ thực thể (ERD), ma trận phân quyền (RBAC) và quy trình nghiệp vụ lâm sàng dưới dạng mã **Mermaid.js** đã được tinh chỉnh để **tương thích hoàn toàn với Draw.io (độ ổn định tối đa khi import)**.

> [!TIP]
> **Cách import vào Draw.io:**
> 1. Trên thanh công cụ Draw.io, chọn **Arrange > Insert > Advanced > Mermaid...**
> 2. Copy đoạn mã Mermaid tương ứng từ tài liệu này (chọn Phần 1 hoặc Phần 3) và paste vào khung nhập liệu.
> 3. Chọn **Insert** để Draw.io tự động vẽ các khối có thể chỉnh sửa trực tiếp.
> 4. *Lưu ý:* Draw.io không hỗ trợ biểu đồ ERD (Phần 2) bằng công cụ import này. Với ERD, vui lòng xem trực tiếp trên GitHub/VS Code hoặc import dưới dạng SQL Schema DDL.

---

## 1. Kiến trúc Phân lớp Hệ thống (System Architecture Layers)

Sơ đồ phân lớp công nghệ của phòng khám từ Client đến hạ tầng cơ sở dữ liệu. Sử dụng định dạng đơn giản không dùng dấu ngoặc kép bên trong mô tả nút để tránh lỗi parser của Draw.io:

```mermaid
graph TD
    subgraph CLIENTS [Clients and User Roles]
        Admin[Admin - Quản trị viên]
        Doctor[Doctor - Bác sĩ]
        Cashier[Cashier - Thu ngân]
        Pharmacist[Pharmacist - Dược sĩ]
        Patient[Patient - Bệnh nhân]
        Receptionist[Receptionist - Tiếp đón]
    end

    subgraph AUTH [Gateway and Identity Provider]
        Keycloak[Keycloak OIDC Client SSO and SLO]
    end

    subgraph PRESENTATION [Presentation Layer Frontend]
        Blade[Laravel Blade Templates]
        CSS[Custom CSS custom-premium-style.css]
        JS[Vite / JS / jQuery and Select2]
    end

    subgraph APP [Application and Logic Layer Laravel]
        Middleware[RoleMiddleware RBAC Check]
        AdminCtrl[Admin Controllers]
        DoctorCtrl[Doctor Controllers]
        CashierCtrl[Cashier Controllers]
        PharmacistCtrl[Pharmacist Controllers]
        PatientCtrl[Patient Controllers]
        NotifCtrl[Notification Controller]
    end

    subgraph DOMAIN [Domain Model Layer Eloquent ORM]
        UserM[User and Role Models]
        ClinicM[Clinic and Staff/Room Models]
        MedicalM[Patient and Consultation Models]
        PrescM[Prescription and Medicine Models]
        BillingM[Invoice Models]
    end

    subgraph INFRA [Infrastructure and Persistence]
        MySQL[(MySQL 8.0 Database)]
        Redis[(Redis Sessions and Cache)]
        Mailpit[Mailpit Email Capture]
    end

    CLIENTS --> AuthCheck{Xác thực truy cập}
    AuthCheck -- Redirect --> Keycloak
    Keycloak -- Login Success --> PRESENTATION
    PRESENTATION -- HTTP Request --> Middleware
    Middleware -- Valid Role --> APP
    APP --> DOMAIN
    DOMAIN --> INFRA
```

---

## 2. Sơ đồ Quan hệ Cơ sở dữ liệu (ERD)

Sơ đồ ERD thể hiện quan hệ thực thể của 23 Eloquent Models. Xem tốt nhất bằng trình đọc Markdown của VS Code hoặc GitHub:

```mermaid
erDiagram
    users ||--o| staff : "user_id"
    users ||--o| patient : "user_id"
    users }o--o{ roles : "users_roles"
    roles ||--o{ roles_permission : "role_id"
    department ||--o{ room : "department_id"
    room ||--o| room_detail : "room_id"
    department ||--o{ staff : "department_id"
    room ||--o{ staff : "room_id"
    patient ||--o{ consultation : "patient_id"
    staff ||--o{ consultation : "doctor_id"
    consultation ||--o{ consultation_room : "consultation_id"
    room ||--o{ consultation_room : "room_id"
    diagnosis ||--o{ consultation : "diagnosis_code"
    consultation ||--o{ prescription : "consultation_id"
    prescription ||--o{ prescription_detail : "prescription_id"
    medicine ||--o{ prescription_detail : "medicine_id"
    medicine_category ||--o{ medicine : "medicine_category_id"
    medicine ||--o{ medicine_detail : "medicine_id"
    staff ||--o{ prescription : "prescribed_by_or_dispensed_by"
    consultation ||--o| invoice : "consultation_id"
    invoice ||--o{ invoice_detail : "invoice_id"
    staff ||--o{ invoice : "cashier_id"
    users ||--o{ system_notification : "sender_id"
    users ||--o{ notification_reads : "user_id"
    system_notification ||--o{ notification_reads : "notification_id"
    staff ||--o{ prescription_template : "doctor_id"
    diagnosis ||--o{ prescription_template : "diagnosis_code"
    prescription_template ||--o{ prescription_template_detail : "template_id"
    medicine ||--o{ prescription_template_detail : "medicine_id"
    users ||--o{ system_setting : "created_by"
```

---

## 3. Quy trình Vận hành Lâm sàng End-to-End (Workflow)

Sơ đồ tuần tự chuẩn hóa luồng tiếp nhận, chẩn đoán, thu phí và cấp phát thuốc của QLPK. Đã loại bỏ tất cả các thẻ HTML `<br/>` trong phần Note để đảm bảo Draw.io nhận diện và vẽ thành công:

```mermaid
sequenceDiagram
    autonumber
    actor Patient as Bệnh nhân
    actor Receptionist as Lễ tân / Tiếp đón
    actor Doctor as Bác sĩ (Khám lâm sàng)
    actor Cashier as Thu ngân (Tài chính)
    actor Pharmacist as Dược sĩ (Nhà thuốc)

    Patient->>Receptionist: Đến khám & cung cấp thông tin SĐT/CCCD
    Note over Receptionist: PatientController@store - Tạo hoặc cập nhật hồ sơ Patient
    Receptionist->>Receptionist: Lập Phiếu khám & Đo sinh hiệu (Nhiệt độ, Huyết áp)
    Note over Receptionist: ConsultationController@store - Tạo Consultation mới STATUS_NEW
    Receptionist->>Receptionist: Xếp hàng chờ vào phòng khám chuyên khoa
    Note over Receptionist: ConsultationRoomController@store - Lưu ConsultationRoom
    
    Note over Doctor: Hàng chờ phòng khám tự động hiển thị ca bệnh mới
    Doctor->>Doctor: Mở EMR, click khám bệnh (Examine)
    Note over Doctor: ExaminationController@examine - Consultation sang STATUS_PROCESSING
    Doctor->>Doctor: Tra cứu lịch sử, chẩn đoán mã bệnh ICD-10
    Note over Doctor: Select2 AJAX tìm mã ICD-10 từ bảng Diagnosis
    Doctor->>Doctor: Kê đơn thuốc điện tử (Sử dụng toa mẫu hoặc đơn cũ)
    Note over Doctor: Select2 AJAX tìm Medicine và MedicineDetail
    Doctor->>Doctor: Lưu kết luận và Hướng điều trị
    Note over Doctor: ExaminationController@saveConclusion - Consultation sang STATUS_WAIT_PAYMENT

    Note over Cashier: Hàng chờ thanh toán hiển thị hóa đơn mới
    Cashier->>Cashier: Tra cứu hóa đơn của bệnh nhân
    Note over Cashier: InvoiceController@index và edit
    Cashier->>Cashier: Thêm phụ phí other_fee hoặc chiết khấu discount
    Note over Cashier: InvoiceController@update - Tự động tính toán lại total_amount
    Cashier->>Cashier: Xác nhận thanh toán (Cash/Card/Transfer)
    Note over Cashier: InvoiceController@pay - Invoice sang STATUS_PAID và Consultation sang STATUS_COMPLETED

    Note over Pharmacist: Hàng chờ cấp phát nhận đơn thuốc đã thanh toán
    Pharmacist->>Pharmacist: Soạn thuốc vật lý theo toa
    Pharmacist->>Pharmacist: Xác nhận đã cấp phát thuốc
    Note over Pharmacist: PrescriptionController@updateStatus - Prescription chuyển trạng thái Đã phát
    Pharmacist->>Patient: Trao thuốc và hướng dẫn sử dụng
    Patient->>Patient: Ra về và Đăng nhập cổng tra cứu lịch sử khám bệnh
    Note over Patient: MyRecordController@index (Role PATIENT)
```

---

## 4. Ma trận Phân quyền & Vai trò (RBAC Matrix)

| Vai trò | Mã Code (Role) | Mô tả phạm vi quyền hạn chính |
| :--- | :--- | :--- |
| **Quản trị viên** | `ADMIN` | Toàn quyền cấu hình hệ thống, quản lý Staff, cấu hình phòng/khoa, import Excel cho ICD-10 và Danh mục Thuốc gốc, xem Dashboard tổng số liệu doanh thu. |
| **Tiếp đón** | `RECEPTIONIST` | Tiếp nhận hồ sơ bệnh nhân mới/cũ, tạo Phiếu khám ban đầu, nhập chỉ số sinh hiệu, xếp hàng phân buồng khám vào phòng bác sĩ chuyên khoa. |
| **Bác sĩ khám** | `DOCTOR` | Xem hàng chờ khám phòng mình, thực hiện chẩn đoán EMR, nhập mã bệnh ICD-10, kê đơn thuốc và lưu kết luận điều trị, quản lý toa mẫu cá nhân. |
| **Thu ngân** | `CASHIER` | Tiếp nhận hàng chờ thanh toán của bệnh nhân, nhập phụ thu/chiết khấu, in biên nhận tài chính in ấn và bấm xác nhận hoàn tất thanh toán. |
| **Dược sĩ** | `PHARMACIST` | Xem hàng chờ cấp đơn thuốc đã đóng tiền, chuẩn bị thuốc vật lý, bấm xác nhận đã cấp phát thuốc để giải phóng đơn thuốc khỏi hàng đợi. |
| **Bệnh nhân** | `PATIENT` | Tự đăng nhập cổng tra cứu để xem toàn bộ lịch sử bệnh án cá nhân, chẩn đoán lâm sàng của bác sĩ và hướng dẫn sử dụng các đơn thuốc cũ. |

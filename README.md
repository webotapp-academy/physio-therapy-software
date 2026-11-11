# PhysioCare — Physiotherapy & Rehabilitation Center Management Software

[![Laravel 11](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%20%7C%208.3-777BB4?style=flat&logo=php)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?style=flat&logo=mysql)](https://mysql.com)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-v3.x-38B2AC?style=flat&logo=tailwind-css)](https://tailwindcss.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

A clinical and financial management software designed specifically for physiotherapy centers, orthopedic rehabilitation clinics, and mobile domiciliary home-care providers. 

Built with **Laravel 11**, **MySQL**, and styled with a **Sage Green & Warm Ivory** medical palette.

---

## Key Clinical & Operational Features

### 1. Domiciliary Home Visit & In-Clinic Appointment Dispatch
* **Clinic Appointments**: Token number generation, check-in queuing, and doctor assignment.
* **Domiciliary Home Service**: Home visit scheduling with address landmarks, travel distance calculation ($km$), automatic travel allowance / conveyance fees, and round-trip travel buffers to avoid scheduling overlaps.

### 2. Dual Billing Engine (Pay-Per-Session & Prepaid Packages)
* **Option A: Pay-Per-Session (Pay-As-You-Go)**
  * Itemized single-visit billing: Consultation / Therapy fee + Travel conveyance + Clinical consumables (K-Tape, dry needles, electrodes).
  * Instant tax invoice & official payment receipt generation (Cash, UPI QR, Card POS, Bank Transfer).
* **Option B: Multi-Session Therapy Packages & Visual Attendance Punch Card**
  * Sell 10-session or 20-session bundles (e.g., 10 Sessions @ ₹5,000) with package discounts.
  * **Interactive 10-Session Punch Card** on patient EMR profile showing completed visits (`#1 ✓ Done`), upcoming sessions (`#2 ★ Next`), and remaining quota.
  * **1-Click Attendance Punch**: Auto-deducts 1 session quota when patient arrives; logs clinical details without requiring new fee collection.
  * Automatic package expiration when all sessions are completed.

### 3. Comprehensive Electronic Medical Records (EMR)
* Detailed patient profiling with auto-generated Unique IDs (`PHY-YYYY-XXXX`).
* Chief complaints, medical/surgical history, red flags, allergies, and emergency contacts.
* **VAS Pain Scale (0–10)** tracking across every session to visualize patient recovery velocity over time.
* Modality tracking: IFT, Ultrasound 1MHz/3MHz, TENS, Dry Needling, Joint Mobilization, Traction, Cryo/Thermotherapy, Cupping, and Gait Training.

### 4. Role-Based Access Control (RBAC)
Tailored navigation menus and workspace dashboards for each staff persona:
* **Clinic Director / Admin (`admin`)**:
  * Full clinic oversight: Total invoiced revenue, Cash/UPI collections register, pay-per-session vs package earnings, unpaid balance alerts, and therapist workload.
* **Physiotherapist / Doctor (`therapist`)**:
  * Clinical workspace: My daily schedule, in-clinic vs home patients, modality logging, VAS pain tracker, and exercise prescription. *Financial metrics and billing registers are automatically hidden.*
* **Front Desk / Receptionist (`receptionist`)**:
  * Operational desk: Patient onboarding, appointment booking, desk fee collections, invoice receipts, and package sales.

### 5. Exercise Prescription Library & Home Care Programs
* Searchable exercise bank filtered by anatomical region (Cervical, Lumbar, Knee, Shoulder, Ankle).
* Standardized instructions, target repetitions $\times$ sets, hold times (seconds), and precautions.
* 1-click prescription to the patient's digital home regimen.

### 6. Calming Sage Green & Ivory Aesthetic
* Interface designed using a healthcare-aligned palette:
  * **Background**: Warm Ivory (`#FAF7F2`)
  * **Cards & Surfaces**: Soft Ivory (`#FDFCF9`) with warm borders (`#E6DFD3`)
  * **Primary Accents**: Sage Green (`#4D6D4A` / `#3C5539`)

---

## Demo Accounts & Default Roles

The database seeder includes 3 ready-to-test accounts:

| Role | Name | Email | Password | Access Scope |
| :--- | :--- | :--- | :--- | :--- |
| **Admin** | Dr. Sanjeev Roy (Director) | `admin@physio.local` | `admin123` | Complete clinic, financial & operational oversight |
| **Therapist** | Dr. Rajesh Sharma (Doctor) | `rajesh.physio@physio.local` | `password` | Clinical EMR, schedule, sessions & exercises |
| **Receptionist**| Kavita Das (Front Desk) | `reception@physio.local` | `password` | Patient registration, appointments & fee billing |

---

## Installation & Setup Guide

### Prerequisites
* **PHP**: `>= 8.2` (tested with PHP 8.2 and 8.3)
* **MySQL**: `>= 8.0` (or MariaDB in XAMPP)
* **Composer**: `>= 2.x`
* **Apache / Nginx** (e.g. XAMPP on port 80)

### 1. Clone the Repository
```bash
git clone https://github.com/webotapp-academy/physio-therapy-software.git physio
cd physio
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Environment Configuration
Copy `.env.example` to `.env` and generate the application key:
```bash
cp .env.example .env
php artisan key:generate
```

Configure your MySQL database settings in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=physio
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Run Migrations & Seed Sample Data
Execute the database migrations and seed default services, packages, demo users, exercises, and sample patients:
```bash
php artisan migrate --seed
```

### 5. Storage Directory Permissions
Ensure Laravel storage and cache directories are writable by the web server:
```bash
chmod -R 777 storage bootstrap/cache
```

### 6. Run the Application
* **Under XAMPP / Apache**: Place the folder in your `htdocs/physio` and browse to:
  ```
  http://localhost/physio/public/
  ```
* **Or via Artisan Built-in Server**:
  ```bash
  php artisan serve
  ```
  Access at `http://127.0.0.1:8000`.

---

## Running Automated Tests

The application includes a complete PHPUnit test suite covering authentication, patient creation, home-visit appointments, billing calculations, and role-based permissions:

```bash
php artisan test
# or
vendor/bin/phpunit
```

---

## Database Architecture Overview

```
users (role: admin, therapist, receptionist)
  ├── therapist_profiles (qualifications, home visit rates)
  │
patients (patient_uid: PHY-YYYY-XXXX, demographics, address, medical history)
  ├── appointments (service_location: clinic | home_visit, travel_distance_km, travel_charge)
  ├── treatment_plans (diagnosis, target goals, total planned sessions)
  ├── treatment_sessions (session_number, pain VAS, modalities_used, billing_type)
  ├── patient_packages (total_sessions, used_sessions, remaining_sessions, status)
  ├── invoices (billing_model: bill_per_session | package_upfront, subtotal, travel, total)
  │     └── payments (payment_method: upi | cash | card, transaction_reference)
  └── prescribed_exercises (exercise_id, sets, reps, hold_seconds, frequency)

services (consultation rates, home visit fee modifiers)
packages (prepaid multi-session bundles, e.g. 10 sessions @ ₹5,000)
exercises (exercise library, target body parts, instructions, precautions)
equipment (clinic equipment & portable home-visit kits)
```

---

## License

This project is open-source software licensed under the [MIT License](LICENSE).

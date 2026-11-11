<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Equipment;
use App\Models\Exercise;
use App\Models\Invoice;
use App\Models\Package;
use App\Models\Patient;
use App\Models\PatientPackage;
use App\Models\Payment;
use App\Models\Service;
use App\Models\TherapistProfile;
use App\Models\TreatmentPlan;
use App\Models\TreatmentSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users & Staff
        $admin = User::firstOrCreate(
            ['email' => 'admin@physio.local'],
            [
                'name' => 'Dr. Sanjeev Roy (Director)',
                'phone' => '+91 98765 43210',
                'role' => 'admin',
                'status' => 'active',
                'password' => Hash::make('admin123'),
            ]
        );

        $therapist1 = User::firstOrCreate(
            ['email' => 'rajesh.physio@physio.local'],
            [
                'name' => 'Dr. Rajesh Sharma',
                'phone' => '+91 98111 22334',
                'role' => 'therapist',
                'status' => 'active',
                'password' => Hash::make('password'),
            ]
        );

        TherapistProfile::firstOrCreate(
            ['user_id' => $therapist1->id],
            [
                'qualification' => 'MPT (Orthopedics), BPT, MIAP',
                'specialization' => 'Musculoskeletal & Sports Rehabilitation',
                'license_number' => 'DCPT-2015-849',
                'experience_years' => 9,
                'slot_duration_minutes' => 45,
                'consultation_fee' => 600.00,
                'home_visit_fee' => 950.00,
                'is_available_for_home_visits' => true,
                'bio' => 'Specializes in spine conditions, joint mobilization, and post-surgical athletic rehabilitation.',
            ]
        );

        $therapist2 = User::firstOrCreate(
            ['email' => 'ananya.physio@physio.local'],
            [
                'name' => 'Dr. Ananya Sen',
                'phone' => '+91 98222 33445',
                'role' => 'therapist',
                'status' => 'active',
                'password' => Hash::make('password'),
            ]
        );

        TherapistProfile::firstOrCreate(
            ['user_id' => $therapist2->id],
            [
                'qualification' => 'MPT (Neurology), Certified Dry Needling Therapist',
                'specialization' => 'Neuro Rehab & Stroke Care',
                'license_number' => 'DCPT-2018-312',
                'experience_years' => 6,
                'slot_duration_minutes' => 60,
                'consultation_fee' => 700.00,
                'home_visit_fee' => 1100.00,
                'is_available_for_home_visits' => true,
                'bio' => 'Expert in neurological disorders, geriatric gait training, and home domiciliary care.',
            ]
        );

        $receptionist = User::firstOrCreate(
            ['email' => 'reception@physio.local'],
            [
                'name' => 'Kavita Das (Front Desk)',
                'phone' => '+91 98333 44556',
                'role' => 'receptionist',
                'status' => 'active',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Services (Clinic & Home Service)
        $serviceConsultation = Service::firstOrCreate(
            ['code' => 'SRV-CONS-01'],
            [
                'name' => 'Comprehensive Clinical Consultation & Assessment',
                'service_type' => 'consultation',
                'description' => 'Detailed posture, ROM, manual muscle test, and provisional diagnosis.',
                'duration_minutes' => 45,
                'base_rate' => 500.00,
                'home_visit_additional_fee' => 300.00,
                'tax_percentage' => 0.00,
                'is_active' => true,
            ]
        );

        $serviceClinicPhysio = Service::firstOrCreate(
            ['code' => 'SRV-CLIN-PHY'],
            [
                'name' => 'In-Clinic Physiotherapy Session',
                'service_type' => 'clinic_session',
                'description' => 'Manual therapy, exercise therapy, and modalities (IFT/US/TENS) in clinic.',
                'duration_minutes' => 45,
                'base_rate' => 600.00,
                'home_visit_additional_fee' => 0.00,
                'tax_percentage' => 0.00,
                'is_active' => true,
            ]
        );

        $serviceHomePhysio = Service::firstOrCreate(
            ['code' => 'SRV-HOME-PHY'],
            [
                'name' => 'Home Visit Physiotherapy (Domiciliary Care)',
                'service_type' => 'home_visit_session',
                'description' => 'Physiotherapist visits patient residence with portable therapy equipment.',
                'duration_minutes' => 60,
                'base_rate' => 900.00,
                'home_visit_additional_fee' => 250.00, // Travel allowance / Distance fee
                'tax_percentage' => 0.00,
                'is_active' => true,
            ]
        );

        $serviceDryNeedling = Service::firstOrCreate(
            ['code' => 'SRV-MOD-NEEDLE'],
            [
                'name' => 'Dry Needling & Trigger Point Release',
                'service_type' => 'modality',
                'description' => 'Intramuscular stimulation targeting muscular trigger points and spasms.',
                'duration_minutes' => 30,
                'base_rate' => 750.00,
                'home_visit_additional_fee' => 0.00,
                'tax_percentage' => 0.00,
                'is_active' => true,
            ]
        );

        // 3. Packages
        $packageOrtho10 = Package::firstOrCreate(
            ['name' => '10-Session Orthopedic Rehabilitation Package'],
            [
                'service_id' => $serviceClinicPhysio->id,
                'package_type' => 'clinic',
                'total_sessions' => 10,
                'price' => 5200.00, // Discounted from 6,000
                'validity_days' => 45,
                'description' => 'Comprehensive 10-session clinic care package for spine, shoulder, and knee conditions.',
                'is_active' => true,
            ]
        );

        $packageHome10 = Package::firstOrCreate(
            ['name' => '10-Session Geriatric Home Care Package'],
            [
                'service_id' => $serviceHomePhysio->id,
                'package_type' => 'home_visit',
                'total_sessions' => 10,
                'price' => 10000.00, // Discounted from 11,500
                'validity_days' => 60,
                'description' => '10 Home visits by specialized neuro/geriatric physiotherapist including travel.',
                'is_active' => true,
            ]
        );

        // 4. Patients
        $patient1 = Patient::firstOrCreate(
            ['patient_uid' => 'PHY-2026-0001'],
            [
                'first_name' => 'Amit',
                'last_name' => 'Verma',
                'gender' => 'male',
                'dob' => '1984-06-15',
                'age' => 42,
                'blood_group' => 'B+',
                'phone' => '+91 98450 11223',
                'email' => 'amit.verma@example.com',
                'occupation' => 'Software Architect',
                'address' => 'Flat 402, Green Glen Heights, Bellandur',
                'landmark' => 'Near Central Mall',
                'city' => 'Bengaluru',
                'emergency_contact_name' => 'Sunita Verma (Wife)',
                'emergency_contact_phone' => '+91 98450 11224',
                'medical_history' => 'Sedentary desk job for 15+ years. Intermittent lower back stiffness.',
                'surgical_history' => 'None',
                'allergies' => 'None',
                'referral_source' => 'Dr. K. Mehta (Orthopedic Surgeon)',
                'status' => 'active',
            ]
        );

        $patient2 = Patient::firstOrCreate(
            ['patient_uid' => 'PHY-2026-0002'],
            [
                'first_name' => 'Meera',
                'last_name' => 'Patel',
                'gender' => 'female',
                'dob' => '1958-11-20',
                'age' => 68,
                'blood_group' => 'O+',
                'phone' => '+91 99123 44556',
                'email' => 'meera.family@example.com',
                'occupation' => 'Homemaker / Retired',
                'address' => 'Villa 14, Palm Meadows, Whitefield',
                'landmark' => 'Behind Forum Value Mall',
                'city' => 'Bengaluru',
                'latitude' => 12.9698,
                'longitude' => 77.7499,
                'emergency_contact_name' => 'Kunal Patel (Son)',
                'emergency_contact_phone' => '+91 99123 44557',
                'medical_history' => 'Hypertension, Left Total Knee Replacement (TKR) done 2 weeks ago. Requires home care.',
                'surgical_history' => 'Left TKR on 25-Oct-2026',
                'allergies' => 'Sulfa drugs',
                'referral_source' => 'Manipal Hospital Ortho Dept',
                'status' => 'active',
            ]
        );

        $patient3 = Patient::firstOrCreate(
            ['patient_uid' => 'PHY-2026-0003'],
            [
                'first_name' => 'Rohan',
                'last_name' => 'Kapoor',
                'gender' => 'male',
                'dob' => '1999-03-12',
                'age' => 27,
                'blood_group' => 'A+',
                'phone' => '+91 97333 88990',
                'email' => 'rohan.k@example.com',
                'occupation' => 'Tennis Coach / Athlete',
                'address' => '12B, Indiranagar 100ft Road',
                'landmark' => 'Near Metro Station',
                'city' => 'Bengaluru',
                'emergency_contact_name' => 'Deepak Kapoor (Father)',
                'emergency_contact_phone' => '+91 97333 88991',
                'medical_history' => 'Right shoulder impingement with supraspinatus tendinopathy.',
                'status' => 'active',
            ]
        );

        // 5. Patient Package for Patient 1
        $patPkg1 = PatientPackage::firstOrCreate(
            ['patient_id' => $patient1->id, 'package_id' => $packageOrtho10->id],
            [
                'total_sessions' => 10,
                'used_sessions' => 3,
                'purchase_date' => Carbon::today()->subDays(10),
                'expiry_date' => Carbon::today()->addDays(35),
                'price_paid' => 5200.00,
                'status' => 'active',
            ]
        );

        // 6. Appointments (1 In-Clinic, 1 Home Visit)
        $aptClinic = Appointment::firstOrCreate(
            ['appointment_no' => 'APT-'.Carbon::today()->format('Ymd').'-001'],
            [
                'patient_id' => $patient1->id,
                'therapist_id' => $therapist1->id,
                'service_id' => $serviceClinicPhysio->id,
                'service_location' => 'clinic',
                'appointment_date' => Carbon::today(),
                'start_time' => '10:00:00',
                'end_time' => '10:45:00',
                'status' => 'completed',
                'token_number' => 1,
                'notes' => 'Patient reporting reduced lower back pain. Performing lumbar stabilization.',
            ]
        );

        $aptHome = Appointment::firstOrCreate(
            ['appointment_no' => 'APT-'.Carbon::today()->format('Ymd').'-002'],
            [
                'patient_id' => $patient2->id,
                'therapist_id' => $therapist2->id,
                'service_id' => $serviceHomePhysio->id,
                'service_location' => 'home_visit',
                'home_address' => $patient2->address,
                'home_landmark' => $patient2->landmark,
                'travel_distance_km' => 6.5,
                'travel_charge' => 250.00,
                'appointment_date' => Carbon::today(),
                'start_time' => '14:30:00',
                'end_time' => '15:30:00',
                'travel_buffer_minutes' => 30,
                'status' => 'scheduled',
                'notes' => 'Post-TKR day 16. Carry portable IFT & cryo-pack for knee swelling.',
            ]
        );

        // 7. Treatment Session 1 (Under Package Deduction)
        $plan1 = TreatmentPlan::firstOrCreate(
            ['patient_id' => $patient1->id, 'therapist_id' => $therapist1->id],
            [
                'diagnosis' => 'L4-L5 Disc Bulge with Lumbar Radiculopathy',
                'prescribed_sessions' => 10,
                'completed_sessions' => 3,
                'frequency_per_week' => 3,
                'start_date' => Carbon::today()->subDays(10),
                'expected_end_date' => Carbon::today()->addDays(20),
                'status' => 'active',
            ]
        );

        $session1 = TreatmentSession::firstOrCreate(
            ['appointment_id' => $aptClinic->id],
            [
                'treatment_plan_id' => $plan1->id,
                'patient_id' => $patient1->id,
                'therapist_id' => $therapist1->id,
                'session_number' => 3,
                'session_date' => Carbon::today(),
                'service_location' => 'clinic',
                'modalities_used' => ['IFT (Interferential Therapy)', 'Ultrasound Therapy', 'Core Stabilization'],
                'exercises_done' => 'Pelvic Tilts, Bridging with therapy ball, Cat-Camel stretches.',
                'therapist_notes' => 'VAS pain down from 7/10 to 3/10. Lumbar flexion improved by 15 degrees.',
                'current_pain_vas' => 3,
                'billing_type' => 'package_deduction',
                'patient_package_id' => $patPkg1->id,
                'payment_status' => 'package_covered',
            ]
        );

        // 8. Session 2 (Pay-Per-Session Home Visit Model for Patient 3)
        $aptPatient3 = Appointment::firstOrCreate(
            ['appointment_no' => 'APT-'.Carbon::yesterday()->format('Ymd').'-003'],
            [
                'patient_id' => $patient3->id,
                'therapist_id' => $therapist1->id,
                'service_id' => $serviceDryNeedling->id,
                'service_location' => 'clinic',
                'appointment_date' => Carbon::yesterday(),
                'start_time' => '16:00:00',
                'end_time' => '16:45:00',
                'status' => 'completed',
                'token_number' => 5,
                'notes' => 'Dry needling to infraspinatus & upper trapezius.',
            ]
        );

        $session3 = TreatmentSession::firstOrCreate(
            ['appointment_id' => $aptPatient3->id],
            [
                'patient_id' => $patient3->id,
                'therapist_id' => $therapist1->id,
                'session_number' => 1,
                'session_date' => Carbon::yesterday(),
                'service_location' => 'clinic',
                'modalities_used' => ['Dry Needling', 'Cryotherapy', 'Kinesiology Taping'],
                'exercises_done' => 'Rotator cuff isometric external rotation, Scapular retractions.',
                'therapist_notes' => 'Trigger points de-activated in infraspinatus. Applied stabilizing K-Tape.',
                'current_pain_vas' => 4,
                'billing_type' => 'per_session', // BILL PER SESSION
                'payment_status' => 'paid',
            ]
        );

        // Invoice for Session 3 (Pay Per Session)
        $invPerSession = Invoice::firstOrCreate(
            ['invoice_no' => 'INV-'.Carbon::yesterday()->format('Ymd').'-001'],
            [
                'patient_id' => $patient3->id,
                'billing_model' => 'bill_per_session',
                'appointment_id' => $aptPatient3->id,
                'treatment_session_id' => $session3->id,
                'service_amount' => 750.00,
                'travel_charge' => 0.00,
                'consumables_amount' => 150.00, // K-tape consumable
                'subtotal' => 900.00,
                'discount_amount' => 0.00,
                'tax_amount' => 0.00,
                'total_amount' => 900.00,
                'paid_amount' => 900.00,
                'balance_amount' => 0.00,
                'payment_status' => 'paid',
                'invoice_date' => Carbon::yesterday(),
                'notes' => 'Single session fee + Kinesiology Tape',
                'created_by' => $receptionist->id,
            ]
        );

        Payment::firstOrCreate(
            ['payment_no' => 'PAY-'.Carbon::yesterday()->format('Ymd').'-001'],
            [
                'invoice_id' => $invPerSession->id,
                'patient_id' => $patient3->id,
                'amount' => 900.00,
                'payment_method' => 'upi',
                'transaction_reference' => 'UPI/GPay/839401294',
                'payment_date' => Carbon::yesterday()->setTime(16, 50),
                'received_by' => $receptionist->id,
                'notes' => 'Received via GPay QR Code at front desk',
            ]
        );

        // 9. Exercise Library
        $exercises = [
            [
                'name' => 'Cervical Retraction (Chin Tucks)',
                'target_body_part' => 'Cervical (Neck)',
                'category' => 'postural',
                'difficulty_level' => 'beginner',
                'instructions' => 'Sit upright with shoulders relaxed. Gently glide your chin straight back as if making a double chin. Hold for 5 seconds without tilting head downward.',
                'precautions' => 'Do not flex your neck down. Movement is purely horizontal retraction.',
                'image_url' => 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=600',
            ],
            [
                'name' => 'Supine Pelvic Bridging',
                'target_body_part' => 'Lumbar & Glutes',
                'category' => 'strengthening',
                'difficulty_level' => 'beginner',
                'instructions' => 'Lie flat on your back with knees bent and feet flat on floor hip-width apart. Engage glutes and lift hips toward ceiling until thighs and torso align. Hold for 5 seconds.',
                'precautions' => 'Avoid hyperextending lower back. Squeeze glutes, not lower back.',
                'image_url' => 'https://images.unsplash.com/photo-1518611012118-696072aa579a?w=600',
            ],
            [
                'name' => 'Isometric Quadriceps Setting',
                'target_body_part' => 'Knee',
                'category' => 'strengthening',
                'difficulty_level' => 'beginner',
                'instructions' => 'Sit with leg extended straight. Place a small rolled towel under your knee. Contract thigh muscle to press knee down into towel. Hold 6 seconds.',
                'precautions' => 'Do not hold your breath during muscle contraction.',
                'image_url' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=600',
            ],
            [
                'name' => 'Wall Slides with Scapular Retraction',
                'target_body_part' => 'Shoulder & Upper Back',
                'category' => 'mobility',
                'difficulty_level' => 'intermediate',
                'instructions' => 'Stand with back against a wall. Place forearms vertically against wall. Slowly slide forearms up while maintaining contact with wall and squeezing shoulder blades.',
                'precautions' => 'Stop if sharp impingement pain is felt in front of shoulder.',
                'image_url' => 'https://images.unsplash.com/photo-1599447421416-3414500d18a5?w=600',
            ],
        ];

        foreach ($exercises as $ex) {
            Exercise::firstOrCreate(['name' => $ex['name']], $ex);
        }

        // 10. Equipment
        $equipments = [
            [
                'name' => 'Portable 4-Channel IFT & TENS Unit',
                'equipment_code' => 'EQ-PORT-IFT-01',
                'is_portable' => true,
                'current_location' => 'clinic',
                'serial_number' => 'ELC-2024-9182',
                'purchase_date' => '2024-04-10',
                'next_service_due' => Carbon::today()->addMonths(4),
                'status' => 'operational',
                'notes' => 'Primary kit assigned for home visit physiotherapy.',
            ],
            [
                'name' => 'Therapeutic Ultrasound Unit 1 & 3 MHz',
                'equipment_code' => 'EQ-US-02',
                'is_portable' => false,
                'current_location' => 'clinic',
                'serial_number' => 'US-2023-4102',
                'purchase_date' => '2023-08-15',
                'next_service_due' => Carbon::today()->addMonths(2),
                'status' => 'operational',
                'notes' => 'Stationary clinic unit in Treatment Room 1.',
            ],
            [
                'name' => 'Motorized Lumbar & Cervical Traction Table',
                'equipment_code' => 'EQ-TRAC-01',
                'is_portable' => false,
                'current_location' => 'clinic',
                'serial_number' => 'TRAC-2022-7711',
                'purchase_date' => '2022-12-01',
                'next_service_due' => Carbon::today()->addMonths(1),
                'status' => 'operational',
                'notes' => 'Traction room. Belt calibrated recently.',
            ],
        ];

        foreach ($equipments as $eq) {
            Equipment::firstOrCreate(['equipment_code' => $eq['equipment_code']], $eq);
        }
    }
}

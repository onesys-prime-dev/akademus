-- ============================================================
-- akademus_db FIXED IMPORT
-- Merges March17 backup + new normalized requirements schema
-- Import this into a FRESH akademus_db
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ============================================================
-- TABLES (no FK dependencies first)
-- ============================================================

CREATE TABLE `academic_years` (
  `year_id` int(11) NOT NULL,
  `year_name` varchar(20) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_current` tinyint(1) DEFAULT 0,
  `start_date_admission` date DEFAULT NULL,
  `end_date_admission` date DEFAULT NULL,
  `start_date_enrollment` date DEFAULT NULL,
  `end_date_enrollment` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `programs` (
  `program_id` int(11) NOT NULL,
  `program_code` varchar(20) DEFAULT NULL,
  `program_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `programs` VALUES
(1, 'BSCS', 'Bachelor of Science in Computer Science', '2026-02-24 09:30:27'),
(2, 'BSIT', 'Bachelor of Science in Information Technology', '2026-02-24 09:30:27'),
(3, 'BSIS', 'Bachelor of Science in Information Systems', '2026-02-24 09:30:27');

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `role` varchar(50) DEFAULT 'applicant',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` VALUES
(3, 'THEODORE', 'ABELLA', 'testakademusmentora@gmail.com', '09762402857', 'applicant', '2026-03-13 04:48:48'),
(4, 'Akademus', 'Admin', 'admin@akademus.edu.ph', '09000000000', 'admin', '2026-03-13 04:52:57'),
(11, 'Josh', 'Baquiano', 'joshbaquiano@gmail.com', '09123456789', 'applicant', '2026-03-17 07:45:01');

CREATE TABLE `admin_credentials` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `admin_credentials` VALUES
(0, 4, '$2y$10$nCL21uigGtSBjXrJOY03S.eZhbD9g.CLHq3HOyAqkbk4giNC8i/AC', '2026-03-13 04:52:57'),
(4, 66, '$2y$10$nCL21uigGtSBjXrJOY03S.eZhbD9g.CLHq3HOyAqkbk4giNC8i/AC', '2026-03-10 02:25:12');

CREATE TABLE `applicants` (
  `applicant_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `applicant_number` varchar(30) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `temp_password` tinyint(1) DEFAULT 1,
  `application_status` enum('pending','approved','rejected','enrolled') DEFAULT 'pending',
  `application_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `form_step` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `applicants` VALUES
(2, 11, 1, 'APP-2026-00002', '$2y$10$7vN.eTrsn15jlUfpCqCpaOuDF0Ci2GGcG.VlUbvqwa7Efym/QIE5q', 1, 'pending', '2026-03-17 07:45:01', '2026-03-17 07:45:01', 1);

CREATE TABLE `applicant_documents` (
  `document_id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `program_requirement_id` int(11) NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `status` enum('pending','verified','rejected') DEFAULT 'pending',
  `verified_by` int(11) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `applications` (
  `application_id` int(11) NOT NULL,
  `applicant_id` int(11) DEFAULT NULL,
  `applicant_type` varchar(50) DEFAULT NULL,
  `program_id` int(11) DEFAULT NULL,
  `school_year` varchar(50) DEFAULT NULL,
  `semester` varchar(50) DEFAULT NULL,
  `application_status` enum('draft','pending','approved','rejected','enrolled') NOT NULL DEFAULT 'pending',
  `application_date` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `class_schedules` (
  `schedule_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `section_name` varchar(20) DEFAULT NULL,
  `year_level` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `educational_backgrounds` (
  `educational_bg_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `school_name` varchar(255) DEFAULT NULL,
  `school_location` varchar(255) DEFAULT NULL,
  `strand` varchar(150) DEFAULT NULL,
  `school_year_start` year(4) DEFAULT NULL,
  `school_year_end` year(4) DEFAULT NULL,
  `gwa` decimal(5,2) DEFAULT NULL,
  `school_type` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `family_backgrounds` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `father_firstname` varchar(100) DEFAULT NULL,
  `father_middlename` varchar(100) DEFAULT NULL,
  `father_lastname` varchar(100) DEFAULT NULL,
  `father_occupation` varchar(100) DEFAULT NULL,
  `father_contact_number` varchar(50) DEFAULT NULL,
  `mother_firstname` varchar(100) DEFAULT NULL,
  `mother_middlename` varchar(100) DEFAULT NULL,
  `mother_lastname` varchar(100) DEFAULT NULL,
  `mother_occupation` varchar(100) DEFAULT NULL,
  `mother_contact_number` varchar(50) DEFAULT NULL,
  `guardian_name` varchar(150) DEFAULT NULL,
  `guardian_relationship` varchar(50) DEFAULT NULL,
  `guardian_contact_number` varchar(50) DEFAULT NULL,
  `annual_family_income` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `amount` double DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` enum('pending','paid') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `personal_informations` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `gender` varchar(50) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `place_of_birth` varchar(255) DEFAULT NULL,
  `citizenship` varchar(100) DEFAULT NULL,
  `home_address` varchar(255) DEFAULT NULL,
  `phone_code` varchar(5) DEFAULT '+63',
  `contact_number` varchar(50) DEFAULT NULL,
  `email_address` varchar(200) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `form_pages` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `form_pages` VALUES
(1, 'Application Type', 'Select applicant type, program, and school year.', 1, 1, 10, '2026-03-11 08:58:37', '2026-03-16 17:02:51'),
(2, 'Personal Information', 'Full name, date of birth, contact details, and address.', 1, 1, 20, '2026-03-11 08:58:37', '2026-03-16 17:02:53'),
(3, 'Family Background', 'Father, mother, guardian info, and family income.', 1, 1, 30, '2026-03-11 08:58:37', '2026-03-12 21:37:53'),
(4, 'Educational Background', 'Most recent senior high school information.', 1, 1, 40, '2026-03-11 08:58:37', '2026-03-12 21:37:53');

CREATE TABLE `form_field_configs` (
  `id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL DEFAULT 1,
  `field_key` varchar(100) NOT NULL,
  `step` tinyint(4) NOT NULL,
  `section` varchar(60) NOT NULL,
  `label` varchar(200) NOT NULL,
  `field_type` varchar(30) NOT NULL DEFAULT 'text',
  `placeholder` varchar(255) DEFAULT NULL,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `is_system` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `form_field_configs` (`id`, `page_id`, `field_key`, `step`, `section`, `label`, `field_type`, `placeholder`, `is_visible`, `is_required`, `is_system`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'applicant_type', 1, 'step1-core', 'Applicant Type', 'radio', '', 1, 1, 1, 10, '2026-03-13 05:25:52', '2026-03-16 17:02:40'),
(2, 1, 'program_id', 1, 'step1-core', 'Confirmed Program', 'select', 'Select a Program', 1, 1, 1, 20, '2026-03-13 05:25:52', '2026-03-16 17:02:40'),
(3, 1, 'school_year', 1, 'step1-core', 'School Year', 'select', 'Select School Year', 1, 1, 1, 30, '2026-03-13 05:25:52', '2026-03-16 17:02:40'),
(4, 1, 'semester', 1, 'step1-core', 'Semester', 'select', 'Select Semester', 1, 1, 1, 40, '2026-03-13 05:25:52', '2026-03-16 17:02:40'),
(5, 2, 'gender', 2, 'step2-details', 'Gender', 'select', 'Select Gender', 1, 1, 1, 50, '2026-03-13 05:25:52', '2026-03-13 05:30:56'),
(6, 2, 'date_of_birth', 2, 'step2-details', 'Date of Birth', 'date', '', 1, 1, 1, 60, '2026-03-13 05:25:52', '2026-03-13 05:30:56'),
(7, 2, 'place_of_birth', 2, 'step2-details', 'Place of Birth', 'text', 'City, Province', 1, 1, 1, 70, '2026-03-13 05:25:52', '2026-03-13 05:30:56'),
(8, 2, 'citizenship', 2, 'step2-details', 'Citizenship', 'text', 'e.g., Filipino', 1, 1, 1, 80, '2026-03-13 05:25:52', '2026-03-13 05:30:56'),
(9, 2, 'home_address', 2, 'step2-details', 'Home Address', 'text', 'Street, Barangay, City, Province', 1, 1, 1, 90, '2026-03-13 05:25:52', '2026-03-13 05:30:56'),
(10, 2, 'contact_number', 2, 'step2-details', 'Contact Number', 'tel', '+63 9XX XXX XXXX', 1, 1, 1, 100, '2026-03-13 05:25:52', '2026-03-13 05:30:56'),
(11, 2, 'email_address', 2, 'step2-details', 'Email Address', 'email', 'e.g., sample@email.com', 1, 1, 1, 110, '2026-03-13 05:25:52', '2026-03-13 05:30:56'),
(12, 2, 'firstname', 2, 'step2-name', 'First Name', 'text', 'e.g., Juan', 1, 1, 1, 10, '2026-03-13 05:25:52', '2026-03-13 05:30:56'),
(13, 2, 'middlename', 2, 'step2-name', 'Middle Name', 'text', 'e.g., Sanchez', 1, 0, 1, 20, '2026-03-13 05:25:52', '2026-03-13 05:30:56'),
(14, 2, 'lastname', 2, 'step2-name', 'Last Name', 'text', 'e.g., Dela Cruz', 1, 1, 1, 30, '2026-03-13 05:25:52', '2026-03-13 05:30:56'),
(15, 2, 'suffix', 2, 'step2-name', 'Suffix', 'select', '', 1, 0, 1, 40, '2026-03-13 05:25:52', '2026-03-13 05:30:56'),
(16, 3, 'father_firstname', 3, 'step3-father', 'Father\'s First Name', 'text', 'e.g., Juan', 1, 0, 1, 10, '2026-03-13 05:25:52', '2026-03-12 21:37:10'),
(17, 3, 'father_middlename', 3, 'step3-father', 'Father\'s Middle Name', 'text', 'e.g., Sanchez', 1, 0, 1, 20, '2026-03-13 05:25:52', '2026-03-12 21:37:10'),
(18, 3, 'father_lastname', 3, 'step3-father', 'Father\'s Last Name', 'text', 'e.g., Dela Cruz', 1, 0, 1, 30, '2026-03-13 05:25:52', '2026-03-12 21:37:10'),
(19, 3, 'father_occupation', 3, 'step3-father', 'Father\'s Occupation', 'text', 'e.g., Accountant', 1, 0, 1, 40, '2026-03-13 05:25:52', '2026-03-12 21:37:10'),
(20, 3, 'father_contact_number', 3, 'step3-father', 'Father\'s Contact Number', 'tel', '+63 9XX XXX XXXX', 1, 0, 1, 50, '2026-03-13 05:25:52', '2026-03-12 21:37:10'),
(21, 3, 'guardian_name', 3, 'step3-guardian', 'Guardian Name', 'text', 'If Different from Parents', 1, 0, 1, 10, '2026-03-13 05:25:52', '2026-03-12 21:37:10'),
(22, 3, 'guardian_relationship', 3, 'step3-guardian', 'Relationship', 'select', 'Select relationship', 1, 0, 1, 20, '2026-03-13 05:25:52', '2026-03-12 21:37:10'),
(23, 3, 'guardian_contact_number', 3, 'step3-guardian', 'Guardian Contact Number', 'tel', '+63 9XX XXX XXXX', 1, 0, 1, 30, '2026-03-13 05:25:52', '2026-03-12 21:37:10'),
(24, 3, 'annual_family_income', 3, 'step3-guardian', 'Annual Family Income', 'select', 'Select income range', 1, 0, 1, 40, '2026-03-13 05:25:52', '2026-03-12 21:37:10'),
(25, 3, 'mother_firstname', 3, 'step3-mother', 'Mother\'s First Name', 'text', 'e.g., Mary', 1, 0, 1, 10, '2026-03-13 05:25:52', '2026-03-12 21:37:10'),
(26, 3, 'mother_middlename', 3, 'step3-mother', 'Mother\'s Middle Name', 'text', 'e.g., Alcantara', 1, 0, 1, 20, '2026-03-13 05:25:52', '2026-03-12 21:37:10'),
(27, 3, 'mother_lastname', 3, 'step3-mother', 'Mother\'s Last Name', 'text', 'e.g., Delos Santos', 1, 0, 1, 30, '2026-03-13 05:25:52', '2026-03-12 21:37:10'),
(28, 3, 'mother_occupation', 3, 'step3-mother', 'Mother\'s Occupation', 'text', 'e.g., Accountant', 1, 0, 1, 40, '2026-03-13 05:25:52', '2026-03-12 21:37:10'),
(29, 3, 'mother_contact_number', 3, 'step3-mother', 'Mother\'s Contact Number', 'tel', '+63 9XX XXX XXXX', 1, 0, 1, 50, '2026-03-13 05:25:52', '2026-03-12 21:37:10'),
(30, 4, 'school_name', 4, 'step4-main', 'School Name', 'text', 'Full Name of your Senior High School', 1, 1, 1, 10, '2026-03-13 05:25:52', '2026-03-12 21:46:25'),
(31, 4, 'school_location', 4, 'step4-main', 'Location', 'text', 'City, Province', 1, 1, 1, 20, '2026-03-13 05:25:52', '2026-03-12 21:46:25'),
(32, 4, 'strand', 4, 'step4-main', 'Track / Strand', 'select', 'Select track / strand', 1, 1, 1, 30, '2026-03-13 05:25:52', '2026-03-12 21:46:25'),
(33, 4, 'school_year_start', 4, 'step4-main', 'School Year Start', 'number', 'e.g. 2024', 1, 1, 1, 40, '2026-03-13 05:25:52', '2026-03-12 21:46:25'),
(34, 4, 'school_year_end', 4, 'step4-main', 'School Year End', 'number', 'e.g. 2025', 1, 1, 1, 50, '2026-03-13 05:25:52', '2026-03-12 21:46:25'),
(35, 4, 'gwa', 4, 'step4-main', 'General Average (GWA)', 'text', 'e.g., 92.3', 1, 1, 1, 60, '2026-03-13 05:25:52', '2026-03-12 21:46:25'),
(36, 4, 'school_type', 4, 'step4-main', 'School Type', 'select', 'Select school type', 1, 1, 1, 70, '2026-03-13 05:25:52', '2026-03-12 21:46:25');

-- ============================================================
-- NEW REQUIREMENTS SCHEMA (replaces old flat tables)
-- ============================================================

CREATE TABLE `requirement_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `requirement_categories` VALUES
(1, 'Personal Documents', 'Basic personal identification documents', 1, 1, NULL, NULL),
(2, 'Academic Documents', 'Educational background and records', 2, 1, NULL, NULL),
(3, 'Identification', 'Government-issued IDs for verification', 3, 1, NULL, NULL),
(4, 'Medical Requirements', 'Health-related documents and clearances', 4, 1, NULL, NULL);

CREATE TABLE `requirements` (
  `requirement_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `requirement_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `requirements` VALUES
(1, 1, 'Birth Certificate', 'PSA-issued birth certificate', 1, 1, NULL, NULL),
(2, 1, 'Certificate of Good Moral Character', 'Issued by previous school', 2, 1, NULL, NULL),
(3, 2, 'Transcript of Records', 'Official academic transcript', 1, 1, NULL, NULL),
(4, 2, 'Diploma', 'High school or previous degree diploma', 2, 1, NULL, NULL),
(5, 2, 'Form 137', 'Secondary school permanent record', 3, 1, NULL, NULL),
(6, 3, 'Valid Government ID', 'Any government-issued ID (e.g., Passport, UMID)', 1, 1, NULL, NULL),
(7, 3, '2x2 ID Picture', 'Recent 2x2 photo with white background', 2, 1, NULL, NULL),
(8, 4, 'Medical Certificate', 'General health clearance from a licensed physician', 1, 1, NULL, NULL),
(9, 4, 'Chest X-ray Result', 'Recent X-ray result for medical screening', 2, 1, NULL, NULL),
(10, 4, 'Drug Test Result', 'Laboratory-issued drug test result', 3, 1, NULL, NULL);

CREATE TABLE `program_requirement_categories` (
  `program_requirement_category_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `program_requirement_categories` VALUES
(1, 1, 1, 1, 1, NULL, NULL),
(2, 1, 2, 2, 1, NULL, NULL),
(3, 1, 3, 3, 1, NULL, NULL),
(4, 1, 4, 4, 1, NULL, NULL),
(5, 2, 4, 4, 1, NULL, NULL),
(6, 2, 2, 2, 1, NULL, NULL),
(7, 2, 1, 1, 1, NULL, NULL),
(8, 2, 3, 3, 1, NULL, NULL);

CREATE TABLE `program_requirements` (
  `program_requirement_id` int(11) NOT NULL,
  `program_requirement_category_id` int(11) NOT NULL,
  `requirement_id` int(11) NOT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `is_mandatory` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `program_requirements` VALUES
(1, 1, 1, 1, 1, 1, NULL, NULL),
(2, 1, 2, 2, 1, 1, NULL, NULL),
(3, 2, 3, 1, 1, 1, NULL, NULL),
(4, 2, 4, 2, 1, 1, NULL, NULL),
(5, 2, 5, 3, 1, 0, NULL, NULL),
(6, 3, 6, 1, 1, 1, NULL, NULL),
(7, 3, 7, 2, 1, 1, NULL, NULL),
(8, 4, 8, 1, 1, 0, NULL, NULL),
(9, 4, 9, 2, 1, 0, NULL, NULL),
(10, 4, 10, 3, 1, 0, NULL, NULL),
(11, 5, 8, 1, 1, 1, NULL, NULL),
(12, 5, 9, 2, 1, 1, NULL, NULL),
(13, 5, 10, 3, 1, 1, NULL, NULL),
(14, 6, 3, 1, 1, 1, NULL, NULL),
(15, 6, 4, 2, 1, 1, NULL, NULL),
(16, 7, 1, 1, 1, 1, NULL, NULL),
(17, 7, 2, 2, 1, 1, NULL, '2026-03-17 21:54:01'),
(18, 8, 6, 1, 1, 0, NULL, NULL),
(19, 8, 7, 2, 1, 0, NULL, NULL);

-- theme_settings omitted for brevity — import separately if needed

-- ============================================================
-- PRIMARY KEYS
-- ============================================================

ALTER TABLE `academic_years` ADD PRIMARY KEY (`year_id`);
ALTER TABLE `admin_credentials` ADD PRIMARY KEY (`id`), ADD KEY `fk_admin_user` (`user_id`);
ALTER TABLE `applicants` ADD PRIMARY KEY (`applicant_id`), ADD UNIQUE KEY `applicant_number` (`applicant_number`), ADD KEY `fk_applicant_user` (`user_id`);
ALTER TABLE `applicant_documents` ADD PRIMARY KEY (`document_id`), ADD KEY `applicant_id` (`applicant_id`), ADD KEY `program_requirement_id` (`program_requirement_id`);
ALTER TABLE `applications` ADD PRIMARY KEY (`application_id`), ADD KEY `fk_application_applicant` (`applicant_id`), ADD KEY `fk_applications_program` (`program_id`);
ALTER TABLE `class_schedules` ADD PRIMARY KEY (`schedule_id`), ADD KEY `fk_schedule_program` (`program_id`), ADD KEY `fk_schedule_academic_year` (`academic_year_id`);
ALTER TABLE `educational_backgrounds` ADD PRIMARY KEY (`educational_bg_id`), ADD KEY `fk_education_bg_application` (`application_id`);
ALTER TABLE `family_backgrounds` ADD PRIMARY KEY (`id`), ADD KEY `fk_fam_bg_application` (`application_id`);
ALTER TABLE `form_field_configs` ADD PRIMARY KEY (`id`);
ALTER TABLE `form_pages` ADD PRIMARY KEY (`id`);
ALTER TABLE `payment` ADD PRIMARY KEY (`payment_id`);
ALTER TABLE `personal_informations` ADD PRIMARY KEY (`id`), ADD KEY `fk_personal_info_application` (`application_id`);
ALTER TABLE `programs` ADD PRIMARY KEY (`program_id`);
ALTER TABLE `users` ADD PRIMARY KEY (`user_id`), ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `requirement_categories` ADD PRIMARY KEY (`category_id`);
ALTER TABLE `requirements` ADD PRIMARY KEY (`requirement_id`), ADD KEY `category_id` (`category_id`);
ALTER TABLE `program_requirement_categories`
  ADD PRIMARY KEY (`program_requirement_category_id`),
  ADD UNIQUE KEY `program_id` (`program_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);
ALTER TABLE `program_requirements`
  ADD PRIMARY KEY (`program_requirement_id`),
  ADD UNIQUE KEY `program_requirement_category_id` (`program_requirement_category_id`,`requirement_id`),
  ADD KEY `requirement_id` (`requirement_id`);

-- ============================================================
-- AUTO_INCREMENT
-- ============================================================

ALTER TABLE `academic_years` MODIFY `year_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `applicants` MODIFY `applicant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `applications` MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
ALTER TABLE `class_schedules` MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `educational_backgrounds` MODIFY `educational_bg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
ALTER TABLE `family_backgrounds` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
ALTER TABLE `form_field_configs` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
ALTER TABLE `form_pages` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `personal_informations` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
ALTER TABLE `programs` MODIFY `program_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `users` MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
ALTER TABLE `requirement_categories` MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `requirements` MODIFY `requirement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
ALTER TABLE `program_requirement_categories` MODIFY `program_requirement_category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
ALTER TABLE `program_requirements` MODIFY `program_requirement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

-- ============================================================
-- FOREIGN KEYS
-- ============================================================

ALTER TABLE `applications`
  ADD CONSTRAINT `fk_application_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_applications_program` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `class_schedules`
  ADD CONSTRAINT `fk_schedule_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`year_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_schedule_program` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`) ON DELETE CASCADE;

ALTER TABLE `educational_backgrounds`
  ADD CONSTRAINT `fk_education_bg_application` FOREIGN KEY (`application_id`) REFERENCES `applications` (`application_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `family_backgrounds`
  ADD CONSTRAINT `fk_fam_bg_application` FOREIGN KEY (`application_id`) REFERENCES `applications` (`application_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `personal_informations`
  ADD CONSTRAINT `fk_personal_info_application` FOREIGN KEY (`application_id`) REFERENCES `applications` (`application_id`) ON DELETE CASCADE;

ALTER TABLE `requirements`
  ADD CONSTRAINT `requirements_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `requirement_categories` (`category_id`);

ALTER TABLE `program_requirement_categories`
  ADD CONSTRAINT `program_requirement_categories_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`),
  ADD CONSTRAINT `program_requirement_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `requirement_categories` (`category_id`);

ALTER TABLE `program_requirements`
  ADD CONSTRAINT `program_requirements_ibfk_1` FOREIGN KEY (`program_requirement_category_id`) REFERENCES `program_requirement_categories` (`program_requirement_category_id`),
  ADD CONSTRAINT `program_requirements_ibfk_2` FOREIGN KEY (`requirement_id`) REFERENCES `requirements` (`requirement_id`);

-- ============================================================
-- VIEWS
-- ============================================================

DROP VIEW IF EXISTS `vw_applicants`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_applicants` AS
  SELECT `a`.`applicant_id`, `u`.`user_id`, `a`.`program_id`, `p`.`program_name`,
         `u`.`firstname`, `u`.`lastname`, `u`.`email`, `u`.`mobile`, `u`.`role`,
         `a`.`applicant_number`, `a`.`password`, `a`.`temp_password`,
         `a`.`application_status`, `a`.`application_date`, `a`.`created_at`
  FROM ((`applicants` `a`
    JOIN `users` `u` ON(`a`.`user_id` = `u`.`user_id`))
    JOIN `programs` `p` ON(`a`.`program_id` = `p`.`program_id`));

DROP VIEW IF EXISTS `vw_program_categories`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_program_categories` AS
  SELECT `prc`.`program_requirement_category_id`, `prc`.`program_id`, `prc`.`category_id`,
         `rc`.`category_name`, `rc`.`description` AS `category_description`,
         `prc`.`display_order`, `prc`.`is_active`
  FROM (`program_requirement_categories` `prc`
    JOIN `requirement_categories` `rc` ON(`rc`.`category_id` = `prc`.`category_id`))
  ORDER BY `prc`.`program_id` ASC, `prc`.`display_order` ASC;

DROP VIEW IF EXISTS `vw_program_requirements`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_program_requirements` AS
  SELECT `pr`.`program_requirement_id`, `prc`.`program_id`, `prc`.`program_requirement_category_id`,
         `rc`.`category_name`, `prc`.`display_order` AS `category_order`,
         `r`.`requirement_id`, `r`.`requirement_name`, `r`.`description` AS `requirement_description`,
         `pr`.`display_order` AS `requirement_order`, `pr`.`is_active`, `pr`.`is_mandatory`
  FROM (((`program_requirements` `pr`
    JOIN `program_requirement_categories` `prc` ON(`prc`.`program_requirement_category_id` = `pr`.`program_requirement_category_id`))
    JOIN `requirement_categories` `rc` ON(`rc`.`category_id` = `prc`.`category_id`))
    JOIN `requirements` `r` ON(`r`.`requirement_id` = `pr`.`requirement_id`))
  ORDER BY `prc`.`program_id` ASC, `prc`.`display_order` ASC, `pr`.`display_order` ASC;

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

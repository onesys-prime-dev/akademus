-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 18, 2026 at 03:38 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `akademus_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_years`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `admin_credentials`
--

CREATE TABLE `admin_credentials` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_credentials`
--

INSERT INTO `admin_credentials` (`id`, `user_id`, `password`, `created_at`) VALUES
(0, 4, '$2y$10$nCL21uigGtSBjXrJOY03S.eZhbD9g.CLHq3HOyAqkbk4giNC8i/AC', '2026-03-13 04:52:57'),
(4, 66, '$2y$10$nCL21uigGtSBjXrJOY03S.eZhbD9g.CLHq3HOyAqkbk4giNC8i/AC', '2026-03-10 02:25:12');

-- --------------------------------------------------------

--
-- Table structure for table `applicants`
--

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

--
-- Dumping data for table `applicants`
--

INSERT INTO `applicants` (`applicant_id`, `user_id`, `program_id`, `applicant_number`, `password`, `temp_password`, `application_status`, `application_date`, `created_at`, `form_step`) VALUES
(4, 13, 1, 'APP-2026-00004', '$2y$10$XIh1FmOY4NDzpCyMyaA5bO2MgWjQpdAD90L/8aGOjb4zcWSJXurK6', 1, 'pending', '2026-03-17 16:11:56', '2026-03-17 16:11:56', 1),
(5, 14, 1, 'APP-2026-00005', '$2y$10$fhgOyQnLPQ2LG3lXhnrcLelwyx1gHzHAeYOzMyD5JntnmpNsh/shK', 1, 'pending', '2026-03-17 17:09:05', '2026-03-17 17:09:05', 1);

-- --------------------------------------------------------

--
-- Table structure for table `applicant_documents`
--

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

--
-- Dumping data for table `applicant_documents`
--

INSERT INTO `applicant_documents` (`document_id`, `applicant_id`, `program_requirement_id`, `document_name`, `status`, `verified_by`, `remarks`, `uploaded_at`) VALUES
(1, 4, 8, '4_8.jpg', 'pending', NULL, NULL, '2026-03-18 10:12:15'),
(2, 4, 9, '4_9.png', 'pending', NULL, NULL, '2026-03-18 10:28:43'),
(3, 4, 10, '4_10.png', 'pending', NULL, NULL, '2026-03-18 10:28:46');

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

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

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`application_id`, `applicant_id`, `applicant_type`, `program_id`, `school_year`, `semester`, `application_status`, `application_date`, `updated_at`) VALUES
(33, 5, NULL, 1, '', '', 'draft', '2026-03-17 17:13:48', '2026-03-17 17:13:48'),
(34, 4, 'freshmen', 2, '', '', 'draft', '2026-03-17 17:26:49', '2026-03-18 02:06:45');

-- --------------------------------------------------------

--
-- Table structure for table `class_schedules`
--

CREATE TABLE `class_schedules` (
  `schedule_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `section_name` varchar(20) DEFAULT NULL,
  `year_level` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_form_fields`
--

CREATE TABLE `course_form_fields` (
  `id` int(11) UNSIGNED NOT NULL,
  `course_id` int(11) UNSIGNED NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `field_type` enum('text','textarea','email','number','file','select','checkbox','radio') NOT NULL,
  `label` varchar(255) NOT NULL,
  `placeholder` varchar(255) DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `validation_rules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`validation_rules`)),
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options`)),
  `display_order` int(11) NOT NULL DEFAULT 0,
  `override_global` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'If true, hides global field with same name',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `educational_backgrounds`
--

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

--
-- Dumping data for table `educational_backgrounds`
--

INSERT INTO `educational_backgrounds` (`educational_bg_id`, `application_id`, `school_name`, `school_location`, `strand`, `school_year_start`, `school_year_end`, `gwa`, `school_type`, `created_at`, `updated_at`) VALUES
(28, 33, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-17 17:13:48', '2026-03-17 17:13:48'),
(29, 34, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-17 17:26:49', '2026-03-17 17:26:49');

-- --------------------------------------------------------

--
-- Table structure for table `family_backgrounds`
--

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

--
-- Dumping data for table `family_backgrounds`
--

INSERT INTO `family_backgrounds` (`id`, `application_id`, `father_firstname`, `father_middlename`, `father_lastname`, `father_occupation`, `father_contact_number`, `mother_firstname`, `mother_middlename`, `mother_lastname`, `mother_occupation`, `mother_contact_number`, `guardian_name`, `guardian_relationship`, `guardian_contact_number`, `annual_family_income`, `created_at`, `updated_at`) VALUES
(28, 33, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-17 17:13:48', '2026-03-17 17:13:48'),
(29, 34, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-17 17:26:49', '2026-03-17 17:26:49');

-- --------------------------------------------------------

--
-- Table structure for table `form_field_configs`
--

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

--
-- Dumping data for table `form_field_configs`
--

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
(36, 4, 'school_type', 4, 'step4-main', 'School Type', 'select', 'Select school type', 1, 1, 1, 70, '2026-03-13 05:25:52', '2026-03-12 21:46:25'),
(37, 7, 'first_name', 7, 'page7-general_information', 'First Name', 'text', '', 1, 0, 0, 999, '2026-03-17 08:34:34', '2026-03-17 08:34:48');

-- --------------------------------------------------------

--
-- Table structure for table `form_pages`
--

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

--
-- Dumping data for table `form_pages`
--

INSERT INTO `form_pages` (`id`, `title`, `description`, `is_enabled`, `is_system`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Application Type', 'Select applicant type, program, and school year.', 1, 1, 10, '2026-03-11 08:58:37', '2026-03-17 08:35:10'),
(2, 'Personal Information', 'Full name, date of birth, contact details, and address.', 1, 1, 20, '2026-03-11 08:58:37', '2026-03-17 08:35:09'),
(3, 'Family Background', 'Father, mother, guardian info, and family income.', 1, 1, 30, '2026-03-11 08:58:37', '2026-03-17 08:35:08'),
(4, 'Educational Background', 'Most recent senior high school information.', 1, 1, 40, '2026-03-11 08:58:37', '2026-03-17 08:35:07');

-- --------------------------------------------------------

--
-- Table structure for table `form_presets`
--

CREATE TABLE `form_presets` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_global` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = applies to all programs, 0 = program-specific',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `form_presets`
--

INSERT INTO `form_presets` (`id`, `name`, `description`, `is_global`, `is_active`, `created_at`, `updated_at`) VALUES
(2, 'Computer Science', '', 0, 1, '2026-03-17 17:24:10', '2026-03-18 02:06:00'),
(3, 'Information  technology', '', 0, 1, '2026-03-18 00:46:11', '2026-03-18 02:06:11');

-- --------------------------------------------------------

--
-- Table structure for table `global_form_fields`
--

CREATE TABLE `global_form_fields` (
  `id` int(11) UNSIGNED NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `field_type` enum('text','textarea','email','number','file','select','checkbox','radio') NOT NULL,
  `label` varchar(255) NOT NULL,
  `placeholder` varchar(255) DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `validation_rules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`validation_rules`)),
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'For select, checkbox, radio fields' CHECK (json_valid(`options`)),
  `display_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2026-03-17-000001', 'App\\Database\\Migrations\\CreateCoursesTable', 'default', 'App', 1773763117, 1),
(2, '2026-03-17-000002', 'App\\Database\\Migrations\\CreateGlobalFormFieldsTable', 'default', 'App', 1773763117, 1),
(3, '2026-03-17-000003', 'App\\Database\\Migrations\\CreateCourseFormFieldsTable', 'default', 'App', 1773763117, 1),
(4, '2026-03-18-000001', 'App\\Database\\Migrations\\CreateProgramFormFieldOverridesTable', 'default', 'App', 1773763196, 2),
(5, '2026-03-18-000002', 'App\\Database\\Migrations\\CreateFormPresetsSystem', 'default', 'App', 1773764519, 3),
(8, '2026-03-18-000003', 'App\\Database\\Migrations\\CreatePresetRequirementCategoriesSystem', 'default', 'App', 1773799351, 6),
(10, '2026-03-18-000004', 'App\\Database\\Migrations\\FixApplicantDocumentsTable', 'default', 'App', 1773800847, 7);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `amount` double DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` enum('pending','paid') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_informations`
--

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

--
-- Dumping data for table `personal_informations`
--

INSERT INTO `personal_informations` (`id`, `application_id`, `firstname`, `middlename`, `lastname`, `suffix`, `gender`, `date_of_birth`, `place_of_birth`, `citizenship`, `home_address`, `phone_code`, `contact_number`, `email_address`, `created_at`, `updated_at`) VALUES
(28, 33, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '+63', NULL, NULL, '2026-03-17 17:13:48', '2026-03-17 17:13:48'),
(29, 34, '', '', '', '', NULL, '0000-00-00', '', '', '', '+63', '', '', '2026-03-17 17:26:49', '2026-03-18 00:57:08');

-- --------------------------------------------------------

--
-- Table structure for table `preset_requirement_categories`
--

CREATE TABLE `preset_requirement_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `preset_id` int(10) UNSIGNED NOT NULL,
  `requirement_category_id` int(11) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `preset_requirement_categories`
--

INSERT INTO `preset_requirement_categories` (`id`, `preset_id`, `requirement_category_id`, `sort_order`) VALUES
(2, 2, 1, 0),
(3, 2, 2, 1),
(4, 3, 4, 0);

-- --------------------------------------------------------

--
-- Table structure for table `preset_sections`
--

CREATE TABLE `preset_sections` (
  `id` int(11) UNSIGNED NOT NULL,
  `preset_id` int(11) UNSIGNED NOT NULL,
  `form_page_id` int(11) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_required` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = mandatory section, 0 = optional'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `preset_sections`
--

INSERT INTO `preset_sections` (`id`, `preset_id`, `form_page_id`, `sort_order`, `is_required`) VALUES
(48, 2, 1, 0, 1),
(49, 2, 2, 1, 1),
(50, 2, 3, 2, 1),
(51, 2, 4, 3, 1),
(52, 3, 1, 0, 1),
(53, 3, 2, 1, 1),
(54, 3, 3, 2, 1),
(55, 3, 4, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `program_id` int(11) NOT NULL,
  `program_code` varchar(20) DEFAULT NULL,
  `program_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `preset_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'Foreign key to form_presets, null = use global preset'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`program_id`, `program_code`, `program_name`, `created_at`, `preset_id`) VALUES
(1, 'BSCS', 'Bachelor of Science in Computer Science', '2026-02-24 09:30:27', 2),
(2, 'BSIT', 'Bachelor of Science in Information Technology', '2026-02-24 09:30:27', 3),
(3, 'BSIS', 'Bachelor of Science in Information Systems', '2026-02-24 09:30:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `program_form_field_overrides`
--

CREATE TABLE `program_form_field_overrides` (
  `id` int(11) UNSIGNED NOT NULL,
  `program_id` int(11) UNSIGNED NOT NULL,
  `field_config_id` int(11) UNSIGNED NOT NULL,
  `is_required` tinyint(1) DEFAULT NULL COMMENT 'NULL = use default, true/false = override',
  `is_visible` tinyint(1) DEFAULT NULL COMMENT 'NULL = use default, true/false = override',
  `sort_order` int(11) DEFAULT NULL COMMENT 'NULL = use default',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `program_requirements`
--

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

--
-- Dumping data for table `program_requirements`
--

INSERT INTO `program_requirements` (`program_requirement_id`, `program_requirement_category_id`, `requirement_id`, `display_order`, `is_active`, `is_mandatory`, `created_at`, `updated_at`) VALUES
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

-- --------------------------------------------------------

--
-- Table structure for table `program_requirement_categories`
--

CREATE TABLE `program_requirement_categories` (
  `program_requirement_category_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `program_requirement_categories`
--

INSERT INTO `program_requirement_categories` (`program_requirement_category_id`, `program_id`, `category_id`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, NULL, NULL),
(2, 1, 2, 2, 1, NULL, NULL),
(3, 1, 3, 3, 1, NULL, NULL),
(4, 1, 4, 4, 1, NULL, NULL),
(5, 2, 4, 4, 1, NULL, NULL),
(6, 2, 2, 2, 1, NULL, NULL),
(7, 2, 1, 1, 1, NULL, NULL),
(8, 2, 3, 3, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `requirements`
--

CREATE TABLE `requirements` (
  `requirement_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `requirement_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `is_required` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requirements`
--

INSERT INTO `requirements` (`requirement_id`, `category_id`, `requirement_name`, `description`, `is_required`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Birth Certificate', 'PSA-issued birth certificate', 1, 1, 1, NULL, NULL),
(2, 1, 'Certificate of Good Moral Character', 'Issued by previous school', 1, 2, 1, NULL, NULL),
(3, 2, 'Transcript of Records', 'Official academic transcript', 1, 1, 1, NULL, NULL),
(4, 2, 'Diploma', 'High school or previous degree diploma', 1, 2, 1, NULL, NULL),
(5, 2, 'Form 137', 'Secondary school permanent record', 1, 3, 1, NULL, NULL),
(6, 3, 'Valid Government ID', 'Any government-issued ID (e.g., Passport, UMID)', 1, 1, 1, NULL, NULL),
(7, 3, '2x2 ID Picture', 'Recent 2x2 photo with white background', 1, 2, 1, NULL, NULL),
(8, 4, 'Medical Certificate', 'General health clearance from a licensed physician', 1, 1, 1, NULL, NULL),
(9, 4, 'Chest X-ray Result', 'Recent X-ray result for medical screening', 1, 2, 1, NULL, NULL),
(10, 4, 'Drug Test Result', 'Laboratory-issued drug test result', 1, 3, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `requirement_categories`
--

CREATE TABLE `requirement_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requirement_categories`
--

INSERT INTO `requirement_categories` (`category_id`, `category_name`, `description`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Personal Documents', 'Basic personal identification documents', 1, 1, NULL, NULL),
(2, 'Academic Documents', 'Educational background and records', 2, 1, NULL, NULL),
(3, 'Identification', 'Government-issued IDs for verification', 3, 1, NULL, NULL),
(4, 'Medical Requirements', 'Health-related documents and clearances', 4, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `theme_settings`
--

CREATE TABLE `theme_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` longtext DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `theme_settings`
--

INSERT INTO `theme_settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'primary_color', '#0a6640', '2026-03-18 00:08:07', '2026-03-18 08:56:53'),
(2, 'primary_dark', '#064d30', '2026-03-18 00:08:07', '2026-03-18 08:56:53'),
(3, 'primary_light', '#0e7a4c', '2026-03-18 00:08:07', '2026-03-18 08:56:53'),
(4, 'accent_color', '#f5c518', '2026-03-18 00:08:07', '2026-03-18 08:56:53'),
(5, 'accent_text', '#064d30', '2026-03-18 00:08:07', '2026-03-18 08:56:53'),
(6, 'sidebar_color', '#064d30', '2026-03-18 00:08:07', '2026-03-18 08:56:53'),
(7, 'font_family', 'Poppins', '2026-03-18 00:08:07', '2026-03-18 00:31:52'),
(8, 'border_radius', '22px', '2026-03-18 00:08:07', '2026-03-18 10:35:50'),
(9, 'app_name', 'Akademus Portal', '2026-03-18 00:08:07', '2026-03-18 00:08:07'),
(10, 'logo_text', 'AK', '2026-03-18 00:08:07', '2026-03-18 00:08:07'),
(11, 'logo_url', '', '2026-03-18 00:08:07', '2026-03-18 00:08:07'),
(12, 'school_year', 'SY 2025-2026', '2026-03-18 00:08:07', '2026-03-18 00:08:07'),
(13, 'admission_status', 'Open for Applications', '2026-03-18 00:08:07', '2026-03-18 00:08:07'),
(14, 'preset', 'forest', '2026-03-18 00:08:07', '2026-03-18 08:56:53');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `role` varchar(50) DEFAULT 'applicant',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `firstname`, `lastname`, `email`, `mobile`, `role`, `created_at`) VALUES
(4, 'Akademus', 'Admin', 'admin@akademus.edu.ph', '09000000000', 'admin', '2026-03-13 04:52:57'),
(11, 'Josh', 'Baquiano', 'joshbaquiano@gmail.com', '09123456789', 'applicant', '2026-03-17 07:45:01'),
(13, 'THEODORE', 'ABELLA', 'testakademusmentora@gmail.com', '09762402857', 'applicant', '2026-03-17 16:11:56'),
(14, 'THEODORE', 'ABELLA', 'abellatheodore@gmail.com', '09762402857', 'applicant', '2026-03-17 17:09:05');

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_applicants`
-- (See below for the actual view)
--
CREATE TABLE `vw_applicants` (
`applicant_id` int(11)
,`user_id` int(11)
,`program_id` int(11)
,`program_name` varchar(100)
,`firstname` varchar(100)
,`lastname` varchar(100)
,`email` varchar(150)
,`mobile` varchar(20)
,`role` varchar(50)
,`applicant_number` varchar(30)
,`password` varchar(255)
,`temp_password` tinyint(1)
,`application_status` enum('pending','approved','rejected','enrolled')
,`application_date` datetime
,`created_at` timestamp
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_program_categories`
-- (See below for the actual view)
--
CREATE TABLE `vw_program_categories` (
`program_requirement_category_id` int(11)
,`program_id` int(11)
,`category_id` int(11)
,`category_name` varchar(150)
,`category_description` text
,`display_order` int(11)
,`is_active` tinyint(1)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_program_requirements`
-- (See below for the actual view)
--
CREATE TABLE `vw_program_requirements` (
`program_requirement_id` int(11)
,`program_id` int(11)
,`program_requirement_category_id` int(11)
,`category_name` varchar(150)
,`category_order` int(11)
,`requirement_id` int(11)
,`requirement_name` varchar(150)
,`requirement_description` text
,`requirement_order` int(11)
,`is_active` tinyint(1)
,`is_mandatory` tinyint(1)
);

-- --------------------------------------------------------

--
-- Structure for view `vw_applicants`
--
DROP TABLE IF EXISTS `vw_applicants`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_applicants`  AS SELECT `a`.`applicant_id` AS `applicant_id`, `u`.`user_id` AS `user_id`, `a`.`program_id` AS `program_id`, `p`.`program_name` AS `program_name`, `u`.`firstname` AS `firstname`, `u`.`lastname` AS `lastname`, `u`.`email` AS `email`, `u`.`mobile` AS `mobile`, `u`.`role` AS `role`, `a`.`applicant_number` AS `applicant_number`, `a`.`password` AS `password`, `a`.`temp_password` AS `temp_password`, `a`.`application_status` AS `application_status`, `a`.`application_date` AS `application_date`, `a`.`created_at` AS `created_at` FROM ((`applicants` `a` join `users` `u` on(`a`.`user_id` = `u`.`user_id`)) join `programs` `p` on(`a`.`program_id` = `p`.`program_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `vw_program_categories`
--
DROP TABLE IF EXISTS `vw_program_categories`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_program_categories`  AS SELECT `prc`.`program_requirement_category_id` AS `program_requirement_category_id`, `prc`.`program_id` AS `program_id`, `prc`.`category_id` AS `category_id`, `rc`.`category_name` AS `category_name`, `rc`.`description` AS `category_description`, `prc`.`display_order` AS `display_order`, `prc`.`is_active` AS `is_active` FROM (`program_requirement_categories` `prc` join `requirement_categories` `rc` on(`rc`.`category_id` = `prc`.`category_id`)) ORDER BY `prc`.`program_id` ASC, `prc`.`display_order` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `vw_program_requirements`
--
DROP TABLE IF EXISTS `vw_program_requirements`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_program_requirements`  AS SELECT `pr`.`program_requirement_id` AS `program_requirement_id`, `prc`.`program_id` AS `program_id`, `prc`.`program_requirement_category_id` AS `program_requirement_category_id`, `rc`.`category_name` AS `category_name`, `prc`.`display_order` AS `category_order`, `r`.`requirement_id` AS `requirement_id`, `r`.`requirement_name` AS `requirement_name`, `r`.`description` AS `requirement_description`, `pr`.`display_order` AS `requirement_order`, `pr`.`is_active` AS `is_active`, `pr`.`is_mandatory` AS `is_mandatory` FROM (((`program_requirements` `pr` join `program_requirement_categories` `prc` on(`prc`.`program_requirement_category_id` = `pr`.`program_requirement_category_id`)) join `requirement_categories` `rc` on(`rc`.`category_id` = `prc`.`category_id`)) join `requirements` `r` on(`r`.`requirement_id` = `pr`.`requirement_id`)) ORDER BY `prc`.`program_id` ASC, `prc`.`display_order` ASC, `pr`.`display_order` ASC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_years`
--
ALTER TABLE `academic_years`
  ADD PRIMARY KEY (`year_id`);

--
-- Indexes for table `admin_credentials`
--
ALTER TABLE `admin_credentials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_admin_user` (`user_id`);

--
-- Indexes for table `applicants`
--
ALTER TABLE `applicants`
  ADD PRIMARY KEY (`applicant_id`),
  ADD UNIQUE KEY `applicant_number` (`applicant_number`),
  ADD KEY `fk_applicant_user` (`user_id`);

--
-- Indexes for table `applicant_documents`
--
ALTER TABLE `applicant_documents`
  ADD PRIMARY KEY (`document_id`),
  ADD UNIQUE KEY `uq_applicant_requirement` (`applicant_id`,`program_requirement_id`),
  ADD KEY `applicant_id` (`applicant_id`),
  ADD KEY `program_requirement_id` (`program_requirement_id`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `fk_application_applicant` (`applicant_id`),
  ADD KEY `fk_applications_program` (`program_id`);

--
-- Indexes for table `class_schedules`
--
ALTER TABLE `class_schedules`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `fk_schedule_program` (`program_id`),
  ADD KEY `fk_schedule_academic_year` (`academic_year_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `course_form_fields`
--
ALTER TABLE `course_form_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_form_fields_course_id_foreign` (`course_id`);

--
-- Indexes for table `educational_backgrounds`
--
ALTER TABLE `educational_backgrounds`
  ADD PRIMARY KEY (`educational_bg_id`),
  ADD KEY `fk_education_bg_application` (`application_id`);

--
-- Indexes for table `family_backgrounds`
--
ALTER TABLE `family_backgrounds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_fam_bg_application` (`application_id`);

--
-- Indexes for table `form_field_configs`
--
ALTER TABLE `form_field_configs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `form_pages`
--
ALTER TABLE `form_pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `form_presets`
--
ALTER TABLE `form_presets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_global` (`is_global`),
  ADD KEY `is_active` (`is_active`);

--
-- Indexes for table `global_form_fields`
--
ALTER TABLE `global_form_fields`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `personal_informations`
--
ALTER TABLE `personal_informations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_personal_info_application` (`application_id`);

--
-- Indexes for table `preset_requirement_categories`
--
ALTER TABLE `preset_requirement_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `preset_id` (`preset_id`),
  ADD KEY `requirement_category_id` (`requirement_category_id`);

--
-- Indexes for table `preset_sections`
--
ALTER TABLE `preset_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `preset_id` (`preset_id`),
  ADD KEY `form_page_id` (`form_page_id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`program_id`);

--
-- Indexes for table `program_form_field_overrides`
--
ALTER TABLE `program_form_field_overrides`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `program_id_field_config_id` (`program_id`,`field_config_id`);

--
-- Indexes for table `program_requirements`
--
ALTER TABLE `program_requirements`
  ADD PRIMARY KEY (`program_requirement_id`),
  ADD UNIQUE KEY `program_requirement_category_id` (`program_requirement_category_id`,`requirement_id`),
  ADD KEY `requirement_id` (`requirement_id`);

--
-- Indexes for table `program_requirement_categories`
--
ALTER TABLE `program_requirement_categories`
  ADD PRIMARY KEY (`program_requirement_category_id`),
  ADD UNIQUE KEY `program_id` (`program_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `requirements`
--
ALTER TABLE `requirements`
  ADD PRIMARY KEY (`requirement_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `requirement_categories`
--
ALTER TABLE `requirement_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `theme_settings`
--
ALTER TABLE `theme_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_years`
--
ALTER TABLE `academic_years`
  MODIFY `year_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `applicants`
--
ALTER TABLE `applicants`
  MODIFY `applicant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `applicant_documents`
--
ALTER TABLE `applicant_documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `class_schedules`
--
ALTER TABLE `class_schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_form_fields`
--
ALTER TABLE `course_form_fields`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `educational_backgrounds`
--
ALTER TABLE `educational_backgrounds`
  MODIFY `educational_bg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `family_backgrounds`
--
ALTER TABLE `family_backgrounds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `form_field_configs`
--
ALTER TABLE `form_field_configs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `form_pages`
--
ALTER TABLE `form_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `form_presets`
--
ALTER TABLE `form_presets`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `global_form_fields`
--
ALTER TABLE `global_form_fields`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `personal_informations`
--
ALTER TABLE `personal_informations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `preset_requirement_categories`
--
ALTER TABLE `preset_requirement_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `preset_sections`
--
ALTER TABLE `preset_sections`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `program_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `program_form_field_overrides`
--
ALTER TABLE `program_form_field_overrides`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `program_requirements`
--
ALTER TABLE `program_requirements`
  MODIFY `program_requirement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `program_requirement_categories`
--
ALTER TABLE `program_requirement_categories`
  MODIFY `program_requirement_category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `requirements`
--
ALTER TABLE `requirements`
  MODIFY `requirement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `requirement_categories`
--
ALTER TABLE `requirement_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `theme_settings`
--
ALTER TABLE `theme_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applicant_documents`
--
ALTER TABLE `applicant_documents`
  ADD CONSTRAINT `fk_applicant_doc_requirement` FOREIGN KEY (`program_requirement_id`) REFERENCES `requirements` (`requirement_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `fk_application_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_applications_program` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `class_schedules`
--
ALTER TABLE `class_schedules`
  ADD CONSTRAINT `fk_schedule_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`year_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_schedule_program` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`) ON DELETE CASCADE;

--
-- Constraints for table `course_form_fields`
--
ALTER TABLE `course_form_fields`
  ADD CONSTRAINT `course_form_fields_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `educational_backgrounds`
--
ALTER TABLE `educational_backgrounds`
  ADD CONSTRAINT `fk_education_bg_application` FOREIGN KEY (`application_id`) REFERENCES `applications` (`application_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `family_backgrounds`
--
ALTER TABLE `family_backgrounds`
  ADD CONSTRAINT `fk_fam_bg_application` FOREIGN KEY (`application_id`) REFERENCES `applications` (`application_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `personal_informations`
--
ALTER TABLE `personal_informations`
  ADD CONSTRAINT `fk_personal_info_application` FOREIGN KEY (`application_id`) REFERENCES `applications` (`application_id`) ON DELETE CASCADE;

--
-- Constraints for table `preset_requirement_categories`
--
ALTER TABLE `preset_requirement_categories`
  ADD CONSTRAINT `preset_requirement_categories_preset_id_foreign` FOREIGN KEY (`preset_id`) REFERENCES `form_presets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `preset_sections`
--
ALTER TABLE `preset_sections`
  ADD CONSTRAINT `preset_sections_preset_id_foreign` FOREIGN KEY (`preset_id`) REFERENCES `form_presets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `program_requirements`
--
ALTER TABLE `program_requirements`
  ADD CONSTRAINT `program_requirements_ibfk_1` FOREIGN KEY (`program_requirement_category_id`) REFERENCES `program_requirement_categories` (`program_requirement_category_id`),
  ADD CONSTRAINT `program_requirements_ibfk_2` FOREIGN KEY (`requirement_id`) REFERENCES `requirements` (`requirement_id`);

--
-- Constraints for table `program_requirement_categories`
--
ALTER TABLE `program_requirement_categories`
  ADD CONSTRAINT `program_requirement_categories_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`),
  ADD CONSTRAINT `program_requirement_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `requirement_categories` (`category_id`);

--
-- Constraints for table `requirements`
--
ALTER TABLE `requirements`
  ADD CONSTRAINT `requirements_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `requirement_categories` (`category_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

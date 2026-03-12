-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 12, 2026 at 09:41 AM
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
(36, 59, 1, 'APP-2026-00036', '$2y$10$Y3pT6XXkueAkvFZNFgMr4uHUNj6F1r0Gt5m1j47ibzG0w0/oDrEey', 1, 'pending', '2026-03-05 10:31:40', '2026-03-05 10:31:40', 1),
(37, 60, 3, 'APP-2026-00037', '$2y$10$yLEm/f.tiFuJLBBCCS78T.9Lf6rTPNfbM9Kvpb7G5nrTpfjG0i9Ou', 1, 'pending', '2026-03-06 05:54:26', '2026-03-06 05:54:26', 1),
(41, 78, 2, 'APP-2026-00041', '$2y$10$LVpT5Jploi9MF5sXXw042.SJ7YYcVLUonBzTiFLSGFIEgmCGIxC1G', 1, 'pending', '2026-03-12 08:14:19', '2026-03-12 08:14:19', 1);

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

-- --------------------------------------------------------

--
-- Table structure for table `form_field_configs`
--

CREATE TABLE `form_field_configs` (
  `id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL DEFAULT 1,
  `field_key` varchar(100) NOT NULL COMMENT 'Matches DB column name e.g. firstname, father_occupation',
  `step` tinyint(4) NOT NULL COMMENT '1=App Type, 2=Personal, 3=Family, 4=Education',
  `section` varchar(60) NOT NULL COMMENT 'e.g. step1-core, step2-name, step3-father',
  `label` varchar(200) NOT NULL,
  `field_type` varchar(30) NOT NULL DEFAULT 'text' COMMENT 'text, select, radio, date, tel, email, number, textarea',
  `placeholder` varchar(255) DEFAULT NULL,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `is_system` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=cannot delete, 0=custom field',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `form_field_configs`
--

INSERT INTO `form_field_configs` (`id`, `page_id`, `field_key`, `step`, `section`, `label`, `field_type`, `placeholder`, `is_visible`, `is_required`, `is_system`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'applicant_type', 1, 'step1-core', 'Applicant Type', 'radio', 'freshmen, transferee, ladderized, test', 1, 1, 1, 10, '2026-03-11 08:42:13', '2026-03-11 01:23:29'),
(2, 1, 'program_id', 1, 'step1-core', 'Confirmed Program', 'select', 'Fetched from programs table', 1, 1, 1, 20, '2026-03-11 08:42:13', '2026-03-11 01:23:29'),
(3, 1, 'school_year', 1, 'step1-core', 'School Year', 'select', 'e.g. 2024-2025', 1, 1, 1, 30, '2026-03-11 08:42:13', '2026-03-11 01:23:29'),
(4, 1, 'semester', 1, 'step1-core', 'Semester', 'select', 'e.g. 1st Semester', 1, 1, 1, 40, '2026-03-11 08:42:13', '2026-03-11 01:23:29'),
(5, 2, 'firstname', 2, 'step2-name', 'First Name', 'text', 'e.g. Juan', 1, 1, 1, 10, '2026-03-11 08:42:13', '2026-03-11 17:17:32'),
(6, 2, 'middlename', 2, 'step2-name', 'Middle Name', 'text', 'e.g. Santos', 1, 0, 1, 20, '2026-03-11 08:42:13', '2026-03-11 17:17:32'),
(7, 2, 'lastname', 2, 'step2-name', 'Last Name', 'text', 'e.g. Dela Cruz', 1, 1, 1, 30, '2026-03-11 08:42:13', '2026-03-11 17:17:32'),
(8, 2, 'suffix', 2, 'step2-name', 'Suffix', 'text', 'e.g. Jr., III', 1, 0, 1, 40, '2026-03-11 08:42:13', '2026-03-11 17:17:32'),
(9, 2, 'gender', 2, 'step2-details', 'Gender', 'select', 'Male, Female, Prefer not to say', 1, 1, 1, 10, '2026-03-11 08:42:13', '2026-03-11 17:17:32'),
(10, 2, 'date_of_birth', 2, 'step2-details', 'Date of Birth', 'date', 'YYYY-MM-DD', 1, 1, 1, 20, '2026-03-11 08:42:13', '2026-03-11 17:17:32'),
(11, 2, 'place_of_birth', 2, 'step2-details', 'Place of Birth', 'text', 'e.g. Manila', 1, 1, 1, 30, '2026-03-11 08:42:13', '2026-03-11 17:17:32'),
(12, 2, 'citizenship', 2, 'step2-details', 'Citizenship', 'text', 'e.g. Filipino', 1, 1, 1, 40, '2026-03-11 08:42:13', '2026-03-11 17:17:32'),
(13, 2, 'home_address', 2, 'step2-details', 'Home Address', 'text', 'Full address', 1, 1, 1, 50, '2026-03-11 08:42:13', '2026-03-11 17:17:32'),
(14, 2, 'contact_number', 2, 'step2-details', 'Contact Number', 'tel', '09XXXXXXXXX', 1, 1, 1, 60, '2026-03-11 08:42:13', '2026-03-11 17:17:32'),
(15, 2, 'email_address', 2, 'step2-details', 'Email Address', 'email', 'you@email.com', 1, 1, 1, 70, '2026-03-11 08:42:13', '2026-03-11 17:17:32'),
(16, 3, 'father_firstname', 3, 'step3-father', 'Father\'s First Name', 'text', '', 1, 0, 1, 10, '2026-03-11 08:42:13', '2026-03-11 01:25:02'),
(17, 3, 'father_middlename', 3, 'step3-father', 'Father\'s Middle Name', 'text', '', 1, 0, 1, 20, '2026-03-11 08:42:13', '2026-03-11 01:25:02'),
(18, 3, 'father_lastname', 3, 'step3-father', 'test', 'text', '', 1, 0, 1, 30, '2026-03-11 08:42:13', '2026-03-11 01:25:02'),
(19, 3, 'father_occupation', 3, 'step3-father', 'Father\'s Occupation', 'text', '', 1, 0, 1, 40, '2026-03-11 08:42:13', '2026-03-11 01:25:02'),
(20, 3, 'father_contact_number', 3, 'step3-father', 'Father\'s Contact', 'tel', '09XXXXXXXXX', 1, 0, 1, 50, '2026-03-11 08:42:13', '2026-03-11 01:25:02'),
(21, 3, 'mother_firstname', 3, 'step3-mother', 'Mother\'s First Name', 'text', '', 1, 0, 1, 10, '2026-03-11 08:42:13', '2026-03-11 01:25:02'),
(22, 3, 'mother_middlename', 3, 'step3-mother', 'Mother\'s Middle Name', 'text', '', 1, 0, 1, 20, '2026-03-11 08:42:13', '2026-03-11 01:25:02'),
(23, 3, 'mother_lastname', 3, 'step3-mother', 'Mother\'s Last Name', 'text', '', 1, 0, 1, 30, '2026-03-11 08:42:13', '2026-03-11 01:25:02'),
(24, 3, 'mother_occupation', 3, 'step3-mother', 'Mother\'s Occupation', 'text', '', 1, 0, 1, 40, '2026-03-11 08:42:13', '2026-03-11 01:25:02'),
(25, 3, 'mother_contact_number', 3, 'step3-mother', 'Mother\'s Contact', 'tel', '09XXXXXXXXX', 1, 0, 1, 50, '2026-03-11 08:42:13', '2026-03-11 01:25:02'),
(26, 3, 'guardian_name', 3, 'step3-guardian', 'Guardian Name', 'text', '', 1, 0, 1, 10, '2026-03-11 08:42:13', '2026-03-11 01:25:02'),
(27, 3, 'guardian_relationship', 3, 'step3-guardian', 'Relationship', 'text', 'e.g. Uncle', 1, 0, 1, 20, '2026-03-11 08:42:13', '2026-03-11 01:25:02'),
(28, 3, 'guardian_contact_number', 3, 'step3-guardian', 'Guardian Contact', 'tel', '09XXXXXXXXX', 1, 0, 1, 30, '2026-03-11 08:42:13', '2026-03-11 01:25:02'),
(29, 3, 'annual_family_income', 3, 'step3-guardian', 'Annual Family Income', 'number', '', 1, 0, 1, 40, '2026-03-11 08:42:13', '2026-03-11 01:25:02'),
(30, 4, 'school_name', 4, 'step4-main', 'School Name', 'text', 'Name of institution', 1, 1, 1, 10, '2026-03-11 08:42:13', '2026-03-11 17:18:40'),
(31, 4, 'school_location', 4, 'step4-main', 'School Location', 'text', 'City / Province', 1, 1, 1, 20, '2026-03-11 08:42:13', '2026-03-11 17:18:40'),
(32, 4, 'strand', 4, 'step4-main', 'Strand / Track', 'text', 'e.g. STEM, ABM', 1, 0, 1, 30, '2026-03-11 08:42:13', '2026-03-11 17:18:40'),
(33, 4, 'school_year_start', 4, 'step4-main', 'School Year Start', 'number', 'e.g. 2024', 1, 1, 1, 40, '2026-03-11 08:42:13', '2026-03-11 17:18:40'),
(34, 4, 'school_year_end', 4, 'step4-main', 'School Year End', 'number', 'e.g. 2025', 1, 1, 1, 50, '2026-03-11 08:42:13', '2026-03-11 17:18:40'),
(35, 4, 'gwa', 4, 'step4-main', 'General Weighted Average', 'number', 'e.g. 90', 1, 0, 1, 60, '2026-03-11 08:42:13', '2026-03-11 17:18:40'),
(36, 4, 'school_type', 4, 'step4-main', 'School Type', 'select', 'Public, Private, International', 1, 1, 1, 70, '2026-03-11 08:42:13', '2026-03-11 17:18:40');

-- --------------------------------------------------------

--
-- Table structure for table `form_pages`
--

CREATE TABLE `form_pages` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `is_system` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = cannot be deleted',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `form_pages`
--

INSERT INTO `form_pages` (`id`, `title`, `description`, `is_enabled`, `is_system`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Application Type', 'Select applicant type, program, and school year.', 1, 1, 20, '2026-03-11 08:58:37', '2026-03-11 17:17:22'),
(2, 'Personal Information', 'Full name, date of birth, contact details, and address.', 1, 1, 30, '2026-03-11 08:58:37', '2026-03-11 17:17:25'),
(3, 'Family Background', 'Father, mother, guardian info, and family income.', 1, 1, 40, '2026-03-11 08:58:37', '2026-03-11 17:17:26'),
(4, 'Educational Background', 'Most recent senior high school information.', 1, 1, 50, '2026-03-11 08:58:37', '2026-03-12 07:26:35');

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
  `contact_number` varchar(50) DEFAULT NULL,
  `email_address` varchar(200) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `program_id` int(11) NOT NULL,
  `program_code` varchar(20) DEFAULT NULL,
  `program_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`program_id`, `program_code`, `program_name`, `created_at`) VALUES
(1, 'BSCS', 'Bachelor of Science in Computer Science', '2026-02-24 09:30:27'),
(2, 'BSIT', 'Bachelor of Science in Information Technology', '2026-02-24 09:30:27'),
(3, 'BSIS', 'Bachelor of Science in Information Systems', '2026-02-24 09:30:27');

-- --------------------------------------------------------

--
-- Table structure for table `program_requirements`
--

CREATE TABLE `program_requirements` (
  `program_requirement_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `requirement_id` int(11) NOT NULL,
  `is_mandatory` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `program_requirements`
--

INSERT INTO `program_requirements` (`program_requirement_id`, `program_id`, `requirement_id`, `is_mandatory`) VALUES
(1, 1, 1, 1),
(2, 1, 2, 0),
(3, 1, 3, 1),
(4, 1, 4, 1),
(5, 1, 5, 1),
(6, 1, 6, 0),
(7, 1, 7, 1),
(8, 1, 8, 1),
(9, 1, 9, 1),
(10, 1, 10, 1),
(11, 2, 1, 1),
(12, 2, 2, 0),
(13, 2, 3, 1),
(14, 2, 4, 1),
(15, 2, 5, 1),
(16, 2, 6, 0),
(17, 2, 7, 1),
(18, 2, 8, 1),
(19, 2, 9, 1),
(20, 2, 10, 1),
(21, 3, 1, 1),
(22, 3, 2, 0),
(23, 3, 3, 1),
(24, 3, 4, 1),
(25, 3, 5, 1),
(26, 3, 6, 0),
(27, 3, 7, 1),
(28, 3, 8, 1),
(29, 3, 9, 1),
(30, 3, 10, 1);

-- --------------------------------------------------------

--
-- Table structure for table `requirements`
--

CREATE TABLE `requirements` (
  `requirement_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `requirement_name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `display_order` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requirements`
--

INSERT INTO `requirements` (`requirement_id`, `category_id`, `requirement_name`, `description`, `display_order`, `is_active`, `created_at`) VALUES
(1, 1, 'Birth Certificate', 'PSA issued birth certificate', 1, 1, '2026-03-10 14:25:54'),
(2, 1, 'Baptismal Certificate', 'Church baptismal certificate', 2, 1, '2026-03-10 14:25:54'),
(3, 2, 'Form 138', 'Latest report card from previous school', 1, 1, '2026-03-10 14:25:54'),
(4, 2, 'Honorable Dismissal', 'Transfer clearance from previous school', 2, 1, '2026-03-10 14:25:54'),
(5, 2, 'Good Moral Certificate', 'Certificate of good moral character', 3, 1, '2026-03-10 14:25:54'),
(6, 2, 'School Transcript', 'Official transcript of records', 4, 1, '2026-03-10 14:25:54'),
(7, 3, 'ID Photo', '2x2 ID picture with white background', 1, 1, '2026-03-10 14:25:54'),
(8, 3, 'Valid ID', 'Government issued identification card', 2, 1, '2026-03-10 14:25:54'),
(9, 4, 'Medical Certificate', 'Medical clearance from licensed physician', 1, 1, '2026-03-10 14:25:54'),
(10, 4, 'X-Ray Result', 'Chest X-ray result from accredited clinic', 2, 1, '2026-03-10 14:25:54');

-- --------------------------------------------------------

--
-- Table structure for table `requirement_categories`
--

CREATE TABLE `requirement_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requirement_categories`
--

INSERT INTO `requirement_categories` (`category_id`, `category_name`, `description`, `display_order`, `is_active`, `created_at`) VALUES
(1, 'Personal', 'Personal identity documents', 1, 1, '2026-03-10 14:25:20'),
(2, 'Academic', 'School related documents', 2, 1, '2026-03-10 14:25:20'),
(3, 'Identification', 'Identification and photo requirements', 3, 1, '2026-03-10 14:25:20'),
(4, 'Medical', 'Health related documents', 4, 1, '2026-03-10 14:25:20');

-- --------------------------------------------------------

--
-- Table structure for table `theme_settings`
--

CREATE TABLE `theme_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` mediumtext DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `theme_settings`
--

INSERT INTO `theme_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'primary_color', '#0a6640', '2026-03-11 03:59:09'),
(2, 'primary_dark', '#064d30', '2026-03-11 03:59:09'),
(3, 'primary_light', '#0e7a4c', '2026-03-11 03:59:09'),
(4, 'accent_color', '#f5c518', '2026-03-11 03:59:09'),
(5, 'accent_text', '#064d30', '2026-03-11 03:59:09'),
(6, 'sidebar_color', '#064d30', '2026-03-11 03:59:09'),
(7, 'font_family', 'Plus Jakarta Sans', '2026-03-11 01:23:08'),
(8, 'border_radius', '4px', '2026-03-11 03:35:00'),
(9, 'app_name', 'San Beda University Manila', '2026-03-11 02:48:30'),
(10, 'logo_text', 'AK', '2026-03-10 02:09:22');
INSERT INTO `theme_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(11, 'logo_url', 'data:image/png;base64,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', '2026-03-11 01:22:25');
INSERT INTO `theme_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(12, 'school_year', 'SY 2025-2026', '2026-03-11 00:31:53'),
(13, 'admission_status', 'Open for Applications', '2026-03-10 02:09:22'),
(14, 'preset', 'forest', '2026-03-11 03:59:09');

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
(59, 'Onesys', 'Ecovis', 'testakademusmentora@gmail.com', '09123456789', 'applicant', '2026-03-05 10:31:40'),
(60, 'Josh', 'Baquiano', 'joshbaquiano@gmail.com', '09987654321', 'applicant', '2026-03-06 05:54:26'),
(78, 'Paul', 'Mapula', 'mapula.pauljoshua@gmail.com', '09958541242', 'applicant', '2026-03-12 08:14:19');

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_applicants`
-- (See below for the actual view)
--
CREATE TABLE `vw_applicants` (
`applicant_id` int(11)
,`user_id` int(11)
,`firstname` varchar(100)
,`lastname` varchar(100)
,`email` varchar(150)
,`mobile` varchar(20)
,`role` varchar(50)
,`program_id` int(11)
,`program_code` varchar(20)
,`program_name` varchar(100)
,`applicant_number` varchar(30)
,`password` varchar(255)
,`temp_password` tinyint(1)
,`application_status` enum('pending','approved','rejected','enrolled')
,`application_date` datetime
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_program_requirements`
-- (See below for the actual view)
--
CREATE TABLE `vw_program_requirements` (
`program_requirement_id` int(11)
,`program_id` int(11)
,`program_name` varchar(100)
,`category_id` int(11)
,`category_name` varchar(100)
,`category_description` varchar(255)
,`requirement_id` int(11)
,`requirement_name` varchar(255)
,`requirement_description` varchar(255)
,`display_order` int(11)
,`is_mandatory` tinyint(1)
);

-- --------------------------------------------------------

--
-- Structure for view `vw_applicants`
--
DROP TABLE IF EXISTS `vw_applicants`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_applicants`  AS SELECT `a`.`applicant_id` AS `applicant_id`, `u`.`user_id` AS `user_id`, `u`.`firstname` AS `firstname`, `u`.`lastname` AS `lastname`, `u`.`email` AS `email`, `u`.`mobile` AS `mobile`, `u`.`role` AS `role`, `p`.`program_id` AS `program_id`, `p`.`program_code` AS `program_code`, `p`.`program_name` AS `program_name`, `a`.`applicant_number` AS `applicant_number`, `a`.`password` AS `password`, `a`.`temp_password` AS `temp_password`, `a`.`application_status` AS `application_status`, `a`.`application_date` AS `application_date` FROM ((`applicants` `a` join `users` `u` on(`u`.`user_id` = `a`.`user_id`)) join `programs` `p` on(`a`.`program_id` = `p`.`program_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `vw_program_requirements`
--
DROP TABLE IF EXISTS `vw_program_requirements`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_program_requirements`  AS SELECT `pr`.`program_requirement_id` AS `program_requirement_id`, `p`.`program_id` AS `program_id`, `p`.`program_name` AS `program_name`, `rc`.`category_id` AS `category_id`, `rc`.`category_name` AS `category_name`, `rc`.`description` AS `category_description`, `r`.`requirement_id` AS `requirement_id`, `r`.`requirement_name` AS `requirement_name`, `r`.`description` AS `requirement_description`, `r`.`display_order` AS `display_order`, `pr`.`is_mandatory` AS `is_mandatory` FROM (((`program_requirements` `pr` join `programs` `p` on(`p`.`program_id` = `pr`.`program_id`)) join `requirements` `r` on(`r`.`requirement_id` = `pr`.`requirement_id`)) join `requirement_categories` `rc` on(`rc`.`category_id` = `r`.`category_id`)) WHERE `rc`.`is_active` = 1 AND `r`.`is_active` = 1 ORDER BY `p`.`program_id` ASC, `rc`.`display_order` ASC, `r`.`display_order` ASC ;

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_field_key` (`field_key`),
  ADD KEY `fk_ffc_page` (`page_id`);

--
-- Indexes for table `form_pages`
--
ALTER TABLE `form_pages`
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
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`program_id`);

--
-- Indexes for table `program_requirements`
--
ALTER TABLE `program_requirements`
  ADD PRIMARY KEY (`program_requirement_id`),
  ADD UNIQUE KEY `program_id` (`program_id`,`requirement_id`),
  ADD KEY `requirement_id` (`requirement_id`);

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
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

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
-- AUTO_INCREMENT for table `admin_credentials`
--
ALTER TABLE `admin_credentials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `applicants`
--
ALTER TABLE `applicants`
  MODIFY `applicant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `applicant_documents`
--
ALTER TABLE `applicant_documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `class_schedules`
--
ALTER TABLE `class_schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `educational_backgrounds`
--
ALTER TABLE `educational_backgrounds`
  MODIFY `educational_bg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `family_backgrounds`
--
ALTER TABLE `family_backgrounds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `personal_informations`
--
ALTER TABLE `personal_informations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `program_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `program_requirements`
--
ALTER TABLE `program_requirements`
  MODIFY `program_requirement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `requirements`
--
ALTER TABLE `requirements`
  MODIFY `requirement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `requirement_categories`
--
ALTER TABLE `requirement_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applicants`
--
ALTER TABLE `applicants`
  ADD CONSTRAINT `fk_applicant_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `applicant_documents`
--
ALTER TABLE `applicant_documents`
  ADD CONSTRAINT `applicant_documents_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`),
  ADD CONSTRAINT `applicant_documents_ibfk_2` FOREIGN KEY (`program_requirement_id`) REFERENCES `program_requirements` (`program_requirement_id`);

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `fk_application_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_application_program` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_applications_program` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `class_schedules`
--
ALTER TABLE `class_schedules`
  ADD CONSTRAINT `fk_schedule_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`year_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_schedule_program` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`) ON DELETE CASCADE;

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
-- Constraints for table `program_requirements`
--
ALTER TABLE `program_requirements`
  ADD CONSTRAINT `program_requirements_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`),
  ADD CONSTRAINT `program_requirements_ibfk_2` FOREIGN KEY (`requirement_id`) REFERENCES `requirements` (`requirement_id`);

--
-- Constraints for table `requirements`
--
ALTER TABLE `requirements`
  ADD CONSTRAINT `requirements_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `requirement_categories` (`category_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

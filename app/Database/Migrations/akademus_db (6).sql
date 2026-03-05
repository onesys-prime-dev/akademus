-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 05, 2026 at 08:00 AM
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
-- Table structure for table `applicant`
--

CREATE TABLE `applicant` (
  `applicant_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `applicant_number` varchar(30) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `temp_password` tinyint(1) DEFAULT 1,
  `application_status` enum('pending','approved','rejected','enrolled') DEFAULT 'pending',
  `application_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applicant`
--

INSERT INTO `applicant` (`applicant_id`, `user_id`, `program_id`, `applicant_number`, `password`, `temp_password`, `application_status`, `application_date`, `created_at`) VALUES
(13, 36, 3, 'APP-2026-00013', '$2y$10$mI64/4A6iMgJDmyFEFTmpOsvpA6RemsFTlLb5RUE77HBbL7I7fDUK', 1, 'pending', '2026-03-05 03:06:59', '2026-03-05 03:06:59'),
(22, 45, 2, 'APP-2026-00022', '$2y$10$ZYTC/snuDe261U3Io8yjm.GPIkqXjEY0UyW9U9uqstT7MU6Sldjk6', 1, 'pending', '2026-03-05 05:09:52', '2026-03-05 05:09:52'),
(25, 48, 1, 'APP-2026-00025', '$2y$10$A1Zk90PGgCPgGtiONweIyuYujHVETA2v9p2wn9Kr05WN.SlfgZ2we', 1, 'pending', '2026-03-05 06:56:45', '2026-03-05 06:56:45');

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
(17, 22, 'transferee', 3, 'SY 2025-2026', '1st Semester', 'draft', '2026-03-05 05:35:21', '2026-03-05 05:37:45');

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
  `id` int(11) NOT NULL,
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

INSERT INTO `educational_backgrounds` (`id`, `application_id`, `school_name`, `school_location`, `strand`, `school_year_start`, `school_year_end`, `gwa`, `school_type`, `created_at`, `updated_at`) VALUES
(17, 17, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-05 05:35:21', '2026-03-05 05:35:21');

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
(17, 17, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-05 05:35:21', '2026-03-05 05:35:21');

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

--
-- Dumping data for table `personal_informations`
--

INSERT INTO `personal_informations` (`id`, `application_id`, `firstname`, `middlename`, `lastname`, `suffix`, `gender`, `date_of_birth`, `place_of_birth`, `citizenship`, `home_address`, `contact_number`, `email_address`, `created_at`, `updated_at`) VALUES
(17, 17, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-05 05:35:21', '2026-03-05 05:35:21');

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
(36, 'Edgar', 'Baquiano', 'jbaqui20@gmail.com', '09888888888', 'applicant', '2026-03-05 03:06:59'),
(45, 'Josh', 'Baquiano', 'joshbaquiano@gmail.com', '09123456789', 'applicant', '2026-03-05 05:09:52'),
(48, 'Akademus', 'Mentora', 'testakademusmentora@gmail.com', '09987654321', 'applicant', '2026-03-05 06:56:45');

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_applicants`
-- (See below for the actual view)
--
CREATE TABLE `vw_applicants` (
`applicant_id` int(11)
,`user_id` int(11)
,`program_id` int(11)
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
-- Structure for view `vw_applicants`
--
DROP TABLE IF EXISTS `vw_applicants`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_applicants`  AS SELECT `a`.`applicant_id` AS `applicant_id`, `u`.`user_id` AS `user_id`, `a`.`program_id` AS `program_id`, `u`.`firstname` AS `firstname`, `u`.`lastname` AS `lastname`, `u`.`email` AS `email`, `u`.`mobile` AS `mobile`, `u`.`role` AS `role`, `a`.`applicant_number` AS `applicant_number`, `a`.`password` AS `password`, `a`.`temp_password` AS `temp_password`, `a`.`application_status` AS `application_status`, `a`.`application_date` AS `application_date`, `a`.`created_at` AS `created_at` FROM (`applicant` `a` join `users` `u` on(`a`.`user_id` = `u`.`user_id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_years`
--
ALTER TABLE `academic_years`
  ADD PRIMARY KEY (`year_id`);

--
-- Indexes for table `applicant`
--
ALTER TABLE `applicant`
  ADD PRIMARY KEY (`applicant_id`),
  ADD UNIQUE KEY `applicant_number` (`applicant_number`),
  ADD KEY `fk_applicant_user` (`user_id`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`application_id`);

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
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `family_backgrounds`
--
ALTER TABLE `family_backgrounds`
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
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`program_id`);

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
-- AUTO_INCREMENT for table `applicant`
--
ALTER TABLE `applicant`
  MODIFY `applicant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `class_schedules`
--
ALTER TABLE `class_schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `educational_backgrounds`
--
ALTER TABLE `educational_backgrounds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `family_backgrounds`
--
ALTER TABLE `family_backgrounds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `personal_informations`
--
ALTER TABLE `personal_informations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `program_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applicant`
--
ALTER TABLE `applicant`
  ADD CONSTRAINT `fk_applicant_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `class_schedules`
--
ALTER TABLE `class_schedules`
  ADD CONSTRAINT `fk_schedule_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`year_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_schedule_program` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

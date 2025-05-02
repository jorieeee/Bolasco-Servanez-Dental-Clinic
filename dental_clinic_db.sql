-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 21, 2025 at 05:53 AM
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
-- Database: `dental_clinic_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `patient_name` varchar(255) NOT NULL,
  `appointment_type` varchar(255) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` enum('Scheduled','Completed','Cancelled') DEFAULT 'Scheduled',
  `patient_id` int(11) DEFAULT NULL,
  `fee` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_name`, `appointment_type`, `appointment_date`, `appointment_time`, `status`, `patient_id`, `fee`) VALUES
(1, 'ellen catle', 'Teeth Cleaning', '2025-04-17', '18:36:00', 'Scheduled', NULL, 0.00),
(2, 'jorie gallardo', 'Tooth Extraction', '2025-04-14', '07:30:00', 'Scheduled', NULL, 0.00),
(3, 'leah joy abad', 'Teeth Cleaning', '2025-04-09', '08:00:00', 'Scheduled', NULL, 0.00),
(4, 'leah joy abad', 'Teeth Cleaning', '2025-04-09', '08:00:00', 'Scheduled', NULL, 0.00),
(5, 'shaina aragon', 'General Checkup', '2025-04-11', '14:58:00', 'Scheduled', NULL, 0.00),
(6, 'Josie Gallardo', 'Teeth Cleaning', '2025-04-17', '09:00:00', 'Scheduled', NULL, 0.00),
(7, 'thrtjtdyek', 'Dental Crown', '2025-04-21', '10:30:00', 'Scheduled', NULL, 0.00),
(8, 'rsyjuj ', 'Tooth Filling', '2025-04-21', '11:00:00', 'Scheduled', NULL, 0.00),
(9, 'mik6s5ui,6uut', 'Root Canal', '2025-04-21', '11:30:00', 'Scheduled', NULL, 0.00),
(10, 'rnj ite,yri yyik', 'Orthodontic Adjustment', '2025-04-21', '13:00:00', 'Scheduled', NULL, 0.00),
(11, 'myui,oi87tlo67o l ', 'Consultation', '2025-04-21', '14:00:00', 'Scheduled', NULL, 0.00),
(12, 'fgretn ryhi7,yti k', 'General Checkup', '2025-04-22', '09:00:00', 'Scheduled', NULL, 0.00),
(13, 'atb e4tjenu', 'Tooth Filling', '2025-04-21', '15:30:00', 'Scheduled', NULL, 0.00),
(14, 'Marjorie Gallardo', 'Teeth Cleaning', '2025-04-21', '16:00:00', 'Scheduled', NULL, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `dental_records`
--

CREATE TABLE `dental_records` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `date_of_visit` date NOT NULL,
  `chief_complaint` text DEFAULT NULL,
  `oral_hygiene` enum('Good','Fair','Poor') DEFAULT NULL,
  `gum_condition` enum('Healthy','Inflamed','Bleeding') DEFAULT NULL,
  `cavities_detected` text DEFAULT NULL,
  `missing_teeth` text DEFAULT NULL,
  `plaque_tartar` enum('Yes','No') DEFAULT NULL,
  `other_observations` text DEFAULT NULL,
  `recommended_treatment` text DEFAULT NULL,
  `procedures_done` text DEFAULT NULL,
  `next_appointment` date DEFAULT NULL,
  `prescribed_medications` text DEFAULT NULL,
  `dentist_name` varchar(100) NOT NULL,
  `license_number` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dental_records`
--

INSERT INTO `dental_records` (`id`, `patient_id`, `date_of_visit`, `chief_complaint`, `oral_hygiene`, `gum_condition`, `cavities_detected`, `missing_teeth`, `plaque_tartar`, `other_observations`, `recommended_treatment`, `procedures_done`, `next_appointment`, `prescribed_medications`, `dentist_name`, `license_number`) VALUES
(2, 8, '2025-04-07', 'N/A', 'Good', 'Healthy', 'N/A', 'N/A', 'Yes', 'N/A', 'N/A', 'N/A', '2025-05-07', 'N/A', 'N/A', 'N/A');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `address` text DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `medications` text DEFAULT NULL,
  `conditions` text DEFAULT NULL,
  `history` text DEFAULT NULL,
  `insurance_provider` varchar(100) DEFAULT NULL,
  `policy_number` varchar(50) DEFAULT NULL,
  `group_number` varchar(50) DEFAULT NULL,
  `policy_holder` varchar(100) DEFAULT NULL,
  `relationship` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `first_name`, `last_name`, `dob`, `gender`, `address`, `email`, `phone`, `allergies`, `medications`, `conditions`, `history`, `insurance_provider`, `policy_number`, `group_number`, `policy_holder`, `relationship`, `created_at`) VALUES
(8, 'jorie', 'gallardo', '2025-03-11', 'Female', 'dfcsdf', 'jorie@gmail.com', '464645756856', 'gtfh', 'thfthrth', 'gfhf', 'hthrfh', 'dfhgdh', '444634', '46', '436', 'self', '2025-03-30 11:34:43'),
(15, 'Marjorie ', 'Gallardo', '2004-12-05', 'Female', 'San Agustin, Iba, Zambales', 'gallardojorie@gmail.com', '09451007717', 'N/A', 'N/A', 'N/A', 'Tooth Extraction', 'N/A', '1', '1', 'N/A', 'self', '2025-04-21 03:52:02');

-- --------------------------------------------------------

--
-- Table structure for table `revenue`
--

CREATE TABLE `revenue` (
  `id` int(11) NOT NULL,
  `procedure_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `revenue`
--

INSERT INTO `revenue` (`id`, `procedure_name`, `price`, `date`) VALUES
(1, 'General Checkup', 500.00, '2025-04-17 15:36:09'),
(2, 'Teeth Cleaning', 800.00, '2025-04-17 15:36:09'),
(3, 'Dental Crown', 10000.00, '2025-04-17 15:36:09'),
(4, 'Tooth Filling', 2000.00, '2025-04-17 15:36:09'),
(5, 'Root Canal', 6500.00, '2025-04-17 15:36:09'),
(6, 'Tooth Extraction', 1000.00, '2025-04-17 15:36:09'),
(7, 'Orthodontic Adjustment', 2500.00, '2025-04-17 15:36:09'),
(8, 'Consultation', 500.00, '2025-04-17 15:36:09');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('dentist','receptionist') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `verification_code` varchar(10) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `attempts` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password`, `role`, `created_at`, `updated_at`, `verification_code`, `is_verified`, `attempts`) VALUES
(0, 'Ellen Catle', 'ellenpedrazacatle@gmail.com', '$2y$10$ctelGCZsHCtDrR/dXaeIcexqCClaF1r3ediqY9x9dJBm5djwuej6u', 'dentist', '2025-04-07 05:42:45', '2025-04-07 05:42:45', NULL, 0, 0),
(0, 'Marjorie Gallardo', 'gallardojorie@gmail.com', '$2y$10$0q9TjY41EVzk7jFO4FqaOuNRcKdtyXy2eqYDNRQkYnQpNrm6MM2TK', 'receptionist', '2025-04-07 05:45:10', '2025-04-07 06:07:54', '676703', 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dental_records`
--
ALTER TABLE `dental_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `revenue`
--
ALTER TABLE `revenue`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `dental_records`
--
ALTER TABLE `dental_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `revenue`
--
ALTER TABLE `revenue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dental_records`
--
ALTER TABLE `dental_records`
  ADD CONSTRAINT `dental_records_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

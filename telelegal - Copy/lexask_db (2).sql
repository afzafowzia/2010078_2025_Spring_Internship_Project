-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 25, 2025 at 05:02 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lexask_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `service_type` varchar(50) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `first_name`, `last_name`, `email`, `phone`, `address`, `service_type`, `appointment_date`, `appointment_time`, `notes`, `status`, `created_at`, `user_id`) VALUES
(1, 'Noa', 'Ken', 'gfu@gmail.com', 'ssdsds', 'nhgjhjgh', 'document-review', '2025-07-02', '09:00:00', '', 'pending', '2025-06-24 10:14:21', 2),
(2, 'Noa', 'G', 'gfu@gmail.com', 'zxczxcz', 'nngn', 'document-review', '2025-06-30', '09:00:00', 'ghjgh', 'cancelled', '2025-06-24 10:15:09', 2);

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `name`) VALUES
(1, 'English'),
(2, 'French'),
(5, 'Gujarati'),
(4, 'Hindi'),
(3, 'Mandarin'),
(6, 'Spanish');

-- --------------------------------------------------------

--
-- Table structure for table `lawyers`
--

CREATE TABLE `lawyers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `specialization` varchar(100) NOT NULL,
  `experience` varchar(50) NOT NULL,
  `rating` decimal(2,1) NOT NULL,
  `image` varchar(255) NOT NULL,
  `bio` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lawyers`
--

INSERT INTO `lawyers` (`id`, `name`, `specialization`, `experience`, `rating`, `image`, `bio`, `created_at`, `updated_at`) VALUES
(1, 'Ms. Sarah Johnson', 'Corporate Law', '12 years', 4.8, 'https://randomuser.me/api/portraits/women/65.jpg', 'Specialized in corporate mergers and acquisitions with a track record of successful deals.', '2025-06-17 16:58:55', '2025-06-22 12:25:48'),
(2, 'Mr. David Chen', 'Criminal Defense', '8 years', 4.6, 'https://randomuser.me/api/portraits/men/32.jpg', 'Former prosecutor with extensive courtroom experience and high acquittal rate.', '2025-06-17 16:58:55', '2025-06-17 16:58:55'),
(3, 'Ms. Amina Patel', 'Family Law', '10 years', 4.9, 'https://randomuser.me/api/portraits/women/44.jpg', 'Compassionate family law specialist focused on mediation and collaborative solutions.', '2025-06-17 16:58:55', '2025-06-17 16:58:55'),
(4, 'Mr. James Wilson', 'Intellectual Property', '15 years', 4.7, 'https://randomuser.me/api/portraits/men/75.jpg', 'Patent attorney with technical background in software engineering.', '2025-06-17 16:58:55', '2025-06-17 16:58:55'),
(5, 'Ms. Elizabeth Taylor', 'Immigration Law', '9 years', 4.7, 'https://randomuser.me/api/portraits/women/23.jpg', 'Specializes in work visas, green cards, and citizenship applications with a 98% approval rate.', '2025-06-24 09:37:05', '2025-06-24 09:37:05'),
(6, 'Mr. Michael Rodriguez', 'Personal Injury', '12 years', 4.8, 'https://randomuser.me/api/portraits/men/45.jpg', 'Recovered over $50 million for clients in accident and malpractice cases.', '2025-06-24 09:37:05', '2025-06-24 09:37:05'),
(7, 'Ms. Priya Sharma', 'Healthcare Law', '7 years', 4.5, 'https://randomuser.me/api/portraits/women/67.jpg', 'Advises hospitals and medical professionals on compliance and malpractice defense.', '2025-06-24 09:37:05', '2025-06-24 09:37:05'),
(8, 'Mr. Samuel Johnson', 'Real Estate Law', '15 years', 4.9, 'https://randomuser.me/api/portraits/men/88.jpg', 'Expert in commercial real estate transactions and property disputes.', '2025-06-24 09:37:05', '2025-06-24 09:37:05'),
(9, 'Ms. Fatima Al-Mansoori', 'International Law', '10 years', 4.6, 'https://randomuser.me/api/portraits/women/12.jpg', 'Specializes in cross-border business transactions and international disputes.', '2025-06-24 09:37:05', '2025-06-24 09:37:05'),
(10, 'Mr. Daniel Kim', 'Tax Law', '8 years', 4.4, 'https://randomuser.me/api/portraits/men/34.jpg', 'Helps individuals and businesses navigate complex tax regulations and disputes.', '2025-06-24 09:37:05', '2025-06-24 09:37:05'),
(11, 'Ms. Olivia Parker', 'Employment Law', '6 years', 4.3, 'https://randomuser.me/api/portraits/women/56.jpg', 'Focuses on workplace discrimination, wrongful termination, and labor disputes.', '2025-06-24 09:37:05', '2025-06-24 09:37:05'),
(12, 'Mr. Kwame Okafor', 'Corporate Compliance', '11 years', 4.7, 'https://randomuser.me/api/portraits/men/78.jpg', 'Advises multinational corporations on regulatory compliance and governance.', '2025-06-24 09:37:05', '2025-06-24 09:37:05');

-- --------------------------------------------------------

--
-- Table structure for table `lawyer_languages`
--

CREATE TABLE `lawyer_languages` (
  `lawyer_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lawyer_languages`
--

INSERT INTO `lawyer_languages` (`lawyer_id`, `language_id`) VALUES
(1, 1),
(1, 2),
(2, 1),
(2, 3),
(3, 1),
(3, 4),
(3, 5),
(4, 1),
(4, 6),
(5, 1),
(5, 2),
(5, 6),
(6, 1),
(6, 6),
(7, 1),
(7, 4),
(8, 1),
(9, 1),
(9, 2),
(10, 1),
(10, 3),
(11, 1),
(11, 2),
(12, 1),
(12, 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `profile_picture` varchar(255) DEFAULT 'default.jpg',
  `password` varchar(255) NOT NULL,
  `user_type` enum('client','lawyer','admin') DEFAULT 'client',
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `profile_picture`, `password`, `user_type`, `phone`, `address`, `created_at`, `updated_at`) VALUES
(1, 'Alva', 'alvarezamirah24@gmail.com', 'default.jpg', '$2y$10$dg8SWMj2nkKgSUEgUIxRk.wwcx66yydETX4bEwEHVyXjQAVJneVGO', 'client', NULL, NULL, '2025-06-18 07:13:18', '2025-06-18 07:13:18'),
(2, 'Shahanaz Akter', 'shahanazakter543@gmail.com', 'default.jpg', '$2y$10$epFAFZudrRf9ZrWFn6bYieOKtzcIiTAMtDWu1PoOp.D0ExciYSnla', 'client', NULL, NULL, '2025-06-22 12:18:05', '2025-06-22 12:18:05'),
(3, 'AFza', 'noorfowzia29@gmail.com', 'default.jpg', '$2y$10$Ph/GshgbfnAcedDS50OgmOBTxdfT..63IF6p0C6RHaCV42YyPUIY.', 'client', NULL, NULL, '2025-06-23 18:35:36', '2025-06-23 18:35:36'),
(4, 'Zubi', 'asdhsj@gmail.com', 'default.jpg', '$2y$10$tEUBqvXPRPfaHa7XQjLVsuN.1BHVkwG03Bk2KL8FJ3edL0ACFs3UC', 'client', NULL, NULL, '2025-06-24 05:09:56', '2025-06-24 05:09:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_appointment_user` (`user_id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `lawyers`
--
ALTER TABLE `lawyers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lawyer_languages`
--
ALTER TABLE `lawyer_languages`
  ADD PRIMARY KEY (`lawyer_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `lawyers`
--
ALTER TABLE `lawyers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `fk_appointment_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lawyer_languages`
--
ALTER TABLE `lawyer_languages`
  ADD CONSTRAINT `lawyer_languages_ibfk_1` FOREIGN KEY (`lawyer_id`) REFERENCES `lawyers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lawyer_languages_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

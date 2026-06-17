-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 28, 2026 at 10:09 AM
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
-- Database: `employee_mgmt`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('Present','Absent','Leave') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `department_name`, `description`) VALUES
(1, 'Software Engineering', 'This is used for All Software related Task');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `emp_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `department` varchar(50) DEFAULT NULL,
  `designation` varchar(50) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`emp_id`, `name`, `email`, `phone`, `department`, `designation`, `salary`, `joining_date`, `status`) VALUES
(2, 'mayur', 'sonawanemayur75708@gmain.com', NULL, 'IT', NULL, NULL, NULL, 'Active'),
(3, 'employee', 'employee123@gmail.com', NULL, 'IT', NULL, NULL, NULL, 'Active'),
(5, 'employee1', 'employee1@gmail.com', NULL, 'Finance', NULL, NULL, NULL, 'Active'),
(6, 'employee2', 'employee2@gmail.com', NULL, 'Sales', NULL, NULL, NULL, 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `leaves`
--

CREATE TABLE `leaves` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `leave_type` enum('Sick','Casual','Earned','Maternity/Paternity') NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `applied_on` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `month_year` varchar(20) NOT NULL,
  `basic_salary` decimal(10,2) NOT NULL,
  `allowance` decimal(10,2) DEFAULT 0.00,
  `deduction` decimal(10,2) DEFAULT 0.00,
  `net_salary` decimal(10,2) NOT NULL,
  `paid_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll`
--

INSERT INTO `payroll` (`id`, `emp_id`, `month_year`, `basic_salary`, `allowance`, `deduction`, `net_salary`, `paid_date`) VALUES
(3, 3, 'March 2026', 25000.00, 2500.00, 1850.00, 25650.00, '2026-03-04 13:02:26'),
(4, 5, 'March 2026', 36000.00, 1525.00, 1200.00, 36325.00, '2026-03-05 06:06:25'),
(5, 6, 'March 2026', 31500.00, 2100.00, 1870.00, 31730.00, '2026-03-05 06:06:45');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','HR','Employee') NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `status`) VALUES
(1, 'System Admin', 'admin@corp.com', 'admin@123', 'Admin', 'Active'),
(2, 'Virendra Arvind Solunke', 'virendrasolunke14@gmail.com', '$2y$10$bFE2L4PvqhJ15hYdUU9rm.CMDysARGh6PqUuKijObKvEYbZ9c1rGy', 'Employee', 'Active'),
(3, 'Rohit Dilip Solunke', 'rohitsolunke20@gmail.com', '$2y$10$Vcrx75mJWB8TKCGI2AealuVknD6BA.C.uMxAQp29dnuQeLZbBvE0W', 'Admin', 'Active'),
(4, 'Harshal Arvind Solunke ', 'harshalsolunke08@gmail.com', '$2y$10$VMx6l57sm8dmzJ6x2CsHHemCj3kU37NFDQgiEtK0PAxgBtEDprYVe', 'HR', 'Active'),
(5, 'System Admin', 'admin123@gmail.com', '$2y$10$i/rvOxK1emPbBwjPDUbkJ..vbq0.sR/v4dzM7N8QrO4MVJICPwVnG', 'Admin', 'Active'),
(6, 'Harshal Arvind Solunke ', 'harshal31@gmail.com', '$2y$10$hGK5B1NVP29iSMRXkEdbzend1EOcsLllu8h6QgCw1ogjdIbX.L.Qi', 'Employee', 'Active'),
(7, 'mayur', 'mayur123@gmail.com', '$2y$10$V.5ISnV8XYalL7LWlcZLJeIeZHjQoNvGdT6qrOeYckFD2iKKrm2Aq', 'Employee', 'Active'),
(8, 'harsh sawant', 'harshsawant123@gmail.com', '$2y$10$hU1ZPo3IfHMMgAVItKOTOuq6ZY7ccsCmsI840s8Hry/CPwSVvkeZ.', 'Employee', 'Active'),
(9, 'Employee1', 'employee123@gmail.com', '$2y$10$umtNJncwNzD6Q4td9Z8g7.6XV3tCDxQY6x.LLnPOaUEjYbpSq./1.', 'Employee', 'Active'),
(10, 'Employee2', 'employee2@gmail.com', '$2y$10$YzcYghFKezCh2ab0W0cUAeTjzyHXoQ3D1TIkY5Zo2snKAtpnon.g.', 'Employee', 'Active'),
(11, 'Employee3', 'employee3@gmail.com', '$2y$10$nioRgq4VNPiRv9FBZ0i7jOm/sNMOq3L1Or6DTn/ozrIGm.DOZCIWy', 'Employee', 'Active'),
(12, 'System Admin', 'systemadmin123@gmail.com', '$2y$10$q3SKmw3vbd7j2oFyNdJ0Q.Rg9B7bjoiseEbOP6.FgTUHkz/e.gzNS', 'Admin', 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attendance` (`emp_id`,`attendance_date`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `department_name` (`department_name`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `emp_id` (`emp_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`emp_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `leaves`
--
ALTER TABLE `leaves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `emp_id` (`emp_id`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`id`),
  ADD KEY `emp_id` (`emp_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `emp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `leaves`
--
ALTER TABLE `leaves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`) ON DELETE CASCADE;

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`) ON DELETE CASCADE;

--
-- Constraints for table `leaves`
--
ALTER TABLE `leaves`
  ADD CONSTRAINT `leaves_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`) ON DELETE CASCADE;

--
-- Constraints for table `payroll`
--
ALTER TABLE `payroll`
  ADD CONSTRAINT `payroll_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

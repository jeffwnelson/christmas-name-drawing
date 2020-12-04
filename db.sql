-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: db:3306
-- Generation Time: Dec 04, 2020 at 10:18 PM
-- Server version: 5.7.32
-- PHP Version: 7.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `app-db`
--
CREATE DATABASE IF NOT EXISTS `app-db` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `app-db`;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `roomcode` varchar(3) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `password` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `groupname` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `dollaramount` int(3) NOT NULL,
  `format` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `solution_attempts` int(4) DEFAULT NULL,
  `live` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userid` int(6) NOT NULL,
  `roomcode` varchar(3) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `nodraw` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `giftperson` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `viewed` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD UNIQUE KEY `roomcode` (`roomcode`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userid` int(6) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

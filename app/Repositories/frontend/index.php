-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 29, 2020 at 02:00 AM
-- Server version: 5.6.46-cll-lve
-- PHP Version: 7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `School_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
`id` bigint(20) UNSIGNED NOT NULL,
`uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`username` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`activation_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`status` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `uuid`, `email`, `username`, `password`, `activation_token`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, '0cbdc198-bfdf-4ff9-a9ec-b3ac2e904627', 'sgjeschool@gmail.com', 'Gnanajyothi', '$2y$10$Tzjs6Txf1SdtZ3f567Zmw.Dcp4jPDlVYIEmHzKLZqlS9hiLPnjLKa', '3601d873-de8f-43ad-b571-5edeb5b683f8', 'activated', NULL, '2019-09-30 20:49:45', '2019-09-30 20:49:45'),
(2, '6b4a0684-4f3f-4ec5-b309-82517ec1bfb3', 'manju.majsh@gmail.com', 'manja', '$2y$10$P9werZ1X4mvHLP4eOtryr.FbSexTr8W74UXDLSjDJTjERt3r9jyKG', 'ace18246-abd1-4ecc-8efb-10a14a28cc89', 'activated', NULL, '2019-10-01 02:41:53', '2019-10-01 02:41:53'),
(3, 'd48e0243-2f25-4c55-b714-74b4ea534c17', 'principal.sgjeschool@gmail.com', 'pawan', '$2y$10$gmZF7oOVuQX6e3ZpzWSTeeHEwij6bJLOkbk0HXb3ewJ6KbjwCqX/a', '82295ac9-b98c-40da-80e5-7feb42d32d07', 'activated', NULL, '2019-10-01 02:42:45', '2019-10-05 03:37:06'),
(4, '0829a93f-5e92-452e-ae3b-dd8c477dd58c', NULL, 'Student', '$2y$10$9Hr9ny2zBg1BYqsRZnTZDuE5a6bENCX8fH/id4Eu/nGiQMDAZPI3W', 'b66f0a86-6ef6-4f04-9345-fa8330457bf1', 'activated', NULL, '2019-10-05 04:16:02', '2019-10-05 04:16:03'),
(5, '5a11c118-c5c5-4815-a380-9cae9afd8b79', NULL, 'Parent', '$2y$10$vc2z2nwpNfoSpWg7.VsAuexcqqdFORTeY8iJqLEueZue2x14l0Ybi', 'fcef9318-d190-4850-ab42-eaefe1b3df58', 'activated', NULL, '2019-10-05 04:16:02', '2019-10-05 04:16:03'),
(6, 'e6b2137e-d9bd-4c0b-b69b-2878576a2ff7', NULL, 'Teacher', '$2y$10$GwNJt7e1ATIOtxdkk6QM1.K38Sv5WnEy66H.kEteKEDJr6JiL0aHu', '84e3ddd6-96e2-42bc-8f4e-e7c454652fa6', 'activated', NULL, '2019-10-05 04:40:00', '2019-10-05 04:40:32'),
(7, 'dd1711c4-bb3c-4825-a805-c823de68f903', 'sumask.sgjeschool@gmail.com', 'suma', '$2y$10$rHvDR0ltep5fFF0dNCYk2eJPH4dS2609wCkSMAXMlRcfLxPaUIkl6', '1b6a4a55-4972-4eea-856c-2a76bfca4423', 'activated', NULL, '2019-10-11 01:45:33', '2019-10-11 01:45:33'),
(8, '5fb1a5b0-b48a-4b43-a0aa-e0d0c878c87e', 'nkavitha.sgjeschool@gmail.com', 'kavitha', '$2y$10$4wEsoy17oQQIoH4VYsCIturQa3liFtIMGy65ex6OSY.9vGPXUj9Ue', '57addd6d-157a-4dff-b6a1-41f2f69fda95', 'activated', NULL, '2019-10-12 22:18:53', '2019-12-05 17:15:12'),
(9, 'b75591b2-0817-4920-b71e-e0f0c4d07362', 'shanker.sgjeschool@gmail.com', 'shanker', '$2y$10$PWEm3DFYM1i6mBJ6W3/AY.r226YA7psXNQUfRO7VqDGY4FUJsOlPO', '6b4fbead-f45d-4b18-acf9-1f195dd44961', 'activated', NULL, '2019-10-15 21:25:50', '2019-10-15 21:25:50'),
(10, 'a105fa92-9e22-4ce2-a27d-0dfa59b339d5', 'Latha.sgjeschool@gmail.com', 'Latha', '$2y$10$S.n68YbQtFe5u.Fo8qpELemBKjQoKU7tXOPP90l33ksCWc4fpsasy', 'ce6c43e0-c298-4763-b2f3-239eb225d1e0', 'activated', NULL, '2019-10-18 14:57:51', '2019-10-18 14:57:51'),
(11, 'cf84ef93-08db-4d7b-a373-e95ac990b58c', 'manja@gmail.com', 'majsh', '$2y$10$0o.fXpkfPqNaPRpTQGIuw.J/mik9xeny5KHMqEu8GrhDzO.d4u77m', '1778d45e-98bd-4084-bb46-4052faefd0df', 'activated', NULL, '2019-11-24 18:03:37', '2019-11-27 23:57:33'),
(12, 'de7312e3-2c04-4276-a0ab-9cbee411a7ba', 'sgjes1chool@gmail.com', 'aksh', '$2y$10$khftDAw9xxwH4Bn6/DdBP.L2ptPWm1hT49fJFodrLCV8zAcKicOw2', '5097355b-3c86-4edc-8094-171cc8ef3f64', 'banned', NULL, '2019-11-25 03:09:47', '2019-11-25 03:11:47'),
(13, 'f4c1c1ca-96c7-4475-82ff-83bf84d83bb1', NULL, 'nan', '$2y$10$lTRwCoRCtEOfok2gd4RoD.2A.ArqXx/B.IiCTlvbd56qKKo.cyJgG', '8779a179-f1fa-4c26-ae82-e8fd3580a470', 'activated', NULL, '2019-11-28 04:22:33', '2019-11-28 04:22:33'),
(14, 'd20770e7-2b2d-42bd-af6c-6d8c01ab70ba', NULL, 'bha', '$2y$10$zVrpuV9NAz56iMFi/1S5s.zIUpj08bJ3Jz5peSXj18j4ErBnA1a0K', 'bb5aeb0c-1e6a-479e-8b0e-9b1c9deff636', 'activated', NULL, '2019-11-30 23:02:05', '2019-11-30 23:02:05'),
(15, '622736f0-04e1-4560-bcf2-caab9626032f', 'sgjeschool1@gmail.com', 'majs', '$2y$10$b/m9PlN.HSs0BduzMxGgZO76d.oZTLrcK48oMFjeUfOCDK7/QZYjK', 'f29853d5-5dc7-4f7e-b199-00d92d4b06c5', 'activated', NULL, '2019-12-03 02:48:00', '2019-12-03 02:48:00'),
(16, '1f217027-2954-4202-abad-dee8d02115df', 'manju1.majsh@gmail.com', 'GNANA', '$2y$10$DCb5uAtTXNQTz.QR.JWdg.I5zCBLRoPdjwCg1Ju0qipmQOHxLcs56', '04aac252-d922-4f09-8e65-3611e1794855', 'activated', NULL, '2019-12-05 22:04:20', '2019-12-05 22:04:20'),
(17, '93d29058-ac56-4a89-bc67-b97f21504f49', 'shameem.sgjeschool@gmail.com', 'Shameem', '$2y$10$D5MkczcJCT02oZuZbKxePeAixUTekwLVGxNgxZ0XWGGbXxfjXZ3YK', 'f7e82e86-7f03-41cb-906c-f06bc2bf28cc', 'activated', NULL, '2019-12-06 22:01:05', '2019-12-19 00:35:09'),
(18, 'f790cd2b-9a89-4421-8e4c-64228864b693', 'kavitha@gmail.com', 'kavith', '$2y$10$eM2/Sp3LWL0.SnMWMNPJi.Xm8mN5Qzfx/hwdtRwp8H9M7tKKhJLk.', '5dead782-6566-4b9c-86a0-00adc3039c9a', 'activated', NULL, '2019-12-11 03:54:48', '2019-12-11 03:54:48'),
(19, '61ef9fb6-e92b-440f-b04b-0ff3745d5d83', 'teju@gmail.com', 'teju', '$2y$10$JJahbuehj29xQMhqSMo.uuYoXEu.l2OKUJQQNaUuUlk.004lzqr92', '38bdbe31-c21d-4f40-b975-18db66924db3', 'activated', NULL, '2019-12-11 03:56:15', '2019-12-11 03:56:15'),
(20, '393a2d5d-adff-4048-b09c-415257baff1b', 'balakrishnab.1993@gmail.com', 'balu', '$2y$10$FXhshdV5EJiS.6nihzGfruCJA2CYzU7O9fWJ/3XAzt.nfDQamD67S', 'acdde151-b34c-4c1c-9828-dfec848af88d', 'activated', NULL, '2019-12-14 03:55:16', '2019-12-14 03:56:44'),
(21, 'b9c0fa1f-300d-4876-9384-44b729f22273', NULL, 'sangeetha', '$2y$10$47s0RhpmsXd2bx5EJoowsOcbBXq6Xxh0ihqmzowyOWPwfRWHwAtxe', 'af737480-7df2-4dac-99be-6fc85e9d67a7', 'activated', NULL, '2020-01-13 20:46:30', '2020-01-13 20:46:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

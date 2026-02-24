-- ============================================
-- Latest Updates & Notifications Management
-- Add these tables to your database
-- ============================================

-- Table for Latest Updates (scrolling ticker)
CREATE TABLE IF NOT EXISTS `latest_updates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(500) NOT NULL,
  `link` varchar(500) DEFAULT NULL,
  `icon` varchar(50) DEFAULT 'bell',
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_active_order` (`is_active`, `display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for Notifications (sidebar)
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(500) NOT NULL,
  `link` varchar(500) DEFAULT NULL,
  `posted_date` date NOT NULL,
  `icon` varchar(50) DEFAULT 'circle',
  `is_important` tinyint(1) DEFAULT 0,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_active_date` (`is_active`, `posted_date` DESC),
  KEY `idx_display_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data for Latest Updates
INSERT INTO `latest_updates` (`title`, `link`, `icon`, `display_order`, `is_active`) VALUES
('Re-appearing candidates of H.S. 2nd Year (Arts & Science) Exam Fee Payment', 'notices/hs-2nd-year-fee.pdf', 'bell', 1, 1),
('MA Assamese 1st Sem. Arrear Exam Routine, 2025', 'notices/ma-assamese-routine.pdf', 'calendar', 2, 1),
('H.S. Final Year Revisionary Test-2025', 'notices/hs-revisionary-test.pdf', 'file-text', 3, 1);

-- Insert sample data for Notifications
INSERT INTO `notifications` (`title`, `link`, `posted_date`, `icon`, `is_important`, `display_order`, `is_active`) VALUES
('Nijut Babu Aasoni, 2025-2026', 'notices/nijut-babu-aasoni.pdf', '2026-01-07', 'circle', 1, 1, 1),
('MA Assamese 1st Sem. Arrear Exam Routine,2025', 'notices/ma-assamese-routine.pdf', '2025-12-10', 'circle', 0, 2, 1),
('H.S. Final Year Revisionary Test-2025', 'notices/hs-revisionary.pdf', '2025-12-08', 'circle', 0, 3, 1),
('NOTICE', 'notices/general-notice.pdf', '2025-12-08', 'circle', 0, 4, 1),
('Boys Hostel Admission Notice', 'notices/boys-hostel.pdf', '2025-12-03', 'circle', 0, 5, 1);

-- ============================================
-- Run this in phpMyAdmin SQL tab
-- Database: college_db
-- ============================================

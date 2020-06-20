--
-- Table structure for table `form_alergy_care_plan`
--

CREATE TABLE IF NOT EXISTS `form_alergy_care_plan` (
  `id` bigint(20) NOT NULL,
  `alergyname` text,
  `alergytype` text,
  `notes` varchar(255) DEFAULT NULL
) ENGINE=InnoDB;



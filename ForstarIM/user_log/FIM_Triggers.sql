DROP TRIGGER IF EXISTS `m_process_del`;

DELIMITER //
CREATE TRIGGER `m_process_del` AFTER DELETE ON `m_process` FOR EACH ROW BEGIN
 UPDATE meta_data_log set update_time=NOW() where table_name='m_process';
END//
DELIMITER ;




DROP TRIGGER IF EXISTS `m_process_ins`;

DELIMITER //
CREATE TRIGGER `m_process_ins` AFTER INSERT ON `m_process` FOR EACH ROW BEGIN
 UPDATE meta_data_log set update_time=NOW() where table_name='m_process';
END//
DELIMITER ;



DROP TRIGGER IF EXISTS `m_process_uptd`;

DELIMITER //
CREATE TRIGGER `m_process_uptd` AFTER UPDATE ON `m_process` FOR EACH ROW BEGIN
 UPDATE meta_data_log set update_time=NOW() where table_name='m_process';
END//
DELIMITER ;



DROP TRIGGER IF EXISTS `ppseq_del`;

DELIMITER //
CREATE TRIGGER `ppseq_del` AFTER DELETE ON `pre_process_sequence` FOR EACH ROW BEGIN
 UPDATE meta_data_log set update_time=NOW() where table_name='pre_process_sequence';
END//
DELIMITER ;



DROP TRIGGER IF EXISTS `ppseq_ins`;

DELIMITER //
CREATE TRIGGER `ppseq_ins` AFTER INSERT ON `pre_process_sequence` FOR EACH ROW BEGIN
 UPDATE meta_data_log set update_time=NOW() where table_name='pre_process_sequence';
END//
DELIMITER ;



DROP TRIGGER IF EXISTS `ppseq_uptd`;

DELIMITER //
CREATE TRIGGER `ppseq_uptd` AFTER UPDATE ON `pre_process_sequence` FOR EACH ROW BEGIN
 UPDATE meta_data_log set update_time=NOW() where table_name='pre_process_sequence';
END//
DELIMITER ;
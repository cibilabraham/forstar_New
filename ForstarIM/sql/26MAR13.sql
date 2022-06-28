INSERT INTO `function` (`module_id`, `name`, `url`, `target`, `pmenu_id`, `form_add`, `form_edit`, `form_delete`, `form_print`, `form_confirm`, `form_reedit`, `processing_activity`, `menu_order`, `group_main_id`, `frm_cpny_specific`) 
	VALUES  (3, 'Daily Stock Report', 'DailyStockReport.php', NULL, 13, 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'N', 423, 0, 'Y');

INSERT INTO `function` (`module_id`, `name`, `url`, `target`, `pmenu_id`, `form_add`, `form_edit`, `form_delete`, `form_print`, `form_confirm`, `form_reedit`, `processing_activity`, `menu_order`, `group_main_id`, `frm_cpny_specific`) 
	VALUES  (2, 'Frozen Stock Allocation', 'FrozenStockAllocation.php', NULL, 8, 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'N', 424, 0, 'Y');

CREATE TABLE `t_dailyfrozenpacking_po` (
	`id` INT(5) NOT NULL AUTO_INCREMENT,
	`entry_id` INT(5) NULL DEFAULT NULL,
	`po_id` INT(5) NULL DEFAULT NULL,
	`total_slabs` INT(11) NULL DEFAULT NULL,
	`total_qty` FLOAT(10,2) NULL DEFAULT NULL,
	`processcode_id` INT(5) NULL DEFAULT NULL,
	`freezing_stage_id` INT(5) NULL DEFAULT NULL,
	`frozencode_id` INT(5) NULL DEFAULT NULL,
	`mcpacking_id` INT(5) NULL DEFAULT NULL,
	`created_on` DATE NULL DEFAULT NULL,
	`created_by` INT(5) NULL DEFAULT NULL,
	`deleted` INT(5) NULL DEFAULT '0',
	`deleted_on` DATE NULL DEFAULT NULL,
	`deleted_by` INT(5) NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	INDEX `tdfppo_entry_id` (`entry_id`),
	INDEX `tdfppo_po_id` (`po_id`),
	INDEX `processcode_id` (`processcode_id`),
	INDEX `freezing_stage_id` (`freezing_stage_id`),
	INDEX `frozencode_id` (`frozencode_id`),
	INDEX `mcpacking_id` (`mcpacking_id`)
);



CREATE TABLE `t_dailyfrozenpacking_allocate` (
	`id` INT(5) NOT NULL AUTO_INCREMENT,
	`po_entry_id` INT(5) NULL DEFAULT NULL,
	`grade_id` INT(5) NULL DEFAULT NULL,
	`number_mc` INT(5) NULL DEFAULT NULL,
	`number_loose_slab` INT(5) NULL DEFAULT NULL,
	`created_on` DATE NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	INDEX `tdfpa_grade_id` (`grade_id`)
);


CREATE TABLE `t_dailyfrozenpacking_allocated_entry` (
	`id` INT(5) NOT NULL AUTO_INCREMENT,
	`processcode_id` INT(5) NULL DEFAULT NULL,
	`freezing_stage_id` INT(5) NULL DEFAULT NULL,
	`frozencode_id` INT(5) NULL DEFAULT NULL,
	`mcpacking_id` INT(5) NULL DEFAULT NULL,
	`entry_id` INT(5) NULL DEFAULT NULL,
	`created_on` DATE NULL DEFAULT NULL,
	`created_by` INT(5) NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	INDEX `processcode_id` (`processcode_id`),
	INDEX `freezing_stage_id` (`freezing_stage_id`),
	INDEX `frozencode_id` (`frozencode_id`),
	INDEX `mcpacking_id` (`mcpacking_id`),
	INDEX `entry_id` (`entry_id`)
);

ALTER TABLE `t_dailyfrozenpacking_entry` ADD COLUMN `quick_entry_list_id` INT(5) NULL DEFAULT NULL AFTER `customer_id`;

ALTER TABLE `c_system` ADD COLUMN `ls_mc_conversion_type` ENUM('AC','MC') NULL DEFAULT 'MC' COMMENT 'AC-Auto Convert. MC-Manually Convert. Auto or Manually conversion in Daily Frozen packing' AFTER `adv_amt_restriction`;
UPDATE c_system SET ls_mc_conversion_type='MC';

ALTER TABLE `t_dailyfrozenpacking_grade` ADD COLUMN `convert_type` VARCHAR(5) NULL COMMENT 'AC- auto convert, MC - Manually Convert' AFTER `settled_date`;

ALTER TABLE `t_dailyfrozenpacking_grade` ADD COLUMN `mc_old` INT(5) NULL DEFAULT NULL AFTER `convert_type`, ADD COLUMN `loose_slab_old` INT(5) NULL DEFAULT NULL AFTER `mc_old`, ADD COLUMN `converted_date` DATE NULL DEFAULT NULL AFTER `loose_slab_old`;




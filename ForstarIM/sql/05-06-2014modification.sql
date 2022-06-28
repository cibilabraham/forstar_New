ALTER TABLE `weighment_data_sheet`
  DROP `pond_id`,
  DROP `pond_details`,
  DROP `product_species`,
  DROP `product_code`,
  DROP `grade_count`,
  DROP `count_code`,
  DROP `weight`,
  DROP `soft_percent`,
  DROP `soft_weight`,
  DROP `package_type`,
  DROP `pkg_nos`,
  DROP `total_quantity`,
  DROP `received_at_unit`,
  DROP `harvesting_equipment`,
  DROP `issued`,
  DROP `used`,
  DROP `returned`,
  DROP `different`;
  
  
ALTER TABLE  `weighment_data_sheet` ADD  `supply_area` INT NOT NULL AFTER  `receiving_supervisor` ,
ADD  `supplier_group` INT NOT NULL AFTER  `supply_area` ,
ADD  `procurement_gatepass_available` INT NOT NULL AFTER  `supplier_group`;

ALTER TABLE `t_weightment_data_entries`
  DROP `product_code`,
  DROP `grade_count`;

  
  
  
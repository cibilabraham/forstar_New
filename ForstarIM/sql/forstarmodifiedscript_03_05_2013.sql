alter table m_physical_stk_packing add daily_frozen_stk_used_status int(2);
alter table m_physical_stk_packing_entry add(fish_id int(5),processcode_id int(10),freezing_stage int(15),frozencode_id int(15),mcpacking_id int(15),grade_id int(15),num_mc int(15),num_ls int(15));
alter table t_dailyfrozenpacking_main add physical_stock_main_id int(10);
alter table m_dist_report_definition_entry add active int(1) default 0;
update m_dist_report_definition_entry set active=1
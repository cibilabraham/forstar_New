alter table m_processratelist add active int(1) default 0;
update m_processratelist set active=1
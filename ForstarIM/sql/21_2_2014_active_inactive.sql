alter table m_eucode add active int(1) default 0;
update m_eucode set active=1;
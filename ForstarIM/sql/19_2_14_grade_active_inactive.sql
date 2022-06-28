alter table m_grade add active int(1) default 0;
update m_grade set active=1;
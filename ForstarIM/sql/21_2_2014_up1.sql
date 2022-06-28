update m_freezingstage set active=1;
alter table m_repacking add active int(1) default 0;
update m_repacking set active=1;
alter table m_paymentterms add active int(1) default 0;
update m_paymentterms set active=1;
alter table m_supplier_ing add active int(1) default 0;
update m_supplier_ing set active=1;
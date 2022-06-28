alter table m_product_category add active int(1) default 0;
update  m_product_category set active=1;
alter table m_product_state add active int(1) default 0;
update  m_product_state set active=1;
alter table m_product_group add active int(1) default 0;
update m_product_group set active=1;

alter table m_productmaster add active int(5) default 0;
update m_productmaster set active=1;

alter table m_product_price add active int(1) default 0;
update m_product_price set active=1;

alter table m_productprice_ratelist add active int(5) default 0;
update m_productprice_ratelist set active=1;

alter table m_product_manage add active int(5) default 0;
update m_product_manage set active=1;
alter table m_prodn_travel add active int(5) default 0;
update m_prodn_travel set active=1;

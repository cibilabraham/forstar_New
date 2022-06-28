1.alter table m_fish add active int(1) default 0;
update m_fish set active=1;
2.alter table m_grade add active int(1) default 0;
update m_grade set active=1;
3.alter table m_fishcategory add active int(1) default 0;
update m_fishcategory set active=1;
4.alter table m_landingcenter add active int(1) default 0;
update m_landingcenter  set active=1;


5.alter table m_preprocessor add activeconfirm int(1) default 0;
update m_preprocessor set activeconfirm=1;
6.alter table m_processcode add active int(1) default 0;
update m_processcode set active=1;
7.alter table m_process add active int(1) default 0;
update m_process set active=1;
8.alter table m_processratelist add active int(1) default 0;
update m_processratelist set active=1;



9.alter table m_eucode add active int(1) default 0;
update m_eucode set active=1;
10.alter table m_customer add active int(1) default 0;
update m_customer set active=1;
11.alter table m_glaze add active int(1) default 0;
update m_glaze set active=1;
12.alter table m_brand add active int(1) default 0;
update m_brand set active=1;
13.alter table m_freezingstage add active int(1) default 0;
update m_freezingstage set active=1;
14.alter table m_mcpacking add active int(1) default 0;
update m_mcpacking set active=1;
15.alter table m_freezing add active int(1) default 0;
update m_freezing set active=1;
16.alter table m_frozenpacking add active int(1) default 0;
update m_frozenpacking set active=1;
17.alter table m_packagingstructure add active int(1);
update m_packagingstructure set active=1;
18.alter table m_repacking add active int(1);
update m_repacking set active=1;
19.alter table m_freezercapacity add active int(1);
update m_freezercapacity set active=1;



alter table m_status add active int(1) default 0;
update m_status set active=1;
alter table m_labellingstage add active int(1) default 0;
update m_labellingstage set active=1;
alter table m_usd add active int(1) default 0;
update m_usd set active=1;


alter table m_country add active int(1) default 0;
update m_country set active=1; 
alter table m_shipping_company add active int(1) default 0; 
update m_shipping_company set active=1;
alter table m_invoice_type add active int(1) default 0;
update m_invoice_type set active=1; 
alter table m_carriage_mode add active int(1) default 0;
update m_carriage_mode set active=1; 
alter table m_unitmaster add active int(1) default 0;
update m_unitmaster set active=1; 

alter table m_plant add active int(1) default 0;
update m_plant set active=1;
alter table m_processingactivities add active int(1) default 0;
update m_processingactivities set active=1;
alter table m_billing_company add active int(1) default 0;
update m_billing_company set active=1;
alter table m_operation_type add active int(1);
update m_operation_type set active=1;
alter table m_monitoring_parameters add active int(1);
update m_monitoring_parameters set active=1;
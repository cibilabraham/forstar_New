RM procurement order


SELECT max(`return_days`) FROM `m_pond_master`

SELECT max(return_days) FROM m_pond_master a left join t_rmprocurmentsupplier b on a.id=b.pond_id

SELECT max(return_days) FROM m_pond_master a left join t_rmprocurmentsupplier b on a.id=b.pond_id left join t_rmprocurmentorder c on b.rmProcurmentOrderId=c.id

SELECT max(return_days),d.schedule_date FROM m_pond_master a left join t_rmprocurmentsupplier b on a.id=b.pond_id left join t_rmprocurmentorder c on c.id=b.rmProcurmentOrderId left join t_rm_procurement_driver d on c.id=d.rmProcurmentOrderId

SELECT a.*,max(e.return_days) from m_driver_master a left join t_rm_procurement_driver b on a.id=b.driver_id left join t_rmprocurmentorder c on c.id=b.rmProcurmentOrderId left join t_rmprocurmentsupplier d on d.rmProcurmentOrderId=c.id left join m_pond_master e on e.id=d.pond_id

SELECT a.*,d.*,e.return_days from m_driver_master a left join t_rm_procurement_driver b on a.id=b.driver_id left join t_rmprocurmentorder c on c.id=b.rmProcurmentOrderId left join t_rmprocurmentsupplier d on d.rmProcurmentOrderId=c.id left join m_pond_master e on e.id=d.pond_id

SELECT a.id,a.name_of_person,b.schedule_date,d.pond_id,e.return_days from m_driver_master a left join t_rm_procurement_driver b on a.id=b.driver_id left join t_rmprocurmentorder c on c.id=b.rmProcurmentOrderId left join t_rmprocurmentsupplier d on d.rmProcurmentOrderId=c.id left join m_pond_master e on e.id=d.pond_id where c.generated='0' and a.active=1 order by a.name_of_person


=>1
SELECT a.id,a.name_of_person,b.schedule_date,d.pond_id,e.return_days from m_driver_master a left join t_rm_procurement_driver b on a.id=b.driver_id left join t_rmprocurmentorder c on c.id=b.rmProcurmentOrderId left join t_rmprocurmentsupplier d on d.rmProcurmentOrderId=c.id left join m_pond_master e on e.id=d.pond_id where c.generated='0' and a.id='23' and a.active=1 

=>3
SELECT a.id,a.name_of_person,b.schedule_date,d.pond_id,e.return_days from m_driver_master a left join t_rm_procurement_driver b on a.id=b.driver_id left join t_rmprocurmentorder c on c.id=b.rmProcurmentOrderId left join t_rmprocurmentsupplier d on d.rmProcurmentOrderId=c.id left join m_pond_master e on e.id=d.pond_id where c.generated='0' and a.id='24' and a.active=1

==>2
 SELECT a.id,a.name_of_person,b.schedule_date,d.pond_id,e.return_days from m_driver_master a left join t_rm_procurement_driver b on a.id=b.driver_id left join t_rmprocurmentorder c on c.id=b.rmProcurmentOrderId left join t_rmprocurmentsupplier d on d.rmProcurmentOrderId=c.id left join m_pond_master e on e.id=d.pond_id where c.generated='0' and a.id='25' and a.active=1 
 
==>1 
 SELECT a.id,a.name_of_person,b.schedule_date,d.pond_id,e.return_days from m_driver_master a left join t_rm_procurement_driver b on a.id=b.driver_id left join t_rmprocurmentorder c on c.id=b.rmProcurmentOrderId left join t_rmprocurmentsupplier d on d.rmProcurmentOrderId=c.id left join m_pond_master e on e.id=d.pond_id where c.generated='0' and a.id='26' and a.active=1 
 
 ==>1
 SELECT a.id,a.name_of_person,b.schedule_date,d.pond_id,e.return_days from m_driver_master a left join t_rm_procurement_driver b on a.id=b.driver_id left join t_rmprocurmentorder c on c.id=b.rmProcurmentOrderId left join t_rmprocurmentsupplier d on d.rmProcurmentOrderId=c.id left join m_pond_master e on e.id=d.pond_id where c.generated='0' and a.id='27' and a.active=1 
 
 ==>0
 SELECT a.id,a.name_of_person,b.schedule_date,d.pond_id,e.return_days from m_driver_master a left join t_rm_procurement_driver b on a.id=b.driver_id left join t_rmprocurmentorder c on c.id=b.rmProcurmentOrderId left join t_rmprocurmentsupplier d on d.rmProcurmentOrderId=c.id left join m_pond_master e on e.id=d.pond_id where c.generated='0' and a.id='28' and a.active=1 
 
 ==>0
 SELECT a.id,a.name_of_person,b.schedule_date,d.pond_id,e.return_days from m_driver_master a left join t_rm_procurement_driver b on a.id=b.driver_id left join t_rmprocurmentorder c on c.id=b.rmProcurmentOrderId left join t_rmprocurmentsupplier d on d.rmProcurmentOrderId=c.id left join m_pond_master e on e.id=d.pond_id where c.generated='0' and a.id='29' and a.active=1 
 
 ==>1
 SELECT a.id,a.name_of_person,b.schedule_date,d.pond_id,e.return_days from m_driver_master a left join t_rm_procurement_driver b on a.id=b.driver_id left join t_rmprocurmentorder c on c.id=b.rmProcurmentOrderId left join t_rmprocurmentsupplier d on d.rmProcurmentOrderId=c.id left join m_pond_master e on e.id=d.pond_id where c.generated='0' and a.id='30' and a.active=1 
 
 =>0
 SELECT a.id,a.name_of_person,b.schedule_date,d.pond_id,e.return_days from m_driver_master a left join t_rm_procurement_driver b on a.id=b.driver_id left join t_rmprocurmentorder c on c.id=b.rmProcurmentOrderId left join t_rmprocurmentsupplier d on d.rmProcurmentOrderId=c.id left join m_pond_master e on e.id=d.pond_id where c.generated='0' and a.id='31' and a.active=1
 
 

 
Daily preprocess 
 
  select * from t_rmweightaftergradingdetails e left join t_rmweightaftergrading f on f.id=e.weightment_grading_id where f.created_on='2014-09-29'
  
  
  
  
  select a.id, a.code, SUBSTRING_INDEX(a.processes,',',1) as pfrom, a.processes, a.fish_id, b.name,f.rmLotId as rmlot,f.rmLotId as lot,frs.process_criteria as process,frs.sort_id as sort,tos.process_criteria as tosprocess,tos.sort_id as tossort  from m_process a left join m_fish b on a.fish_id=b.id join pre_process_sequence frs on frs.processcode_id = substring(a.processes,1,instr(a.processes,',')-1) left join pre_process_sequence tos on tos.processcode_id = substring(a.processes,length(a.processes)-instr(reverse(a.processes),',')+2)  left join    t_rmweightaftergradingdetails e on e.process_code_id =frs.processcode_id left join t_rmweightaftergrading f on f.id=e.weightment_grading_id where e.fish_id='36' and f.created_on='2014-09-29'and a.fish_id='36' and a.rate_list_id='1' 
  
  
 f.rmLotId  and SUBSTRING_INDEX(a.processes,',',1) not in ( select b.rm_lot_id,a.process from t_dailypreprocess_entries_rmlotid a left join t_dailypreprocess_rmlotid b on b.id=a.dailypreprocess_main_id )
 
 
 select a.id, a.code, SUBSTRING_INDEX(a.processes,',',1) as pfrom, a.processes, a.fish_id, b.name,f.rmLotId as rmlot,f.rmLotId as lot,frs.process_criteria as process,frs.sort_id as sort,tos.process_criteria as tosprocess,tos.sort_id as tossort  from m_process a left join m_fish b on a.fish_id=b.id join pre_process_sequence frs on frs.processcode_id = substring(a.processes,1,instr(a.processes,',')-1) left join pre_process_sequence tos on tos.processcode_id = substring(a.processes,length(a.processes)-instr(reverse(a.processes),',')+2)  left join    t_rmweightaftergradingdetails e on e.process_code_id =frs.processcode_id left join t_rmweightaftergrading f on f.id=e.weightment_grading_id where e.fish_id='36' and f.created_on='2014-09-29'and a.fish_id='36' and a.rate_list_id='1' and not exists (select m.rm_lot_id,l.process from t_dailypreprocess_entries_rmlotid l left join t_dailypreprocess_rmlotid m on m.id=l.dailypreprocess_main_id where m.rm_lot_id=f.rmLotId and l.process=SUBSTRING_INDEX(a.processes,',',1))
 
 
 
 
 select a.id, a.code, SUBSTRING_INDEX(a.processes,',',1) as pfrom, a.processes, a.fish_id, b.name,f.rmLotId as rmlot,f.rmLotId as lot,frs.process_criteria as process,frs.sort_id as sort,tos.process_criteria as tosprocess,tos.sort_id as tossort  from m_process a left join m_fish b on a.fish_id=b.id join pre_process_sequence frs on frs.processcode_id = substring(a.processes,1,instr(a.processes,',')-1) left join pre_process_sequence tos on tos.processcode_id = substring(a.processes,length(a.processes)-instr(reverse(a.processes),',')+2)  left join    t_rmweightaftergradingdetails e on e.process_code_id =frs.processcode_id left join t_rmweightaftergrading f on f.id=e.weightment_grading_id where e.fish_id='36' and f.created_on='2014-09-29'and a.fish_id='36' and a.rate_list_id='1' and not exists (select m.rm_lot_id,l.process from t_dailypreprocess_entries_rmlotid l left join t_dailypreprocess_rmlotid m on m.id=l.dailypreprocess_main_id where m.rm_lot_id=f.rmLotId and l.process=a.id)
 
 
 
 soaking
 
 
 
select d.id,d.code,e.id,e.name,SUBSTRING_INDEX(c.processes,',',1) as processes,a.process as process, sum(a.total_preprocess_qty) as sum from  t_dailypreprocess_entries a left join t_dailypreprocess b on a.dailypreprocess_main_id=b.id left join m_process c  on c.id=a.process left join  m_processcode d on d.id=SUBSTRING_INDEX(c.processes,',',1) left join m_fish e on e.id=d.fish_id  where b.date='$entryDate' and b.fish_id='$filterId' GROUP BY d.id 
		
		union all

		select d.id,d.code,e.id,e.name,'notval' as processes,a.fish_code as process,sum(a.effective_wt) as sum from  t_dailycatchentry a left join t_dailycatch_main b on a.main_id=b.id  left join  m_processcode d on d.id=a.fish_code left join m_fish e on e.id=a.fish  where a.select_date='$entryDate' and a.fish='$filterId'  GROUP BY d.id   and d.id not in (select d.id from  t_dailypreprocess_entries a left join t_dailypreprocess b on a.dailypreprocess_main_id=b.id left join m_process c  on c.id=a.process left join  m_processcode d on d.id=SUBSTRING_INDEX(c.processes,',',1) left join m_fish e on e.id=d.fish_id  where b.date='$entryDate' and b.fish_id='$filterId' GROUP BY d.id)
		
	union all

		SELECT a.id,a.code,b.id,b.name,'noval' as processes ,'noval' as process,'0' as sum  FROM m_processcode a left join m_fish b on b.id=a.fish_id WHERE a.fish_id ='".$filterId."' and a.active='1' and a.id not in(select d.id from  t_dailypreprocess_entries a left join t_dailypreprocess b on a.dailypreprocess_main_id=b.id left join m_process c  on c.id=a.process left join  m_processcode d on d.id=SUBSTRING_INDEX(c.processes,',',1) left join m_fish e on e.id=d.fish_id  where b.date='$entryDate' and b.fish_id='$filterId' GROUP BY d.id union all select d.id,d.code,e.id,e.name,'notval' as processes,a.fish_code as process,sum(a.effective_wt) as sum from  t_dailycatchentry a left join t_dailycatch_main b on a.main_id=b.id  left join  m_processcode d on d.id=a.fish_code left join m_fish e on e.id=a.fish  where a.select_date='$entryDate' and a.fish='$filterId'    GROUP BY d.id ) "
 
 
 
 
 
 
 
 
 



























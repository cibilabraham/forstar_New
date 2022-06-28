<?php	
	class Config_query 
	{ 
		// var	 $db;
		public $Response;				//Array of response
		
		
		function Config_query(&$databaseConnect)
		{
			//$this->LookupDB = LookupService::getInstance();	// create the  LookupService object 
			//$this->db = $this->LookupDB->lookupDatabase('','');	
			// echo $this->databaseConnect = $this->conn; // connect to database
			// echo 'tt';
			// die;
			$this->db =&$databaseConnect;
		}
		
		/* Get the value from single table */
		public function getItems($tableName,$fieldNames = '*',$where = array())
		{					
			$query ="SELECT ".$fieldNames." FROM ".$tableName;
			
			if(sizeof($where) != 0)
			{
				$query.= " WHERE ";$i=0;
				foreach($where as $field=>$value)
				{
					if($i > 0)
					{
						$query.= " AND ".$field."='".$value."'";
					}
					else
					{
						$query.= $field."='".$value."'";
					}
					$i++;
				}
			}
					
			$this->Response		=	array();
			$this->Response		=	$this->db->fetch_array($query);	
			return $this->Response;				 
		}		
		 
		/* Get the value from table using complex queries */
		public function getItemsDirect($query)
		{		
			 $this->Response		=	array();
			 $this->Response		=	$this->db->getRecords($query);	
			 return $this->Response;				 
		}	
		
		/* Insert the values to table */
		public function addData($tableName,$dataArray)
		{
			
			try
			{						
				$incAdddataArray = $this->db->insertData($tableName,$dataArray);
				if( $incAdddataArray  ) {
					$this->db->commit(); 
					
					return $this->db->lastInserted();
				}
				else throw new Exception("Failed to add new user to the system.");
			}
			catch (Exception $e) {
				$this->db->rollback();
				
				return $e->getMessage();
				
			}		
		}
		
		/* Update the values from table */
		public function updateData($tableName,$dataArray,$where)
				{			
					try{
						$updateData       = $this->db->updateData($tableName,$dataArray,$where);
						if( $updateData  ) {
							$this->db->commit(); 
							
							return $updateData;
						}
						else throw new Exception("Failed to Update user records.");
					}
					catch (Exception $e) {
						$this->db->rollback();
						return $e->getMessage();
						
					}		
				}	
		/* Update the values from table */
		public function deleteData($tableName,$where,$delCon)
		{
			try
			{
				$delRes           = $this->db->$delCon($tableName,$where);
				if( $delRes  ) 
				{
					$this->db->commit(); 
					return $delRes;
				}
				else throw new Exception("Failed to delete records.");
			}
			catch (Exception $e) 
			{
				$this->db->rollback();
				return $e->getMessage();
						
			}		
		}
		

	}
?>
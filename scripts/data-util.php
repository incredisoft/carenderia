<?php
	class DbManager
	{
		public static $instance;
		
		var $connection_pool = null;
		var $config = null;
	
		public function __construct()
		{
			$this->config = parse_ini_file("configuration.ini", true);
			$this->connection_pool = array();
		}
		
		public function lookup($dbname)
		{
			if(!array_key_exists($dbname, $this->connection_pool))
				$this->connection_pool[$dbname] = new DbConnection($this->config['server'], $this->config['database'][$dbname]);
			return $this->connection_pool[$dbname];
		}
		
		public function close()
		{
			foreach ($this->connection_pool as $value)
				$value->close();
		}
		
		public function rollback()
		{
			foreach ($this->connection_pool as $value)
				$value->rollback();
		}
		
		public function commit()
		{
			foreach ($this->connection_pool as $value)
				$value->commit();
		}
		
		public static function ExecuteTransaction( $onexecute, $onerror )
		{
			$dm = new DbManager();
			try
			{
				$onexecute( $dm );
				$dm->commit();
			}catch(Exception $e){
				$dm->rollback();
				$onerror($e);
			}
		}
		
		public static function getInstance()
		{
			if( self::$instance == null )
				self::$instance = new DbManager();
			return self::$instance;
		}
	}
	
	class DbConnection
	{
		var $link;
		var $dbname;
		
		public function __construct($server, $dbname)
		{
			$this->link = new PDO("mysql:host=".$server['host'].";dbname=$dbname", $server['user'], $server['password']);
			$this->link->beginTransaction();
		}
		
		public function execute($name, $param, $conditions = false)
		{
			$db = DbManager::getInstance()->lookup('system');
			$query = $db->single('SELECT o.* FROM querytable o WHERE o.name = :name', array('name' => $name));
			
			if(!$query)
				throw new Exception("Query name '$name' not exist.");
			
			if($query['type'] === 'select') return $this->select($query['statement'], $param);
			if($query['type'] === 'single') return $this->single($query['statement'], $param);
			
			if($query['type'] === 'update' || $query['type'] === 'insert' || $query['type'] === 'delete')
			{
				$stmt = $this->link->prepare($insert_stmt.' '.$values_stmt);
				$success = false;
				
				if($param)
					$success = $stmt->execute($params);
				else
					$sucess = $stmt->execute();
				
				if($success) return;
			}
			
			throw new Exception("Error in executing query name '$name'");
		}
		
		public function single($statement, $params)
		{
			$stmt = $this->link->prepare($statement);
			if( $params != null )
				$success = $stmt->execute($params);
			else
				$success = $stmt->execute();
			
			if(!$success)
				throw new Exception("Error in executing select $statement");
				
			return $stmt->fetch(PDO::FETCH_ASSOC);
		}
		
		public function select($statement, $params)
		{
			$parameters = array();
			foreach (array_keys($params) as $key)
			{
				if (substr($key, 0, 1) === '_'){
					$name = substr($key, 1, strlen($key) -1 );
					$statement = str_replace('$P{'.$name.'}', $params[$key].'', $statement);
				}
				else{
					$parameters[$key] = $params[$key];
				}
			}
			
			$stmt = $this->link->prepare($statement);
			if($params)
				$success = $stmt->execute($parameters);
			else
				$success = $stmt->execute();
			
			if(!$success)
				throw new Exception("Error in executing select $statement");
			
			$result = array();
			$index = 0;
			while( ($val = $stmt->fetch(PDO::FETCH_ASSOC)) != null )
			{
				$result[$index] = $val;
				$index += 1;
			}
			return $result;
		}
		
		public function insert($table, $values)
		{
			$insert_stmt = "INSERT INTO $table(";
			$values_stmt = "VALUES(";
			$start = TRUE;
			
			foreach (array_keys($values) as $key)
			{
				if( $start )
				{
					$insert_stmt .= $key.'';
					$values_stmt .= ':'.$key;
					$start = FALSE;
				}
				else
				{
					$insert_stmt .= ', '.$key;
					$values_stmt .= ', :'.$key;
				}
			}
			$insert_stmt .= ')';
			$values_stmt .= ')';
			$stmt = $this->link->prepare($insert_stmt.' '.$values_stmt);
			if( !$stmt->execute($values) )
				throw new Exception("Error in executing insert '$insert_stmt $values_stmt'");
		}
		
		public function delete($table, $conditions)
		{
			$delete_stmt = "DELETE FROM $table WHERE ";
			$start = TRUE;
			foreach (array_keys($conditions) as $key)
			{
				if( $start )
				{
					$delete_stmt .= $key.' = :'.$key;
					$start = FALSE;
				}
				else
				{
					$delete_stmt .= ', '.$key.' = :'.$key;
				}
			}
			$stmt = $this->link->prepare($delete_stmt);
			$res = $stmt->execute($conditions);
			if( !$res )
				throw new Exception("Error in executing delete '$delete_stmt'");
		}
		
		public function update($table, $values, $conditions)
		{
			$update_stmt = "UPDATE $table SET ";
			$condition_stmt = " WHERE ";
			
			$start = TRUE;
			foreach (array_keys($values) as $key)
			{
				if( $start )
				{
					$update_stmt .= $key.' = :'.$key;
					$start = FALSE;
				}
				else
				{
					$update_stmt .= ', '.$key.' = :'.$key;
				}
			}
			
			$start = TRUE;
			foreach (array_keys($conditions) as $key)
			{
				if( $start )
				{
					$condition_stmt .= $key.' = :'.$key;
					$start = FALSE;
				}
				else
				{
					$condition_stmt .= ', '.$key.' = :'.$key;
				}
			}
			
			$stmt = $this->link->prepare($update_stmt.' '.$condition_stmt);
			$res = $stmt->execute(array_merge($values, $conditions));
			if( !$res )
				throw new Exception("Error in executing update '$update_stmt $condition_stmt'");
		}
		
		public function commit()
		{
			$this->link->commit();
		}
		
		public function rollback()
		{
			$this->link->rollback();
		}
		
		public function close()
		{
			$this->link->close();
		}
	}
?>
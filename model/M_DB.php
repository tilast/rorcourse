<?php

class M_DB
{
	private $dbh;
	function __construct()
	{
		try
		{
			if(!file_exists("model/todolists2.db")) {
				$this->dbh = new PDO("sqlite:model/todolists2.db");
				$this->dbh->beginTransaction();
				$this->dbh->exec("
					CREATE TABLE projects(
						project_id INTEGER PRIMARY KEY,
						name TEXT
					)
				");
				$this->dbh->exec("
					CREATE TABLE tasks(
						task_id INTEGER PRIMARY KEY,
						project_id INTEGER,
						content TEXT,
						priority INTEGER,
						status INTEGER
					)
				");
				$this->dbh->exec("INSERT INTO projects(name) VALUES('25.08 planes')");
				$this->dbh->exec("INSERT INTO projects(name) VALUES('26.08 planes')");
				$this->dbh->exec("INSERT INTO tasks(content, project_id, status) VALUES('go to meeting with Pavlo Lisovyi', 1, 1)");
				$this->dbh->exec("INSERT INTO tasks(content, project_id) VALUES('end development of this site', 1)");
				$this->dbh->exec("INSERT INTO tasks(content, project_id) VALUES('meet Sasha', 2)");
				$this->dbh->exec("INSERT INTO tasks(content, project_id) VALUES('go to work', 2)");
				$this->dbh->exec("INSERT INTO tasks(content, project_id) VALUES('walk with Sasha', 2)");
				$this->dbh->commit();
			} else {
				$this->dbh = new PDO("sqlite:model/todolists2.db");
			}
		}
		catch(PDOException $ex)
		{
			echo $ex->getMessage();
		}
		catch(Exception $ex)
		{
			$ex->getMessage();
		}
	}
	private static $instance;
	public static function Instance()
	{
		if(self::$instance == null)
			self::$instance = new M_DB();

		return self::$instance;
	}
	public function Select($query)
	{
		try
		{
			$stmt = $this->dbh->prepare($query);
			$stmt->execute();
			$arr = array();
			if($stmt) {
				while($row = $stmt->fetch(PDO::FETCH_ASSOC))
				{
					$arr[] = $row;
				}
			} else {
				$arr = array();
			}

			return $arr;
		}
		catch(PDOException $ex)
		{
			echo $ex->getMessage();
		}
		catch(Exception $ex)
		{
			$ex->getMessage();
		}
	}
	public function Insert($table, $obj)
	{
		try
		{
			$columns = array();
			$values = array();

			foreach($obj as $key => $value)
			{
				$key = $this->dbh->quote($key) . '';
				$columns[] = $key;

				if($value === null)
					$values[] = null;
				else
				{
					$value = ($value) . '';
					$values[] = '"'.$value.'"';
				}
			}

			$columns_s = implode(", ", $columns);
			$values_s = implode(", ", $values);


			$this->dbh->exec("INSERT INTO $table ($columns_s) VALUES ($values_s)");
			return $this->dbh->lastInsertId();
		}
		catch(PDOException $ex)
		{
			$ex->getMessage();
		}
		catch(Exception $ex)
		{
			$ex->getMessage();
		}
	}
	public function Update($table, $object, $where)
	{
		try
		{
			$columns = array();
			$set = array();
			foreach($object as $key => $value)
			{
				$key = $key . '';
				if($value === NULL)
					$set[] = "$key=NULL";
				else
				{
					$value = $value . '';
					$set[] = "$key=\"$value\"";
				}
				$set_s = implode(",", $set);
			}
			//return "UPDATE $table SET $set_s WHERE $where";

			return $this->dbh->exec("UPDATE $table SET $set_s WHERE $where");
		}
		catch(PDOException $ex)
		{
			$ex->getMessage();
		}
		catch(Exception $ex)
		{
			$ex->getMessage();
		}
	}
	public function Delete($table, $where)
	{
		try
		{
			$this->dbh->exec("DELETE FROM $table WHERE $where");
			return true;
		}
		catch(PDOException $ex)
		{
			$ex->getMessage();
		}
		catch(Exception $ex)
		{
			$ex->getMessage();
		}
	}
} 
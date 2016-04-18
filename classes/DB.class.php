<?php

require_once("../../../dbconst.php");

class DB
{
	/*Attributes*/
	private $db;
	
	/*Constructor*/
	//Starts the database connection - Create PDO
	function __construct()
	{
		try
		{
			$this->db = new PDO("mysql:host=".HOST.";dbname=".DB,USER,PASSWORD);
			$this->db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION); //setting the error mode to ERRMODE_EXCEPTION
		}
		catch(PDOException $pe)
		{
			$logDetails = $this->getBasicLogInfo();
			$logDetails["Error name"]="Connection open issue";
			$logDetails["Error Description"]=$pe->getMessage();
			throw new DLException($logDetails);
		}
	}
	
	/*Methods*/
	
	//Returns a statement after preparing and binding the parameters
	function prepare($strQuery, $paramValues, $paramTypes)
	{
		try
		{
			$stmt = $this->db->prepare($strQuery);
			
			//Taking out the placeholders from the query string and storing it in an array
			$placeholders = array();
			$pos = 0;
			while($pos<strlen($strQuery))
			{
				$start = strpos($strQuery,':',$pos);
				if($start>0)
				{
					$space_pos = strpos($strQuery," ",$start);
					if($space_pos>0)
					{
						$placeholders[] = substr($strQuery, $start, $space_pos-$start);
						$pos = $space_pos;
					}
					else
					{
						$placeholders[] = substr($strQuery, $start, strlen($strQuery)-$start+1);
						$pos = strlen($strQuery);
					}
				}
				else
					break;
			}
			
			//Replacing paramTypes characters with PDO Parameter type using utility function setPDOParam
			$paramTypesPDO = array();
			foreach($paramTypes as $singleParamType)
			{
				$paramTypesPDO[] = $this->getPDOParam(strtolower($singleParamType));
			}
			
			for($i=0; $i<count($paramValues); $i++)
			{
				$stmt->bindParam($placeholders[$i],$paramValues[$i],$paramTypesPDO[$i]);
			}
			
			return $stmt;
		}
		catch(PDOException $pe)
		{
			$logDetails = $this->getBasicLogInfo();
			$logDetails["Query"] = $strQuery;
			$logDetails["Error name"]="Prepare Statement Issue";
			$logDetails["Error Description"]=$pe->getMessage();
			throw new DLException($logDetails);
		}
	}
	
	//Returns an associative array with RowsAffected and InsertId values
	function setData($strQuery, $paramValues, $paramTypes)
	{
		try
		{
			$stmt = $this->prepare($strQuery, $paramValues, $paramTypes);
			$stmt->execute();
			$rowsAffected = $stmt->rowCount();
			$insertId = $this->db->lastInsertId();
			return array("RowsAffected"=>$rowsAffected,"InsertId"=>$insertId);
		}
		catch(PDOException $pe)
		{
			$logDetails = $this->getBasicLogInfo();
			$logDetails["Query"] = $strQuery;
			$logDetails["Error name"]="Set Data Issue";
			$logDetails["Error Description"]=$pe->getMessage();
			throw new DLException($logDetails);
		}
	}
	
	//Maps the object of the $classname passed - Returns an array of objects
	//If row not present, then returns false
	function getData($strQuery, $paramValues, $paramTypes, $className)
	{
		try
		{
			$stmt = $this->prepare($strQuery, $paramValues, $paramTypes);
			$stmt->execute();
			$stmt->setFetchMode(PDO::FETCH_CLASS,$className);
			while($objectOfClass = $stmt->fetch())
			{
				$arrayOfObjects[] = $objectOfClass;
			}
			if(count($arrayOfObjects)>0)
				return $arrayOfObjects;
			else
				return false;
		}
		catch(PDOException $pe)
		{
			$logDetails = $this->getBasicLogInfo();
			$logDetails["Query"] = $strQuery;
			$logDetails["Error name"]="Get Data Issue";
			$logDetails["Error Description"]=$pe->getMessage();
			throw new DLException($logDetails);
		}
	}
	
	/*Utility Functions*/
	
	//Function to return PDO Parameter type based on a character(s)
	function getPDOParam($str)
	{
		switch($str)
		{
			case "b":
				return PDO::PARAM_BOOL;
			break;
			
			case "n":
				return PDO::PARAM_NULL;
			break;
			
			case "i":
				return PDO::PARAM_INT;
			break;
			
			case "s":
				return PDO::PARAM_STR;
			break;
			
			case "l":
				return PDO::PARAM_LOB;
			break;
		}
		
	}
	
	//Function to create an array used to log the common exception details which were caught
	//This array will be used in DLException class.
	function getBasicLogInfo()
	{
		$basicLogInfo = array();
		$basicLogInfo["Logged at"] = date("F d, Y h:i a");
		$basicLogInfo["DB Host Server"] = HOST;
		$basicLogInfo["DB User"] = USER;
		$basicLogInfo["DB Name"] = DB;
		return $basicLogInfo;
	}
}

?>
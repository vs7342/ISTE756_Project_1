<?php

class User
{
	//Attributes
	private $Uid;
	private $Email;
	private $Password;
	private $FirstName;
	private $LastName;
	private $Role;
	
	//Constructor
	function __construct($Uid=null, $Email=null, $Password=null, $FirstName=null, $LastName=null, $Role=null)
	{
		if($Uid!=null)
		{
			$this->Uid = $Uid;
		}
		if($Email!=null)
		{
			$this->Email = $Email;
		}
		if($Password!=null)
		{
			$this->Password = $Password;
		}
		if($FirstName!=null)
		{
			$this->FirstName = $FirstName;
		}
		if($LastName!=null)
		{
			$this->LastName = $LastName;
		}
		if($Role!=null)
		{
			$this->Role = $Role;
		}
	}
	
	//Accessors
	function getUid()
	{
		return $this->Uid;
	}
	function getEmail()
	{
		return $this->Email;
	}
	function getPassword()
	{
		return $this->Password;
	}
	function getFirstName()
	{
		return $this->FirstName;
	}
	function getLastName()
	{
		return $this->LastName;
	}
	function getRole()
	{
		return $this->Role;
	}
	
	//Mutators
	function setUid($Uid)
	{
		$this->Uid = $Uid;
	}
	function setEmail($Email)
	{
		$this->Email = $Email;
	}
	function setPassword($Password)
	{
		$this->Password = $Password;
	}
	function setFirstName($FirstName)
	{
		$this->FirstName = $FirstName;
	}
	function setLastName($LastName)
	{
		$this->LastName = $LastName;
	}
	function setRole($Role)
	{
		$this->Role = $Role;
	}
	
	//Methods
	
	//returns an array of Users (actually only single user but needs to be accessed as an array) based on email and password
	//password is hashed using SQL's md5.
	function login()
	{
		$selectQuery = "SELECT Uid, Email, FirstName, LastName, Role FROM users WHERE Email = :Email AND Password = md5(:Password )";
		$paramTypes = array('s','s');
		$paramValues = array($this->Email, $this->Password);
		
		try
		{
			$db = new DB();
			$result = $db->getData($selectQuery, $paramValues, $paramTypes, "User");
			return $result;
		}
		catch(DLException $dle)
		{
			throw $dle;
		}
	}
	
	//Creates the user
	function signup()
	{
		$insertQuery = "INSERT INTO users (
			Email, Password, FirstName, LastName, Role
		) VALUES (:Email ,md5(:Password ),:FirstName ,:LastName ,:Role )";
		$paramValues = array(
			$this->Email,
			$this->Password,
			$this->FirstName,
			$this->LastName,
			$this->Role,
		);
		$paramTypes = array('s','s','s','s','s');
		
		try
		{
			$db = new DB();
			$result = $db->setData($insertQuery, $paramValues, $paramTypes);
			if($result["RowsAffected"]==1)
			{
				$this->Uid = $result["InsertId"];
				return true;
			}
			else
				return false;
		}
		catch(DLException $dle)
		{
			throw $dle;
		}
	}
	
	
	//function to check whether the email id already exists.
	//used during signup
	function checkEmailExists()
	{
		$selectQuery = "SELECT Email FROM users WHERE Email = :Email ";
		$paramTypes = array('s');
		$paramValues = array($this->Email);
		
		try
		{
			$db = new DB();
			$result = $db->getData($selectQuery, $paramValues, $paramTypes, "User");
			return $result;
		}
		catch(DLException $dle)
		{
			throw $dle;
		}
	}
}

?>
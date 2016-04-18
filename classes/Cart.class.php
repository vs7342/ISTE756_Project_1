<?php

class Cart
{
	/*Attributes*/
	private $Uid;
	private $Pid;
	
	/*Constructor*/
	function __construct($Uid=null,$Pid=null)
	{
		if($Uid!=null)
		{
			$this->Uid = $Uid;
		}
		if($Pid!=null)
		{
			$this->Pid = $Pid;
		}
	}
	
	/*Accessors*/
	function getUid()
	{
		return $this->Uid;
	}
	function getPid()
	{
		return $this->Pid;
	}
	
	/*Mutators*/
	function setUid($Uid)
	{
		$this->Uid = $Uid;
	}
	function setPid($Pid)
	{
		$this->Pid = $Pid;
	}
	
	/*Methods*/
	
	//function to create a cart - that is with user id and product id
	function post()
	{
		$insertQuery = "INSERT INTO cart (Uid, Pid) VALUES (:Uid ,:Pid )";
		$paramValues = array($this->Uid, $this->Pid);
		$paramTypes = array('i','i');
		
		try
		{
			$db = new DB();
			$result = $db->setData($insertQuery, $paramValues, $paramTypes);
			if($result["RowsAffected"]==1)
			{
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
	
	//returns array of cart objects based on the user id
	//a used can have many products in the cart
	function fetchCartForUid()
	{
		$selectQuery = "SELECT Uid,Pid FROM cart WHERE Uid = :Uid ";
		$paramValues = array($this->Uid);
		$paramTypes = array('i');
		
		try
		{
			$db = new DB();
			$result = $db->getData($selectQuery, $paramValues, $paramTypes, "Cart");
			return $result;
		}
		catch(DLException $dle)
		{
			throw $dle;
		}
	}
	
	
	//deletes all the products from the cart for a particular User ID- used in Empty Cart functionality
	function deleteCartForUid()
	{
		$deleteQuery = "DELETE FROM cart WHERE Uid = :Uid";
		$paramValues = array($this->Uid);
		$paramTypes = array('i');
		try
		{
			$db = new DB();
			$result = $db->setData($deleteQuery, $paramValues, $paramTypes);
			return $result["RowsAffected"];
		}
		catch(DLException $dle)
		{
			throw $dle;
		}
	}
}

?>
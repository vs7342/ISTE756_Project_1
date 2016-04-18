<?php

class Product
{
	/*Attributes*/
	protected $Pid;
	protected $ProductName;
	protected $Description;
	protected $Price;
	protected $Quantity;
	protected $ImageName;
	protected $SalePrice;
	
	/*Constructor*/
	function __construct($Pid=null, $ProductName=null, $Description=null, $Price=null, $Quantity=null, $ImageName=null, $SalePrice=null){
		if($Pid!=null)
		{
			$this->Pid = $Pid;
		}
		if($ProductName!=null)
		{
			$this->ProductName = $ProductName;
		}
		if($Description!=null)
		{
			$this->Description = $Description;
		}
		if($Price!==null)
		{
			$this->Price = $Price;
		}
		if($Quantity!==null)
		{
			$this->Quantity = $Quantity;
		}
		if($ImageName!=null)
		{
			$this->ImageName = $ImageName;
		}
		if($SalePrice!==null)
		{
			$this->SalePrice = $SalePrice;
		}
	}
	
	/*Accessors*/
	public function getPid()
	{
		return $this->Pid;
	}
	function getProductName()
	{
		return $this->ProductName;
	}
	function getDescription()
	{
		return $this->Description;
	}
	function getPrice()
	{
		return $this->Price;
	}
	function getQuantity()
	{
		return $this->Quantity;
	}
	function getImageName()
	{
		return $this->ImageName;
	}
	function getSalePrice()
	{
		return $this->SalePrice;
	}
	
	
	/*Mutators*/
	function setPid($Pid)
	{
		$this->Pid = $Pid;
	}
	function setProductName($ProductName)
	{
		$this->ProductName = $ProductName;
	}
	function setDescription($Description)
	{
		$this->Description = $Description;
	}
	function setPrice($Price)
	{
		$this->Price = $Price;
	}
	function setQuantity($Quantity)
	{
		$this->Quantity = $Quantity;
	}
	function setImageName($ImageName)
	{
		$this->ImageName = $ImageName;
	}
	function setSalePrice($SalePrice)
	{
		$this->SalePrice = $SalePrice;
	}

	/*Methods*/
	
	//Function to Create a product
	function post()
	{
		$insertQuery = "INSERT INTO products (
			ProductName, Description, Price, Quantity, ImageName, SalePrice
		) VALUES (:ProductName ,:Description ,:Price ,:Quantity ,:ImageName ,:SalePrice )";
		$paramValues = array(
			$this->ProductName,
			$this->Description,
			$this->Price,
			$this->Quantity,
			$this->ImageName,
			$this->SalePrice
		);
		$paramTypes = array('s','s','i','i','s','i');
		
		try
		{
			$db = new DB();
			$result = $db->setData($insertQuery, $paramValues, $paramTypes);
			if($result["RowsAffected"]==1)
			{
				$this->Pid = $result["InsertId"];
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
	
	
	//Function to Read a Product
	//Returns an array of Product objects
	//Fetches product and adds constraints based on
	//1. $onSale --> Based on true/false, returns products which are on sale or not
	//2. $this->Pid --> If Pid is set, returns a single Product row/object
	function fetch($onSale=null)
	{
		$selectQuery = "SELECT 
			Pid, ProductName, Description, Price, Quantity, ImageName, SalePrice
		FROM products ";
		$paramTypes = array();
		$paramValues = array();
		
		//Adding where clause based on $onSale and/or whether $Pid is set.
		if($onSale!==null||$this->Pid!==null)
		{
			$selectQuery.="WHERE ";
			if($onSale!==null)
				if($onSale===true)
					$selectQuery.= "SalePrice > 0 ";
				else
					$selectQuery.= "SalePrice = 0 ";
				
			if($onSale!==null && $this->Pid!==null)
				$selectQuery.= "AND ";
			
			if($this->Pid!==null)
			{
				$selectQuery.="Pid = :Pid";
				$paramTypes[] = 'i';
				$paramValues[] = $this->Pid;
			}
		}
		try
		{
			$db = new DB();
			$result = $db->getData($selectQuery, $paramValues, $paramTypes, "Product");
			return $result;
		}
		catch(DLException $dle)
		{
			throw $dle;
		}
	}
	
	//Function to Update a Product
	function put()
	{
		$updateQuery = "UPDATE products SET ";
		
		//building update query based on params passed
		//Also adding values and types of these params in respective arrays(paramValues and paramTypes)
		if($this->ProductName!=null)
		{
			$updateQuery.="ProductName = :ProductName ,";
			$paramValues[] = $this->ProductName;
			$paramTypes[] = "s";
		}
		if($this->Description!=null)
		{
			$updateQuery.="Description = :Description ,";
			$paramValues[] = $this->Description;
			$paramTypes[] = "s";
		}
		if($this->Price!==null)
		{
			$updateQuery.="Price = :Price ,";
			$paramValues[] = $this->Price;
			$paramTypes[] = "i";
		}
		if($this->Quantity!==null)
		{
			$updateQuery.="Quantity = :Quantity ,";
			$paramValues[] = $this->Quantity;
			$paramTypes[] = "i";
		}
		if($this->ImageName!=null)
		{
			$updateQuery.="ImageName = :ImageName ,";
			$paramValues[] = $this->ImageName;
			$paramTypes[] = "s";
		}
		if($this->SalePrice!==null)
		{
			$updateQuery.="SalePrice = :SalePrice ,";
			$paramValues[] = $this->SalePrice;
			$paramTypes[] = "i";
		}
		$updateQuery = trim($updateQuery,',');
		$updateQuery.= " WHERE Pid = :Pid ";
		$paramValues[] = $this->Pid;
		$paramTypes[] = "i";
		
		try
		{
			$db = new DB();
			$result = $db->setData($updateQuery, $paramValues, $paramTypes);
			return $result["RowsAffected"];
		}
		catch(DLException $dle)
		{
			throw $dle;
		}
	}
	
	//Function to Delete a product
	function delete()
	{
		$deleteQuery = "DELETE FROM products WHERE Pid = :Pid";
		$paramValues = array($this->Pid);
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
	
	//Function to Delete specified quantity of a product
	//Currently deletes one quantity - can be updgraded to user defined qty later
	function deleteQty()
	{
		$deleteQuery = "UPDATE products SET Quantity = Quantity - :deleteQty  WHERE Pid = :Pid";
		$deleteQty = 1;
		$paramValues = array($deleteQty, $this->Pid);
		$paramTypes = array('i','i');
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
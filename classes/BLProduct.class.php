<?php

class BLProduct extends Product
{
	function post()
	{
		if($this->SalePrice>0)
		{
			$onSaleProducts = parent::fetch(true);
			if(count($onSaleProducts)>=5)
			{
				/*SaleConstraintViolation*/
				//echo "<h1>SaleConstraintViolation</h1>";
				//return false;
				return "Cannot have more than 5 products on Sale!!";
			}
		}
		$postSuccess = parent::post();
		return $postSuccess;
	}
	
	function put()
	{
		if($this->SalePrice!==null) //updating SalePrice
		{
			$tempPid = $this->Pid;
			$this->Pid = null; //Setting the Pid to null for proper functioning of fetch
			$onSaleProducts = parent::fetch(true);
			
			//checks if updating saleprice of an item already on sale 
			$productAlreadyOnSale = false;
			foreach($onSaleProducts as $singleProduct)
			{
				if($tempPid==$singleProduct->Pid)
				{
					$productAlreadyOnSale = true;
					break;
				}
			}
			if(
				($this->SalePrice>0 && count($onSaleProducts)>=5 && !$productAlreadyOnSale)|| //Trying to put an item on sale
				($this->SalePrice==0 && count($onSaleProducts)<=3)  //Trying to remove an item from sale
			)
			{
				/*SaleConstraintViolation*/
				//echo "<h1>SaleConstraintViolation</h1>";
				//return false;
				return "Cannot have more than 5 OR less than 3 products on Sale!!";
			}
			$this->Pid = $tempPid;
			
		}
		$putSuccess = parent::put();
		return $putSuccess;
	}
	
	function delete()
	{
		$productDetails = parent::fetch(); //fetching product details since we only know Pid
		$this->SalePrice = $productDetails[0]->SalePrice; //Taking out SalePrice from product details and setting current object's SalePrice
		if($this->SalePrice>0)
		{
			$tempPid = $this->Pid;
			$this->Pid = null; //Setting the Pid to null for proper functioning of fetch
			$onSaleProducts = parent::fetch(true);
			if(count($onSaleProducts)<=3)
			{
				/*SaleConstraintViolation*/
				//echo "<h1>SaleConstraintViolation</h1>";
				//return false;
				return "Cannot have less than 3 products on Sale!!";
			}
			$this->Pid = $tempPid;
		}
		$deleteSuccess = parent::delete();
		return $deleteSuccess;
	}
}

?>
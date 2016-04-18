<?php
session_start();
if(!isset($_SESSION["uid"]) || !isset($_SESSION["role"]))
{
	$Msg="<strong>Warning!!</strong> Please Login to View items in your cart";
	$MsgType = "alert alert-warning";
}

require_once("LIB_project1.php");

if(isset($_POST["emptyCart"]))
{
	$uid = $_SESSION["uid"];
	$cart = new BLCart($uid);
	$rows = $cart->deleteCartForUid();
	if($rows>0)
	{
		$Msg="Your Cart has been emptied";
		$MsgType = "alert alert-success";
	}
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Chocolate Factory - Cart</title>
	<?=getExternalSheetsInfo()?>
</head>
<body>
	<div class="header">
		<?=getHeader()?>
	</div>
	
	<div id="navigation">
		<?=getNavigation()?>
	</div>
	
	<div class="messages">
		<p class='<?=$MsgType?>'><?=$Msg?></p>
	</div>
	
	<div id="cart" class="productSection">
		<h1 class="productSectionHeaders">Your Cart Contents</h1>
		<?php
			//Only if user is logged in -->session uid is set = proceed
			if(isset($_SESSION["uid"]))
			{
				$uid = $_SESSION["uid"];
				
				//Get cart array using uid
				$cart = new BLCart($uid);
				$cart = $cart->fetchCartForUid();
				if($cart!=false)
				{
					$totalPrice = 0;
					for ($i=0;$i<count($cart);$i++)
					{
						$pid = $cart[$i]->getPid();
						$product = new BLProduct($pid);
						$product = $product->fetch();
						
						//since array of products is recvd
						$product = $product[0];
						//Getting details of that product
						$ProductName = $product->getProductName();
						$Description = $product->getDescription();
						$Price = $product->getPrice();
						$SalePrice = $product->getSalePrice();
						
						//Setting Price of product which has to be displayed(Sale/Normal)
						if($SalePrice==0)
						{
							$PriceToDisplay = $Price;
							$totalPrice +=$Price;
						}
						else
						{
							$PriceToDisplay = $SalePrice;
							$totalPrice+=$SalePrice;
						}
						
						echo "
							<div class='singleProduct'>
								<h3>$ProductName</h3>
									<p>$Description</p>
									<p>Quantity: 1</p>
									<p>Price: $PriceToDisplay$</p>
							</div>
						";
					}
					echo "
						<div class='productSectionHeaders'>
							<h3>Total Price: $totalPrice$</h3>
						</div>
						<div id='EmptyCart'>
							<form action='cart.php' method='post'>
								<input type='submit' name='emptyCart' value='Empty Cart' class='btn btn-primary'>
							</form>
						</div>
						";
				}
				else
				{
					//display that cart is empty
					echo "
						<h3 class='alert alert-info'>
							Your Cart is Empty. Visit Home page to add products to your cart.
						</h3>
					";
				}
			}
		?>
	</div>
</body>
</html>
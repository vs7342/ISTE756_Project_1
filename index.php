<?php
session_start();
if(!isset($_SESSION["uid"]) || !isset($_SESSION["role"]))
{
	$Msg="<strong>Warning!!</strong> Login is required for adding chocolates to your Cart!";
	$MsgType = "alert alert-warning";
}

require_once("LIB_project1.php");

function renderProductDetails($onSale, $pageNum=null)
{
	$products = new BLProduct();
	$products = $products->fetch($onSale);
	$allProductsString = "";
	if($pageNum==null)
	{
		$start = 0;
		$end = count($products);
	}
	else
	{
		$start = ($pageNum-1)*5;
		$end = min($pageNum*5,count($products));
	}
	
	for($i=$start; $i<$end; $i++)
	{
		$singleProduct = $products[$i];
		$Pid = $singleProduct->getPid();
		$ProductName = $singleProduct->getProductName();
		$Description = $singleProduct->getDescription();
		$SalePrice = $singleProduct->getSalePrice();
		$Price = $singleProduct->getPrice();
		$Quantity = $singleProduct->getQuantity();
		$ImageName = $singleProduct->getImageName();
		
		if($ImageName=="NULL")
			$ImageName = "default.png";
		
		if($onSale)
		{
			$SalePartDesc = "<p>Sale Price: $SalePrice$ (Regularly: $Price$). Only $Quantity left !!!</p>";
		}
		else
		{
			$SalePartDesc = "<p>Price: $Price$. Only $Quantity left !!!</p>";
		}
		
		$addToCartDisabled = "";
		if($Quantity==0 || !isset($_SESSION["uid"]) || !isset($_SESSION["role"]))
		{
			$addToCartDisabled = "disabled='disabled'";
		}
		
		$allProductsString .= "
		<div class='singleProduct'>
			<h3>$ProductName</h3>
			<img src='images/$ImageName' alt='$ProductName'>
			<div class='singleProductData'>
				<p>$Description</p>
				$SalePartDesc
				<form action='index.php' method='post'>
					<input type='hidden' name='product' value='$Pid'>
					<input type='submit' name='addCart' value='Add to Cart!!' class='btn btn-primary' $addToCartDisabled>
				</form>
			</div>
		</div>
		";
	}
	return $allProductsString;
}

if(isset($_POST["addCart"]))
{
	$uid = $_SESSION["uid"];
	$pid = $_POST["product"];
	$cart = new BLCart($uid,$pid);
	if($cart->post())
	{
		$Msg="Product has been added to your cart";
		$MsgType = "alert alert-success";
		
		$product = new BLProduct($pid);
		$product->deleteQty();
	}
}

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Chocolate Factory - Home</title>
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
	
	<div id="sales" class="productSection">
		<h1 class="productSectionHeaders">Chocolates on Sale</h1>
		<?php
			echo renderProductDetails(true);
		?>
	</div>
	
	<div id="catalog" class="productSection">
		<h1 class="productSectionHeaders">Catalog</h1>
		<?php
			if(!isset($_POST["submitPage"]))
				$pageNum = 1;
			else
				$pageNum = $_POST["submitPage"];
			echo renderProductDetails(false, $pageNum);
		?>
		<div id="pages">
			<form action='index.php' method='post'>
				<?php
					$p = new BLProduct();
					$p = $p->fetch(false);
					$totalPages = ceil(count($p)/5);
					for($i=1;$i<=$totalPages;$i++)
					{
						//First and Prev Page
						if($pageNum!=1&&$i==1)
						{
							echo "<button type='submit' name='submitPage' value='1' class='btn btn-primary'>&lt;&lt;First</button> ";
							$prev = $pageNum-1;
							echo "<button type='submit' name='submitPage' value='$prev' class='btn btn-primary'>&lt;Prev</button> ";
						}
						
						$activeString = "";
						if($pageNum==$i)
							$activeString = "active";
						echo "<input type='submit' name='submitPage' value='$i' class='btn btn-primary $activeString'> ";
						
						//Next and Last Page
						if($pageNum!=$totalPages&&$i==$totalPages)
						{
							$next = $pageNum+1;
							echo "<button type='submit' name='submitPage' value='$next' class='btn btn-primary'>Next&gt;</button> ";
							echo "<button type='submit' name='submitPage' value='$totalPages' class='btn btn-primary'>Last&gt;&gt;</button> ";
						}
					}
				?>
			</form>
		</div>
	</div>
</body>
</html>
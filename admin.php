<?php
session_start();
if(!isset($_SESSION["uid"]) || !isset($_SESSION["role"]) || strtolower($_SESSION["role"])!="admin")
{
	header("location: login.php");
	die();
}

require_once("LIB_project1.php");

//Function to reset the form variables
function resetFormFieldVars()
{
	$ProductName = "";
	$Description = "";
	$Price = "";
	$Quantity = "";
	$SalePrice = 0.0;
}

//Input Field for form string - Used to render input fields of AddProduct and EditProduct form
function fetchForm($ProductName,$Description,$Price,$Quantity,$SalePrice)
{
	$formInput = '
			<div class="form-group">
				Name
				<input type="text" class="form-control" name="ProductName" placeholder="Name of Product" style="width: 200px" value="'.$ProductName.'">
			</div>
			<div class="form-group">
				Description
				<textarea class="form-control" name="Description" placeholder="Description of Product" style="width: 400px" rows="2" cols="40">'.$Description.'</textarea>
			</div>
			<div class="form-group">
				Price
				<input type="text" class="form-control" name="Price" placeholder="Price of the Product" style="width: 200px" value="'.$Price.'">
			</div>
			<div class="form-group">
				Available Quantity
				<input type="text" class="form-control" name="Quantity" placeholder="Quantity of Product" style="width: 200px" value="'.$Quantity.'">
			</div>
			<div class="form-group">
				Sale Price
				<input type="text" class="form-control" name="SalePrice" placeholder="Sale Price of Product" style="width: 200px" value="'.$SalePrice.'">
			</div>
			<div class="form-group">
				Product Image
				<input type="file" name="ImageName">
			</div>
	';
	return $formInput;
}

//Sanitize and Validate form data and then set the form variables
//Else Set the form variables to "" - So that they can be used throughout the forms
if($_POST["SubmitAddProduct"]||$_POST["SubmitEditProduct"])
{
	$ProductName = $_POST["ProductName"];
	$Description = $_POST["Description"];
	$Price = $_POST["Price"];
	$Quantity = $_POST["Quantity"];
	$SalePrice = $_POST["SalePrice"];
	
	//Sanitize
	$ProductName = sanitizeInput($ProductName);
	$Description = sanitizeInput($Description);
	$Price = sanitizeInput($Price);
	$Quantity = sanitizeInput($Quantity);
	$SalePrice = sanitizeInput($SalePrice);
	
	//Validatation
	$validSuccess = true;
	$Msg="";
	
	//Process Form Text Inputs
	if(
		$ProductName==""||
		!validateNameDescription($ProductName)||
		strlen($ProductName)>40
	)
	{
		$validSuccess = false;
		$Msg.= "Please enter alphanumeric Product Name &lt; 40 Characters. ";
	}
	if(
		$Description==""||
		!validateNameDescription($Description)||
		strlen($Description)>80
	)
	{
		$validSuccess = false;
		$Msg.= "Please enter alphanumeric Product Description &lt; 80 Characters. ";
	}
	if(!validatePrice($Price))
	{
		$validSuccess = false;
		$Msg.= "Please enter a valid Price - With Max 2 decimal places. ";
	}
	if(!validateQuantity($Quantity))
	{
		$validSuccess = false;
		$Msg.= "Please enter a valid Quantity. ";
	}
	if(!validatePrice($SalePrice))
	{
		$validSuccess = false;
		$Msg.= "Please enter a valid Sale Price - With Max 2 decimal places. ";
	}
	
	
	//Process Image Code -
	//1. Check for presence of image
	//2. Check validity of image (Size and Type)
	if(!empty($_FILES['ImageName']['name']))
	{
		if($_FILES['ImageName']['error']==0)
		{
			//do the processing
			$ImageName = basename($_FILES['ImageName']['name']);
			$extension = substr($ImageName, strpos($ImageName,'.')+1);
			if(
				$extension != "png" || 
				$_FILES['ImageName']['type']!='image/png' ||
				$_FILES['ImageName']['size']>1000000
			)
			{
				$validSuccess = false;
				$Msg.= "Image File must be of type - png and size &lt; 1 MB. ";
			}
		}
		else
		{
			$validSuccess = false;
			$Msg.= "Some error occured while uploading the image. Please Try again or contact Admin. ";
		}
	}
	else 
		if($_POST["SubmitAddProduct"]) //if empty and adding a product
		{
			$validSuccess = false;
			$Msg.="You need to upload an image while inserting a new Product. ";
		}
	
	
	
	if(!$validSuccess)
	{
		$MsgType = "alert alert-danger";
	}
}
else
{
	resetFormFieldVars();
}

//File Renaming and Moving Code
//Creates a random name for the png file, moves it to images folder and returns $FileName
function processFile()
{
	srand(time());
	$ImageName = rand();
	$ImageName .=".png";
	$filePath = "./images/";
	$destination = $filePath.$ImageName;
	if(move_uploaded_file($_FILES['ImageName']['tmp_name'],$destination))
	{
		chmod($destination,0644);
		return $ImageName;
	}
	else
		return null;
}

//Add Product Code
if($validSuccess && isset($_POST["SubmitAddProduct"]))
{
	//Processing Image Input and placing it in images folder and setting the $ImageName to the filename.ext
	$ImageName = processFile();
	if($ImageName!=null)
	{
		$product = new BLProduct(null,$ProductName,$Description,$Price,$Quantity,$ImageName,$SalePrice);
		$success = $product->post();
		if($success===true)
		{
			$Msg = "Product Added Successfully";
			$MsgType = "alert alert-success";
			$ProductName = "";
			$Description = "";
			$Price = "";
			$Quantity = "";
			$SalePrice = "0";
		}
		else
		{
			$Msg = $success;
			$MsgType = "alert alert-danger";
		}
	}
	else
	{
		$Msg = "Error Encountered. Could Not add the product. File Image error";
		$MsgType = "alert alert-danger";
	}
}

//Edit Product Code
if(isset($_POST["SubmitEditProduct"]))
{
	if($validSuccess)
	{
		$pid = $_POST["singleprod"];
		
		//Processing Image Input and placing it in images folder and setting the $ImageName to the filename.ext - In DB, ImageName = Overwrite if already exists - In Folder - Delete Previous image if exists
		if(isset($ImageName))
		{
			$ImageName = processFile();
			
			//Deleting the previous image only when user uploads an image while editing the product
			$prod = new BLProduct($pid);
			$prod = $prod->fetch();
			$prod = $prod[0];
			$prevImage = $prod->getImageName();
			unlink("./images/$prevImage");	
		}
		else
		{
			$ImageName=null;
		}
		
		//Actual update of the product
		$product = new BLProduct($pid,$ProductName,$Description,$Price,$Quantity,$ImageName,$SalePrice);
		$success = $product->put();
		if($success===1)
		{
			$Msg = "Product Updated Successfully";
			$MsgType = "alert alert-success";
		}
		else if($success===0) //when 0 rows were updated
		{
			$Msg = "No changes were made";
			$MsgType = "alert alert-danger";
			//For re-rendering the EditProduct form
			$_POST["SubmitSelectProduct"] = true;
			$_POST["SingleProduct"] = $pid;
		}
		else
		{
			$Msg = $success;
			$MsgType = "alert alert-danger";
			//For re-rendering the EditProduct form
			$_POST["SubmitSelectProduct"] = true;
			$_POST["SingleProduct"] = $pid;
			
		}
	}
	else
	{
		//Setting these variables so that edit form can be rendered again
		$_POST["SubmitSelectProduct"] = true;
		$_POST["SingleProduct"] = $_POST["singleprod"];
	}
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Chocolate Factory - Admin</title>
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
	
	<h3 class="adminHeaders">Edit a Product</h3>
	<div id="selectProduct">
		<form name="addProduct" action="admin.php" method="post" class="form-inline">
			<label>Select Product to edit:</label>
			<select name="SingleProduct" class="form-control">
				<?php
					$products = new BLProduct();
					$products = $products->fetch();
					for($i=0;$i<count($products);$i++)
					{
						echo "<option value='{$products[$i]->getPid()}'>{$products[$i]->getProductName()}-{$products[$i]->getDescription()}</option>\n";
					}
				?>
			</select>
			<input type="submit" name="SubmitSelectProduct" value="Select Product" class="btn btn-primary">
		</form>
	</div>
	
	<div id="editProduct" class="container-fluid">
		<?php
			if(isset($_POST["SubmitSelectProduct"]))
			{
				if(isset($_POST["SingleProduct"]))
				{
					$pid = $_POST["SingleProduct"];
					$product = new BLProduct($pid);
					$product = $product->fetch();
					$product = $product[0];
					
					//Setting the form variables using $product object
					$ProductName = $product->getProductName();
					$Description = $product->getDescription();
					$Price = $product->getPrice();
					$Quantity = $product->getQuantity();
					$SalePrice = $product->getSalePrice();
					
					//Starting the EditProduct form
					echo '<form name="EditProduct" action="admin.php" method="post" enctype="multipart/form-data" class="form-horizontal">';
					
					//EditProduct form import
					echo fetchForm($ProductName,$Description,$Price,$Quantity,$SalePrice);
					
					//Setting a hidden field to ID of the product so that it can be used during updates
					echo '<input type="hidden" name="singleprod" value="'.$pid.'" />';
					
					//EditProduct form submit buttons
					echo '<div class="form-group">
							<input type="submit" name="SubmitEditProduct" value="Update" class="btn btn-primary">
							<input type="reset" value="Reset Form" class="btn btn-primary">
						</div>
					</form>';
				}
				else
				{
					//Some Problem - Mostly this wont happen - Add some code to show error.
				}
			}
		?>
	</div>
	<hr/>
	
	<h3 class="adminHeaders">Add a Product</h3>
	<?php
	//Resetting the fields if EditProduct has been executed
	if(isset($_POST["SubmitSelectProduct"]) || isset($_POST["SubmitEditProduct"]) || !isset($_POST["SubmitAddProduct"]))
	{
		//Tried calling the function resetFormFieldVars() but the variables were not resetting
		//However when I put the SAME code in here, it does resets!
		//Dont know what is happening.
		$ProductName = "";
		$Description = "";
		$Price = "";
		$Quantity = "";
		$SalePrice = "0";
	}
	?>
	<div id="addProductForm" class="container-fluid">
		<form name="addProduct" action="admin.php" method="post" enctype="multipart/form-data" class="form-horizontal">
			<?php echo fetchForm($ProductName,$Description,$Price,$Quantity,$SalePrice);?>
			<div class="form-group">
				<input type="submit" name="SubmitAddProduct" value="Add Product" class="btn btn-primary">
				<input type="reset" value="Reset Form" class="btn btn-primary">
			</div>
		</form>
	</div>

</body>
</html>
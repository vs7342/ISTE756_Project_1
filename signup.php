<?php
require_once("LIB_project1.php");
if(isset($_POST["submitSignup"]))
{
	$FirstName = $_POST["FirstName"];
	$LastName = $_POST["LastName"];
	$Email = $_POST["Email"];
	$Password = $_POST["Password"];
	$RePassword = $_POST["RePassword"];
	
	//Sanitize
	$FirstName = sanitizeInput($FirstName);
	$LastName = sanitizeInput($LastName);
	$Email = sanitizeInput($Email);
	$Password = sanitizeInput($Password);
	$RePassword = sanitizeInput($RePassword);
	
	//Validation
	$validSuccess = true;
	$Msg = "";
	$MsgType="";
	
	if($FirstName==""||!validateTextInput($FirstName)||strlen($FirstName)>40)
	{
		$validSuccess = false;
		$Msg.="Please enter a valid First Name with text/spaces only with &lt; 40 Characters. ";
	}
	if($LastName==""||!validateTextInput($LastName)||strlen($LastName)>40)
	{
		$validSuccess = false;
		$Msg.="Please enter a valid Last Name with text/spaces only with &lt; 40 Characters. ";
	}
	
	if(!validateEmail($Email))
	{
		$validSuccess = false;
		$Msg.="Please enter a valid Email ID. ";
	}
	if($Password==""||!validatePassword($Password))
	{
		$validSuccess = false;
		$Msg.="Please enter a valid Password - Only Alphanumeric characters allowed. ";
	}
	if($RePassword==""||!validatePassword($RePassword))
	{
		$validSuccess = false;
		$Msg.="Please enter a valid Password in Re-Enter Password field- Only Alphanumeric characters allowed. ";
	}
	if($Password!==$RePassword)
	{
		$validSuccess = false;
		$Msg.="Passwords do not match.";
	}
	
	if(!$validSuccess)
	{
		$MsgType = "alert alert-danger";
	}
}
else
{
	$FirstName = "";
	$LastName = "";
	$Email = "";
	$Password = "";
	$RePassword = "";
}

if(isset($_POST["submitSignup"]) && $validSuccess)
{
	$user = new BLUser();
	$user->setEmail($Email);
	$exists = $user->checkEmailExists();
	if($exists===false)
	{
		$user->setFirstName($FirstName);
		$user->setLastName($LastName);
		$user->setPassword($Password);
		$user->setRole("General");
		
		if($user->signup())
		{
			$Msg = "You are now registered. You may now login!";
			$MsgType = "alert alert-success";
			$FirstName = "";
			$LastName = "";
			$Email = "";
			$Password = "";
			$RePassword = "";
		}
		else
		{
			$Msg = "Something went wrong. Please contact admin if problem persists.";
			$MsgType = "alert alert-danger";
		}
	}
	else
	{
		$Msg = "Email already exists. Please try with a different Email ID.";
		$MsgType = "alert alert-warning";
	}
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Chocolate Factory - Signup</title>
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
	
	<div id="signup" class="productSection">
		<h1 class="productSectionHeaders">Sign Up</h1>
		<form action="signup.php" method="post">
			<div class="form-group">
				First Name
				<input type="text" class="form-control" name="FirstName" placeholder="Enter your First Name" style="width: 200px" value="<?=$FirstName?>">
			</div>
			<div class="form-group">
				Last Name
				<input type="text" class="form-control" name="LastName" placeholder="Enter your Last Name" style="width: 200px" value="<?=$LastName?>">
			</div>
			<div class="form-group">
				Email
				<input type="email" class="form-control" name="Email" placeholder="Enter your Email" style="width: 200px" value="<?=$Email?>">
			</div>
			<div class="form-group">
				Password
				<input type="password" class="form-control" name="Password" placeholder="Enter your Password" style="width: 200px" value="<?=$Password?>">
			</div>
			<div class="form-group">
				Re-Enter Password
				<input type="password" class="form-control" name="RePassword" placeholder="Re-Enter your Password" style="width: 200px" value="<?=$RePassword?>">
			</div>
			<div class="form-group">
				<input type="submit" name="submitSignup" value="Register!" class="btn btn-primary">
				<input type="reset" value="Reset Form" class="btn btn-primary">
			</div>
		</form>
	</div>
	
</body>
</html>
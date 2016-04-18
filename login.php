<?php
session_start();
require_once("LIB_project1.php");

//Check if user session is already set. If yes redirect them to index page
if(isset($_SESSION["uid"]) && isset($_SESSION["role"]))
{
	header("location: index.php");
	die();
}

//Sanitize and Validate inputs
if(isset($_POST["submitLogin"]))
{
	$Email = $_POST["Email"];
	$Password = $_POST["Password"];
	
	//Sanitize
	$Email = sanitizeInput($Email);
	$Password = sanitizeInput($Password);
	
	//Validation
	$validSuccess = true;
	$Msg = "";
	$MsgType="";
	
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
	
	if(!$validSuccess)
	{
		$MsgType = "alert alert-danger";
	}
}
else
{
	$Email = "";
	$Password = "";
}

if(isset($_POST["submitLogin"]) && $validSuccess)
{
	$user = new BLUser();
	$user->setEmail($Email);
	$user->setPassword($Password);
	
	$user = $user->login();
	if($user===false)
	{
		$Msg = "Invalid Email or Password";
		$MsgType = "alert alert-danger";
	}
	else
	{
		$user = $user[0];
	
		$uid = $user->getUid();
		$role = $user->getRole();
		
		$_SESSION["uid"] = $uid;
		$_SESSION["role"] = $role;
		
		header("location: index.php");
	}
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Chocolate Factory - Login</title>
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
	
	<div id="login" class="productSection">
		<h1 class="productSectionHeaders">User Login</h1>
		<form action="login.php" method="post">
			<div class="form-group">
				Email
				<input type="email" class="form-control" name="Email" placeholder="Enter your Email" style="width: 200px" value="<?=$Email?>">
			</div>
			<div class="form-group">
				Password
				<input type="password" class="form-control" name="Password" placeholder="Enter your Password" style="width: 200px" value="<?=$Password?>">
			</div>
			<div class="form-group">
				<input type="submit" name="submitLogin" value="Login" class="btn btn-primary" style="width: 95px">
				<input type="reset" value="Reset Form" class="btn btn-primary" style="width: 100px">
			</div>
		</form>
		<div id="signuplink">
			<a href="signup.php" class="btn btn-primary" role="button" style="width: 200px">Not a member? Signup Now!</a>
		</div>
	</div>
</body>
</html>

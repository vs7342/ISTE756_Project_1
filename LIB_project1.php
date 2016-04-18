<?php

/*This file is required in every single script which has HTML output/processing*/

//Magic method to include Classes automatically from the 'classes' folder
function __autoload($classname)
{
	require_once("./classes/$classname.class.php");
}

//This function returns the navigation bar based on the type of user and/or logged in or not.
function getNavigation()
{
	session_start();
	
	$admin="";
	$login="";
	$logout="";
	
	if(isset($_SESSION["uid"])||isset($_SESSION["role"]))
	{
		$logout = "
			<ul class='nav navbar-nav navbar-right'>
			  <li><a href='logout.php'>Logout</a></li>
			</ul>
		";
		if(strtolower($_SESSION["role"])=="admin")
		{
			$admin = '<li><a href="admin.php">Admin</a></li>';
		}
	}
	else
	{
		$login = '
			<ul class="nav navbar-nav navbar-right">
			  <li><a href="login.php">Login</a></li>
			</ul>
		';
	}
	$nav ="
		<nav class='navbar navbar-inverse'>
		  <div class='container-fluid'>
			<ul class='nav navbar-nav navbar-left'>
			  <li><a href='index.php'>Home</a></li>
			  <li><a href='cart.php'>Cart</a></li>
			  $admin
			</ul>
			$login
			$logout
		  </div>
		</nav>";
	return $nav;
}

//returns the banner
function getHeader()
{
	$header = '
		<img src="icon.png" alt="someLogo">
		<h1>The Chocolate Factory</h1>
	';
	return $header;
}

//Since I have used bootstrap extensively, this part of code was needed in all the files
function getExternalSheetsInfo()
{
	$ext = 	'
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" type="text/css" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
		<script type="text/js" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
		<script type="text/js" src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
		<link rel="stylesheet" type="text/css" href="style.css">';
	return $ext;
}

//Sanitize an input
function sanitizeInput($input)
{
	$input = trim($input);
	$input = stripslashes($input);
	$input = strip_tags($input);
	$input = htmlentities($input);
	return $input;
}

//All Validation Functions
function validateQuantity($input)
{
	return preg_match("/^[0-9]+$/",$input);
}

function validatePrice($input)
{
	return preg_match("/^\d+(\.\d{1,2})?$/",$input);
}

function validateNameDescription($input)
{
	return preg_match("/^[A-Za-z0-9 ]+$/",$input);
}

function validateEmail($input)
{
	//This regex is used from the class example - day5 - validations.php
	return preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/",$input);
}

function validatePassword($input)
{
	return validateNameDescription($input); //Only alpha numeric had to be checked
}

function validateTextInput($input)
{
	return preg_match("/^[A-Za-z ]+$/",$input);
}

?>
<?php

//resume the session;
session_start();

//unsetting (both using unset() and session_unset())and destroying the session
unset($_SESSION["uid"]);
unset($_SESSION["role"]);
session_unset();
session_destroy();

//destroying the session cookie
unset($_COOKIE[session_name()]);
setcookie(session_name(),"",1,"/");

//redirecting to home page
header("location: index.php");

?>
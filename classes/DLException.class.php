<?php

class DLException extends Exception
{
	function __construct($logInfo)
	{
		$this->logExc($logInfo);
	}
	
	//function to log the data layer exceptions caught on to the log.txt file
	function logExc($logInfo)
	{
		$logFile = fopen("log.txt","a+");
		
		foreach($logInfo as $k=>$v)
		{
			fwrite($logFile,$k." - ".$v."\n");
		}
		fwrite($logFile,"=========================================================================\n");
		fclose($logFile);
	}
}

?>
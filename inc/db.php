<?php

date_default_timezone_set('Europe/Oslo');

include('inc/settings.php');

try
{
	$con = new PDO('mysql:host=' . $config['HOST'] . ';dbname=' . $config['DB'] . ';charset=UTF8', $config['USER'], $config['PASS']);
	//$con->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
}
catch(PDOException $e)
{
	//die($e->getMessage());
	die('There has occured an error in our system, please notify an administrator!');
}

?>
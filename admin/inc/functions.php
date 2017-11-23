<?php

function isAdmin()
{
	if(isset($_SESSION['username']) && isset($_SESSION['password']))
	{
		global $con;

		$username = $_SESSION['username'];

		$data = $con->prepare('SELECT COUNT(*) FROM users WHERE username = :username AND access >= 2');
		$data->execute(array(
			':username' => $username
		));

		if($data->fetchColumn() == 1)
		{
			
		}
		else
		{
			die('Access Denied!');
		}
	}
	else
	{
		die('Access Denied!');
	}
}





































?>
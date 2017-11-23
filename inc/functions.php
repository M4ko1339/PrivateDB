<?php

function LogData($page, $data)
{
	global $con;

	$username   = $_SESSION['username'];
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$ip_address = $_SERVER['REMOTE_ADDR'];

	$query = $con->prepare('INSERT INTO logs (username, page, data, user_agent, ip, time) VALUES(:username, :page, :data, :user_agent, :ip, :time)');
	$query->execute(array(
		':username'   => $username,
		':page'       => $page,
		':data'       => $data,
		':user_agent' => $user_agent,
		':ip'         => $ip_address,
		':time'       => time()
	));
}

function GrabAddons($expansion)
{
	global $con;

	$page    = isset($_GET['page']) ? (int)$_GET['page'] : 1;
	$perPage = ADDON_RESULTS;

	$start = ($page > 1) ? ($page * $perPage) - $perPage : 0;

	$type = array(
		1 => 'vanilla',
		2 => 'tbc',
		3 => 'wotlk'
	);

	$total = $con->prepare('SELECT COUNT(*) FROM addons WHERE expansion = :expansion AND status = 2');
	$total->execute(array(
		':expansion' => (int)$expansion
	));

	$total = $total->fetchColumn();

	$pages = ceil($total / $perPage);

	$data = $con->prepare('SELECT * FROM addons WHERE expansion = :expansion AND status = 2 ORDER BY id DESC LIMIT ' . $start . ', ' . $perPage);
	$data->execute(array(
		':expansion' => (int)$expansion
	));

	echo '<table class="addons-list">
			<th>Name</th>
			<th>Version</th>
			<th>Updated</th>
			<th>Downloads</th>
			<th></th>';

	while($result = $data->fetchAll(PDO::FETCH_ASSOC))
	{
		foreach($result as $row)
		{
			echo '<tr>
					<td><a href="view.php?id=' . $row['id'] . '">' . $row['addon_name'] . '</a></td>
					<td>' . $row['addon_version'] . '</td>
					<td>' . date('j. F, Y', $row['updated']) . '</td>
					<td>' . $row['downloads'] . '</td>
					<td><a href="download.php?id=' . $row['id'] . '" class="small button">DOWNLOAD</a></td>
				</tr>';
		}
	}

	echo '</table></div></div>';

	echo '<div class="navigation column small-12"><ul class="nav-box">';

		echo '<li><a href="?type=' . $type[$expansion] . '&page=1"><<</a></li>';

		$min = max($page - 2, 1);
		$max = min($page + 2, $pages);

		for($x = $min; $x <= $max; $x++)
		{
			if(@$page == $x)
			{
				echo '<li><a href="?type=' . $type[$expansion] . '&page=' . $x . '" class="current-nav">' . $x . '</a></li>';
			}
			elseif(@!isset($page))
			{
				echo '<li><a href="?type=' . $type[$expansion] . '&page=' . $x . '" class="current-nav">' . $x . '</a></li>';
			}
			else
			{
				echo '<li><a href="?type=' . $type[$expansion] . '&page=' . $x . '">' . $x . '</a></li>';
			}
		}

		echo '<li><a href="?type=' . $type[$expansion] . '&page=' . $pages . '">>></a></li>';

	echo '</ul></div>';
}

function SearchAddon($expansion)
{
	if(isset($_GET['name']))
	{
		global $con;

		$name = strtolower($_GET['name']);

		$type = array(
			1 => 'vanilla',
			2 => 'tbc',
			3 => 'wotlk'
		);

		$page    = isset($_GET['page']) ? (int)$_GET['page'] : 1;
		$perPage = ADDON_SEARCH_RESULTS;

		$start = ($page > 1) ? ($page * $perPage) - $perPage : 0;

		$data = $con->prepare('SELECT * FROM addons WHERE addon_name LIKE ? AND expansion = ? AND status = 2 ORDER BY id ASC LIMIT ' . $start . ', ' . $perPage);
		$data->execute(array(
			"%$name%",
			(int)$expansion
		));

		$data2 = $con->prepare('SELECT * FROM addons WHERE addon_name LIKE ? AND expansion = ? AND status = 2');
		$data2->execute(array(
			"%$name%",
			(int)$expansion
		));

		$rows = $data2->rowCount();
		
		$totalq = $con->prepare('SELECT * FROM addons WHERE addon_name LIKE ? AND expansion = ? AND status = 2');
		$totalq->execute(array(
			"%$name%",
			(int)$expansion
		));

		$total = $totalq->rowCount();

		$pages = ceil($total / $perPage);

		echo '<table class="addons-list">
				<th>Name</th>
				<th>Version</th>
				<th>Updated</th>
				<th>Downloads</th>
				<th></th>';

		while($result = $data->fetchAll(PDO::FETCH_ASSOC))
		{
			foreach($result as $row)
			{
				echo '<tr>
						<td><a href="view.php?id=' . $row['id'] . '">' . $row['addon_name'] . '</a></td>
						<td>' . $row['addon_version'] . '</td>
						<td>' . date('j. F, Y', $row['updated']) . '</td>
						<td>' . $row['downloads'] . '</td>
						<td><a href="download.php?id=' . $row['id'] . '" class="small button">DOWNLOAD</a></td>
					</tr>';
			}
		}

		echo '</table>
				</div>
			</div>';

		echo '<div class="navigation column small-12"><ul class="nav-box">';

			echo '<li><a href="?type=' . $type[$expansion] . '&name=' . $name . '&page=1"><<</a></li>';

			$min = max($page - 2, 1);
			$max = min($page + 2, $pages);

			for($x = $min; $x <= $max; $x++)
			{
				if(@$page == $x)
				{
					echo '<li><a href="?type=' . $type[$expansion] . '&name=' . $name . '&page=' . $x . '" class="current-nav">' . $x . '</a></li>';
				}
				elseif(@!isset($page))
				{
					echo '<li><a href="?type=' . $type[$expansion] . '&name=' . $name . '&page=' . $x . '" class="current-nav">' . $x . '</a></li>';
				}
				else
				{
					echo '<li><a href="?type=' . $type[$expansion] . '&name=' . $name . '&page=' . $x . '">' . $x . '</a></li>';
				}
			}

			echo '<li><a href="?type=' . $type[$expansion] . '&name=' . $name . '&page=' . $pages . '">>></a></li>';

		echo '</ul></div>';
	}
}

function GrabMostDownloaded($expansion)
{
	global $con;

	$data = $con->prepare('SELECT * FROM addons WHERE expansion = :expansion AND status = 2 ORDER BY downloads DESC LIMIT 10');
	$data->execute(array(
		':expansion' => (int)$expansion
	));

	while($result = $data->fetchAll(PDO::FETCH_ASSOC))
	{
		foreach($result as $row)
		{
			echo '<tr>
					<td><a href="view.php?id=' . $row['id'] . '">' . $row['addon_name'] . '</a></td>
					<td>' . $row['downloads'] . '</td>
				</tr>';
		}
	}
}

function GrabNews()
{
	global $con;

	$page    = isset($_GET['page']) ? (int)$_GET['page'] : 1;
	$perPage = NEWS_RESULTS;

	$start = ($page > 1) ? ($page * $perPage) - $perPage : 0;

	$total = $con->prepare('SELECT COUNT(*) FROM news');
	$total->execute();

	$total = $total->fetchColumn();

	$pages = ceil($total / $perPage);

	$data = $con->prepare('SELECT * FROM news ORDER BY id DESC LIMIT ' . $start . ', ' . $perPage);
	$data->execute();

	while($result = $data->fetchAll(PDO::FETCH_ASSOC))
	{
		foreach($result as $row)
		{
			echo '<div class="content-box column small-12">
					<div class="content-box-header column small-12">
						<div class="title-text">
							' . $row['news_title'] . '
						</div>

						<div class="title-date">
							' . date('j. F, Y', $row['post_date']) . '
						</div>
					</div>

					<div class="content-box-content column small-12">
						<div class="content-text">
							' . $row['news_content'] . '
						</div>
					</div>
				</div>';
		}
	}

	echo '<div class="navigation column small-12"><ul class="nav-box">';

		echo '<li><a href="?page=1"><<</a></li>';

		$min = max($page - 2, 1);
		$max = min($page + 2, $pages);

		for($x = $min; $x <= $max; $x++)
		{
			if(@$page == $x)
			{
				echo '<li><a href="?page=' . $x . '" class="current-nav">' . $x . '</a></li>';
			}
			elseif(@!isset($page))
			{
				echo '<li><a href="?page=' . $x . '" class="current-nav">' . $x . '</a></li>';
			}
			else
			{
				echo '<li><a href="?page=' . $x . '">' . $x . '</a></li>';
			}
		}

		echo '<li><a href="?page=' . $pages . '">>></a></li>';

	echo '</ul></div>';
}

function UploadAddon()
{
	global $con;
	
	$username = $_SESSION['username'];

	$data = $con->prepare('SELECT COUNT(*) FROM users WHERE username = :username AND access >= 1');
	$data->execute(array(
		':username' => $username
	));

	if($data->fetchColumn() == 1)
	{
		if(isset($_POST['upload']))
		{
			if(!empty($_POST['name']))
			{
				$max_size = 20971520;

				$name        = ucfirst($_POST['name']);
				$description = $_POST['description'];
				$version     = (int)$_POST['num1'] . '.' . (int)$_POST['num2'] . '.' . (int)$_POST['num3'];
				$uploader    = $_SESSION['username'];
				$category    = $_POST['category'];
				$downloads   = 0;
				$expansion   = $_POST['expansion'];
				$file        = $_FILES['file'];

				$file_name  = $file['name'];
				$file_tmp   = $file['tmp_name'];
				$file_size  = $file['size'];
				$file_error = $file['error'];

				$file_ext  = explode('.', $file_name);
				$file_ext  = strtolower(end($file_ext));

				$whitelist = array(
					'zip',
					'rar',
					'7z'
				);

				if(in_array($file_ext, $whitelist))
				{
					if($file_error === 0)
					{
						if($file_size <= $max_size)
						{
							$folder = array(
								1 => 'addons/vanilla/',
								2 => 'addons/tbc/',
								3 => 'addons/wotlk/'
							);
							
							$file_id = str_shuffle(substr('ABCDEF0123456789', 0, 10));

							$file_name_new    = uniqid() . '-' . $file_name;
							$file_destination = $folder[$expansion] . $file_name_new;

							if(move_uploaded_file($file_tmp, $file_destination))
							{
								$data = $con->prepare('INSERT INTO addons (addon_name, addon_version, addon_uploader, addon_description, status, downloads, category, updated, uploaded, expansion, file_id) 
									VALUES(:name, :version, :uploader, :description, :status, :downloads, :category, :updated, :uploaded, :expansion, :file_id)');
								$data->execute(array(
									':name'          => $name,
									':version'       => $version,
									':uploader'      => $uploader,
									':description'   => nl2br($description),
									':status'        => 0,
									':downloads'     => $downloads,
									':category'      => $category,
									':updated'       => time(),
									':uploaded'      => time(),
									':expansion'     => $expansion,
									':file_id'       => $file_id
								));

								$data2 = $con->prepare('INSERT INTO files (file_id, file_name, file_tmp, file_size, file_url, added) VALUES(:file_id, :file_name, :file_tmp, :file_size, :file_url, :added)');
								$data2->execute(array(
									':file_id'   => $file_id,
									':file_name' => $file_name,
									':file_tmp'  => $file_tmp,
									':file_size' => $file_size,
									':file_url'  => $file_destination,
									':added'     => time()
								));

								LogData('Upload Addon', 'Uploaded addon: ' . $file_id);

								echo '<div class="callout success">Uploaded addon!</div>';
							} 
						}
						else
						{
							echo '<div class="callout alert">File is too large!</div>';
						}
					}
					else
					{
						echo '<div class="callout alert">Something went wrong! Error Code: ' . $file_error . '</div>';
					}
				}
				else
				{
					echo '<div class="callout alert">File is not a zip file!</div>';
				}
			}
			else
			{
				echo '<div class="callout alert">Please fill in a name!</div>';
			}
		}
	}
}

function Download()
{
	if(isset($_GET['id']))
	{
		if(!empty($_GET['id']))
		{
			global $con;

			$id  = (int)$_GET['id'];

			$data = $con->prepare('SELECT COUNT(*) FROM addons WHERE id = :id');
			$data->execute(array(
				':id' => $id
			));

			if($data->fetchColumn() == 1)
			{
				$data = $con->prepare('SELECT * FROM addons WHERE id = :id');
				$data->execute(array(
					':id' => $id
				));

				$result = $data->fetchAll(PDO::FETCH_ASSOC);

				foreach($result as $row)
				{
					$data = $con->prepare('SELECT * FROM files WHERE file_id = :file_id');
					$data->execute(array(
						':file_id' => $row['file_id']
					));

					$result = $data->fetchAll(PDO::FETCH_ASSOC);

					foreach($result as $file)
					{
						$num = $row['downloads'];

						$data = $con->prepare('UPDATE addons SET downloads = :downloads WHERE id = :id');
						$data->execute(array(
							':downloads' => $num+1,
							':id'        => $id
						));

						$data = $con->prepare('INSERT INTO downloads (file_id, ip, user_agent, time) VALUES(:file_id, :ip, :user_agent, :time)');
						$data->execute(array(
							':file_id'    => $file['file_id'],
							':ip'         => $_SERVER['REMOTE_ADDR'],
							':user_agent' => $_SERVER['HTTP_USER_AGENT'],
							':time'       => time()
						));

						echo '<script>setTimeout(function () {
							       window.location.href = "' . SITE_URL . $file['file_url'] . '";
							    }, 3000);</script>';
					}
				}
			}
			else
			{
				header('Location: https://privatedb.net');
			}
		}
		else
		{
			header('Location: https://privatedb.net');
		}
	}
	else
	{
		header('Location: https://privatedb.net');
	}
}

function GrabExpansion($id)
{
	switch($id)
	{
		case 1:
			return 'Vanilla';
		break;

		case 2:
			return 'Burning Crusade';
		break;

		case 3:
			return 'Wrath of the Lich King';
		break;
	}
}

function GrabAddonInfo($id, $column)
{
	global $con;

	$data = $con->prepare('SELECT * FROM addons WHERE id = :id');
	$data->execute(array(
		':id' => $id
	));

	$result = $data->fetchAll(PDO::FETCH_ASSOC);

	foreach($result as $row)
	{
		return $row[$column];
	}
}

function GrabFileInfo($id, $column)
{
	global $con;

	$data = $con->prepare('SELECT * FROM addons WHERE id = :id');
	$data->execute(array(
		':id' => $id
	));

	$result = $data->fetchAll(PDO::FETCH_ASSOC);

	foreach($result as $row)
	{
		$data = $con->prepare('SELECT * FROM files WHERE file_id = :file_id');
		$data->execute(array(
			':file_id' => $row['file_id']
		));

		$result = $data->fetchAll(PDO::FETCH_ASSOC);

		foreach($result as $row)
		{
			return $row[$column];
		}
	}
}

function GrabCategories($id)
{
	global $con;

	$data = $con->prepare('SELECT * FROM category WHERE id = :id');
	$data->execute(array(
		':id' => $id
	));

	$result = $data->fetchAll(PDO::FETCH_ASSOC);

	foreach($result as $row)
	{
		return $row['category'];
	}
}

function ConvertBytes($bytes, $precision = 1)
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 

    $bytes /= pow(1024, $pow);
    // $bytes /= (1 << (10 * $pow)); 

    return round($bytes, $precision) . ' ' . $units[$pow]; 
} 

function Register()
{
	if(isset($_POST['register']))
	{
		if(!empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['re-password']))
		{
			global $con;

			$username   = $_POST['username'];
			$email      = $_POST['email'];
			$password   = $_POST['password'];
			$repassword = $_POST['re-password'];

			$lastip     = $_SERVER['REMOTE_ADDR'];
			$captcha    = $_POST['g-recaptcha-response'];
			$secret     = '6LcViC8UAAAAADfc0VgbaAcTVEtRpJw3tWrSOWAq';

			if(strlen($username) <= 50)
			{
				if(filter_var($email, FILTER_VALIDATE_EMAIL))
				{
					$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $secret . "&response=" . $captcha . "&remoteip=" . $lastip);
					$decode   = json_decode($response, true);

					if(intval($decode['success']) == 1)
					{
						if($password == $repassword)
						{
							$data = $con->prepare('SELECT COUNT(*) FROM users WHERE username = :username OR email = :email');
							$data->execute(array(
								':username' => $username,
								':email'    => $email
							));

							if($data->fetchColumn() == 0)
							{
								$data = $con->prepare('INSERT INTO users (username, email, password, access, registered, ip) 
									VALUES(:username, :email, :password, :access, :registered, :ip)');
								$data->execute(array(
									':username'   => $username,
									':email'      => $email,
									':password'   => sha1($password),
									':access'     => 0,
									':registered' => time(),
									':ip'         => $lastip
								));

								echo '<div class="callout success">Success, Redirecting..</div>';
								echo '<script>
											setTimeout(function () {
											   window.location.href = "login.php";
											}, 3000);
										</script>';
							}
							else
							{
								echo '<div class="callout alert">Username or email already in use!</div>';
							}
						}
						else
						{
							echo '<div class="callout alert">Passwords dont match!</div>';
						}
					}
					else
					{
						echo '<div class="callout alert">Captcha was wrong!</div>';
					}
				}
				else
				{
					echo '<div class="callout alert">Email is not valid!</div>';
				}
			}
			else
			{
				echo '<div class="callout alert">Username is too long!</div>';
			}
		}
		else
		{
			echo '<div class="callout alert">All fields are required!</div>';
		}
	}
}

function Login()
{
	if(isset($_POST['login']))
	{
		if(!empty($_POST['username']) && !empty($_POST['password']))
		{
			global $con;

			$username = $_POST['username'];
			$password = sha1($_POST['password']);

			$data = $con->prepare('SELECT COUNT(*) FROM users WHERE username = :username AND password = :password');
			$data->execute(array(
				':username' => $username,
				':password' => $password
			));

			if($data->fetchColumn() == 1)
			{
				$data = $con->prepare('UPDATE users SET last_login = :last_login WHERE username = :username');
				$data->execute(array(
					':last_login' => time(),
					':username'   => $username
				));

				$_SESSION['username'] = $username;
				$_SESSION['password'] = $password;

				LogData('Login UserCP', 'Logged into UserCP');
				
				echo '<div class="callout success">Success, logging in..</div>';
				echo '<script>
							setTimeout(function () {
							   window.location.href = "usercp.php";
							}, 3000);
						</script>';
			}
			else
			{
				echo '<div class="callout alert">Wrong username or password!</div>';
			}
		}
		else
		{
			echo '<div class="callout alert">Please fill in all fields!</div>';
		}
	}
}

function GrabCategory($id)
{
	global $con;

	$data = $con->prepare('SELECT * FROM category ORDER BY id ASC');
	$data->execute();

	while($result = $data->fetchAll(PDO::FETCH_ASSOC))
	{
		foreach($result as $row)
		{
			return '<option value="' . $row['id'] . '">' . $row['category'] . '</option>';
		}
	}
}

function MyAddons()
{
	global $con;

	$username = $_SESSION['username'];

	if(!isset($_GET['edit']))
	{
		$status = array(
			0 => '<span class="orange">Pending</span>',
			1 => '<span class="red">Declined</span>',
			2 => '<span class="green">Active</span>'
		);

		$type = array(
			1 => 'Vanilla',
			2 => 'TBC',
			3 => 'WOTLK'
		);

		$page    = isset($_GET['page']) ? (int)$_GET['page'] : 1;
		$perPage = USERCP_ADDON_RESULTS;

		$start = ($page > 1) ? ($page * $perPage) - $perPage : 0;

		$total = $con->prepare('SELECT COUNT(*) FROM addons WHERE addon_uploader = :username AND status != 3');
		$total->execute(array(
			':username' => $username
		));

		$total = $total->fetchColumn();

		$pages = ceil($total / $perPage);

		$data = $con->prepare('SELECT COUNT(*) FROM addons WHERE addon_uploader = :username AND status != 3');
		$data->execute(array(
			':username' => $username
		));

		if($data->fetchColumn() > 0)
		{
			$data = $con->prepare('SELECT * FROM addons WHERE addon_uploader = :username AND status != 3 ORDER BY status ASC LIMIT ' . $start . ', ' . $perPage);
			$data->execute(array(
				':username' => $username
			));

			echo '<div class="content-box column small-12">
					<div class="content-box-header column small-12">
						<div class="title-text">
							My Addons
						</div>
					</div>

					<div class="content-box-content column small-12">
						<table class="addons-list">
							<th>Name</th>
							<th>Version</th>
							<th>Expansion</th>
							<th>Updated</th>
							<th>Downloads</th>
							<th>Status</th>
							<th></th>
							<th></th>';

			while($result = $data->fetchAll(PDO::FETCH_ASSOC))
			{
				foreach($result as $row)
				{
					echo '<tr>
							<td>' . $row['addon_name'] . '</td>
							<td>' . $row['addon_version'] . '</td>
							<td>' . $type[$row['expansion']] . '</td>
							<td>' . date('j. F, Y', $row['updated']) . '</td>
							<td>' . $row['downloads'] . '</td>
							<td>' . $status[$row['status']] . '</td>
							<td><a href="?edit=' . $row['id'] . '" title="Edit"><i class="fa fa-pencil yellow" aria-hidden="true"></i></a></td>
							<td><a href="?delete=' . $row['id'] . '" title="Delete" onclick="return confirm(\'Are you sure?\')"><i class="fa fa-trash red" aria-hidden="true"></i></a></td>
						</tr>';
				}
			}

			echo '</table></div></div>';

			echo '<div class="navigation column small-12"><ul class="nav-box">';

				echo '<li><a href="?page=1"><<</a></li>';

				$min = max($page - 2, 1);
				$max = min($page + 2, $pages);

				for($x = $min; $x <= $max; $x++)
				{
					if(@$page == $x)
					{
						echo '<li><a href="?page=' . $x . '" class="current-nav">' . $x . '</a></li>';
					}
					elseif(@!isset($page))
					{
						echo '<li><a href="?page=' . $x . '" class="current-nav">' . $x . '</a></li>';
					}
					else
					{
						echo '<li><a href="?page=' . $x . '">' . $x . '</a></li>';
					}
				}

				echo '<li><a href="?page=' . $pages . '">>></a></li>';

			echo '</ul></div>';
		}
		else
		{
			echo '<div class="callout alert">You have not uploaded any addons yet.</div>';
		}
	}
	else
	{
		$id = (int)$_GET['edit'];

		$data = $con->prepare('SELECT * FROM addons WHERE addon_uploader = :username AND id = :id');
		$data->execute(array(
			':username' => $username,
			':id'       => $id
		));

		$result = $data->fetchAll(PDO::FETCH_ASSOC);

		foreach($result as $row)
		{
			$version = explode('.', $row['addon_version']);

			echo '<div class="content-box column small-12">
					<div class="content-box-header column small-12">
						<div class="title-text">
							Editing Addon
						</div>
					</div>

					<div class="content-box-content column small-12">
						<div class="content-text">
							<form method="POST" enctype="multipart/form-data">
								<div class="content-strip column small-12">
									<label>Name</label>
									<input type="text" name="name" value="' . $row['addon_name'] . '" />
								</div>

								<div class="content-strip column small-12">
									<label>Version</label>
									<input type="number" name="num1" min="0" max="99" value="' . $version[0] . '" />
									<input type="number" name="num2" min="0" max="99" value="' . $version[1] . '" />
									<input type="number" name="num3" min="0" max="99" value="' . $version[2] . '" />
								</div>

								<div class="content-strip column small-12">
									<label>Description</label>
									<textarea name="description">' . $row['addon_description'] . '</textarea>
								</div>

								<div class="content-strip column small-12">
									<label>Category</label>
									<select name="category">
										<option value="1">Action Bars</option>
										<option value="2">Chat & Communication</option>
										<option value="3">Artwork</option>
										<option value="4">Auction & Economy</option>
										<option value="5">Audio & Video</option>
										<option value="6">Bags & Inventory</option>
										<option value="7">Boss Encounters</option>
										<option value="8">Buffs & Debuffs</option>
										<option value="9">Class</option>
										<option value="10">Combat</option>
										<option value="11">Guild</option>
										<option value="12">Mail</option>
										<option value="13">Map & Minimap</option>
										<option value="14">Minigames</option>
										<option value="15">Miscellaneous</option>
										<option value="16">Professions</option>
										<option value="17">PvP</option>
										<option value="18">Quests & Leveling</option>
										<option value="19">Roleplay</option>
										<option value="20">Tooltip</option>
										<option value="21">Unitframes</option>
									</select>
								</div>

								<div class="content-strip column small-12">
									<label>Expansion</label>
									<select name="expansion">
										<option value="1">Vanilla</option>
										<option value="2">Burning Crusade</option>
										<option value="3">Wrath of the Lich King</option>
									</select>
								</div>

								<div class="content-strip column small-12">
									<input type="submit" name="edit" class="small button" value="Submit" />
								</div>
							</form>
						</div>
					</div>
				</div>';
		}

		if(isset($_POST['edit']))
		{
			$name        = ucfirst($_POST['name']);
			$version     = (int)$_POST['num1'] . '.' . (int)$_POST['num2'] . '.' . (int)$_POST['num3'];
			$description = $_POST['description'];
			$category    = (int)$_POST['category'];
			$expansion   = (int)$_POST['expansion'];

			$data = $con->prepare('UPDATE addons SET addon_name = :name, addon_version = :version, addon_description = :description, category = :category, expansion = :expansion, updated = :time, status = 0 WHERE addon_uploader = :username AND id = :id AND status != 3');
			$data->execute(array(
				':name'        => $name,
				':version'     => $version,
				':description' => nl2br($description),
				':category'    => $category,
				':expansion'   => $expansion,
				':time'        => time(),
				':id'          => $id,
				':username'    => $username
			));

			LogData('Edit Addon', 'Edited addon: ' . $id);

			echo '<script>
					setTimeout(function () {
					   window.location.href = "usercp.php";
					}, 0);
				</script>';
		}
	}

	if(isset($_GET['delete']))
	{
		$id = (int)$_GET['delete'];

		$data = $con->prepare('UPDATE addons SET status = 3 WHERE id = :id AND addon_uploader = :username AND status != 3');
		$data->execute(array(
			':id'       => $id,
			':username' => $username
		));
		
		LogData('Delete Addon', 'Deleted addon: ' . $id);

		echo '<script>
				setTimeout(function () {
				   window.location.href = "usercp.php";
				}, 0);
			</script>';
	}
}

function GrabAccountInfo($type)
{
	global $con;

	$username = $_SESSION['username'];

	switch($type)
	{
		case 'USERNAME':
			return ucfirst($username);
		break;

		case 'EMAIL':
			$data = $con->prepare('SELECT * FROM users WHERE username = :username');
			$data->execute(array(
				':username' => $username
			));

			$result = $data->fetchAll(PDO::FETCH_ASSOC);

			foreach($result as $row)
			{
				return $row['email'];
			}
		break;

		case 'RANK':
			$access = array(
				0 => '<span style="color: #1abc9c;">Leecher</span>',
				1 => '<span class="blue">Uploader</span>',
				2 => '<span class="red">Moderator</span>',
				3 => '<span class="green">Administrator</span>'
			);

			$data = $con->prepare('SELECT * FROM users WHERE username = :username');
			$data->execute(array(
				':username' => $username
			));

			$result = $data->fetchAll(PDO::FETCH_ASSOC);

			foreach($result as $row)
			{
				return $access[$row['access']];
			}
		break;

		case 'ADDONS':
			$data = $con->prepare('SELECT COUNT(*) FROM addons WHERE addon_uploader = :username AND status = 2');
			$data->execute(array(
				':username' => $username
			));

			return $data->fetchColumn();
		break;

		case 'TOTAL':
			$data = $con->prepare('SELECT COUNT(*) FROM addons WHERE addon_uploader = :username AND status != 3');
			$data->execute(array(
				':username' => $username
			));

			return $data->fetchColumn();
		break;

		case 'PENDING':
			$data = $con->prepare('SELECT COUNT(*) FROM addons WHERE addon_uploader = :username AND status = 0');
			$data->execute(array(
				':username' => $username
			));

			return $data->fetchColumn();
		break;

		case 'ACTIVE':
			$data = $con->prepare('SELECT COUNT(*) FROM addons WHERE addon_uploader = :username AND status = 2');
			$data->execute(array(
				':username' => $username
			));

			return $data->fetchColumn();
		break;

		case 'DECLINED':
			$data = $con->prepare('SELECT COUNT(*) FROM addons WHERE addon_uploader = :username AND status = 1');
			$data->execute(array(
				':username' => $username
			));

			return $data->fetchColumn();
		break;
	}
}

function isLoggedOut()
{
	if(!isset($_SESSION['username']))
	{
		if(!isset($_SESSION['password']))
		{
			header('Location: login.php');
			exit();
		}

		header('Location: login.php');
		exit();
	}
}

function isLoggedIn()
{
	if(isset($_SESSION['username']))
	{
		if(isset($_SESSION['password']))
		{
			header('Location: usercp.php');
			exit();
		}

		header('Location: usercp.php');
		exit();
	}
}

function ChangePassword()
{
	if(isset($_POST['change']))
	{
		if(!empty($_POST['oldpassword']) && !empty($_POST['newpassword']) && !empty($_POST['re-password']))
		{
			global $con;

			$username    = $_SESSION['username'];
			$oldpassword = sha1($_POST['oldpassword']);
			$newpassword = sha1($_POST['newpassword']);
			$repassword  = sha1($_POST['re-password']);

			$data = $con->prepare('SELECT COUNT(*) FROM users WHERE username = :username');
			$data->execute(array(
				':username' => $username
			));

			if($data->fetchColumn() == 1)
			{
				$data = $con->prepare('SELECT * FROM users WHERE username = :username');
				$data->execute(array(
					':username' => $username
				));

				$result = $data->fetchAll(PDO::FETCH_ASSOC);

				foreach($result as $row)
				{
					if($oldpassword == $row['password'])
					{
						if($newpassword == $repassword)
						{
							$data = $con->prepare('UPDATE users SET password = :password WHERE username = :username');
							$data->execute(array(
								':username' => $username,
								':password' => $newpassword
							));

							echo '<div class="callout success">Password has been changed!</div>';
						}
						else
						{
							echo '<div class="callout alert">Passwords doesn\'t match!</div>';
						}
					}
					else
					{
						echo '<div class="callout alert">Current password doesn\'t match!</div>';
					}
				}
			}
			else
			{
				header('Location: index.php');
			}
		}
		else
		{
			echo '<div class="callout alert">Please fill in all fields!</div>';
		}
	}
}

function CheckAccess($level)
{
	global $con;

	$username = $_SESSION['username'];

	if(isset($username) && isset($_SESSION['password']))
	{
		$data = $con->prepare('SELECT COUNT(*) FROM users WHERE username = :username AND access >= :access');
		$data->execute(array(
			':username' => $username,
			':access'   => $level
		));

		if($data->fetchColumn() == 1)
		{
			echo '<div class="content-box column small-12">
					<div class="content-box-header column small-12">
							<div class="title-text">
								Upload Addon
							</div>
						</div>

						<div class="content-box-content column small-12">
							<div class="content-text">
								<form method="POST" enctype="multipart/form-data">
									<div class="content-strip column small-12">
										<label>Name</label>
										<input type="text" name="name" />
									</div>

									<div class="content-strip column small-12">
										<label>Version</label>
										<input type="number" name="num1" min="0" max="99" value="1" />
										<input type="number" name="num2" min="0" max="99" value="0" />
										<input type="number" name="num3" min="0" max="99" value="0" />
									</div>

									<div class="content-strip column small-12">
										<label>Description</label>
										<textarea name="description"></textarea>
									</div>

									<div class="content-strip column small-12">
										<label>Category</label>
										<select name="category">
											<option value="1">Action Bars</option>
											<option value="2">Chat & Communication</option>
											<option value="3">Artwork</option>
											<option value="4">Auction & Economy</option>
											<option value="5">Audio & Video</option>
											<option value="6">Bags & Inventory</option>
											<option value="7">Boss Encounters</option>
											<option value="8">Buffs & Debuffs</option>
											<option value="9">Class</option>
											<option value="10">Combat</option>
											<option value="11">Guild</option>
											<option value="12">Mail</option>
											<option value="13">Map & Minimap</option>
											<option value="14">Minigames</option>
											<option value="15">Miscellaneous</option>
											<option value="16">Professions</option>
											<option value="17">PvP</option>
											<option value="18">Quests & Leveling</option>
											<option value="19">Roleplay</option>
											<option value="20">Tooltip</option>
											<option value="21">Unitframes</option>
										</select>
									</div>

									<div class="content-strip column small-12">
										<label>Expansion</label>
										<select name="expansion">
											<option value="1">Vanilla</option>
											<option value="2">Burning Crusade</option>
											<option value="3">Wrath of the Lich King</option>
										</select>
									</div>

									<div class="content-strip column small-12">
										<label>Upload Addon (.zip)</label>
										<input type="file" name="file" />
									</div>

									<div class="content-strip column small-12">
										<input type="submit" name="upload" class="small button" value="Submit" />
									</div>
								</form>
							</div>
						</div>
					</div>';
		}
		else
		{
			echo '<div class="apply-button">
					<form method="POST">
						<center><input type="submit" name="apply" class="small button" value="Apply to become a uploader" /></center>
					</form>
				</div>';

			if(isset($_POST['apply']))
			{
				$data = $con->prepare('SELECT COUNT(*) FROM applications WHERE username = :username AND active = 1');
				$data->execute(array(
					':username' => $username
				));

				if($data->fetchColumn() == 0)
				{
					$data = $con->prepare('INSERT INTO applications (username, active, time) VALUES(:username, :active, :time)');
					$data->execute(array(
						':username' => $username,
						':active'   => 1,
						':time'     => time()
					));

					echo '<div class="callout success">Application has been sent to our admins!</div>';
				}
				else
				{
					echo '<div class="callout alert">You have already applied to become a uploader!</div>';
				}
			}
		}
	}
}
































?>
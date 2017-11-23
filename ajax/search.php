<?php


if(isset($_POST['search']))
{
	global $con;

	$name = ucfirst(strtolower($_POST['search']));

	$page    = isset($_GET['page']) ? (int)$_GET['page'] : 1;
	$perPage = 15;

	$start = ($page > 1) ? ($page * $perPage) - $perPage : 0;

	$data = $con->prepare('SELECT * FROM addons WHERE addon_name LIKE ? ORDER BY id ASC LIMIT ' . $start . ', ' . $perPage);
	$data->execute(array(
		"%$name%"
	));

	$data2 = $con->prepare('SELECT * FROM addons WHERE addon_name LIKE ?');
	$data2->execute(array(
		"%$name%"
	));

	$rows = $data2->rowCount();
	
	$totalq = $con->prepare('SELECT * FROM addons WHERE addon_name LIKE ?');
	$totalq->execute(array(
		"%$name%"
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
					<td>' . date('d. F, Y', $row['updated']) . '</td>
					<td>' . $row['downloads'] . '</td>
					<td><a href="download.php?id=' . $row['id'] . '" class="small button">DOWNLOAD</a></td>
				</tr>';
		}
	}

	echo '</table>
			</div>
		</div>';

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

















?>
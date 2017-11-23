<?php

include('header.php');

isLoggedOut();

?>

<div class="row">
	<div class="content-menu column small-12">
		<div class="content-bar">
			<ul class="usercp-bar">
				<li><a href="usercp.php" class="current-nav">My Addons</a></li>
				<li><a href="change_password.php">Change Password</a></li>
				<li><a href="upload.php">Upload Addon</a></li>
			</ul>
		</div>
	</div>

	<div class="content column small-12 medium-7 large-8">	
		<?php MyAddons(); ?>
	</div>

	<div class="content column small-12 medium-5 large-4">
		<div class="content-box-sidebar column small-12">
			<div class="content-box-header column small-12">
				<div class="title-text">
					Account Information
				</div>
			</div>

			<div class="content-box-content column small-12">
				<table class="account-info">
					<tr>
						<td>Username</td>
						<td><?php echo GrabAccountInfo('USERNAME'); ?></td>
					</tr>

					<tr>
						<td>Email</td>
						<td><?php echo GrabAccountInfo('EMAIL'); ?></td>
					</tr>

					<tr>
						<td>Rank</td>
						<td><?php echo GrabAccountInfo('RANK'); ?></td>
					</tr>

					<tr>
						<td>Addons</td>
						<td><?php echo GrabAccountInfo('ADDONS'); ?></td>
					</tr>
				</table>
			</div>
		</div>
	</div>

	<div class="content column small-12 medium-5 large-4">
		<div class="content-box-sidebar column small-12">
			<div class="content-box-header column small-12">
				<div class="title-text">
					My Uploads
				</div>
			</div>

			<div class="content-box-content column small-12">
				<table class="account-info">
					<tr>
						<td>Uploaded</td>
						<td><?php echo GrabAccountInfo('TOTAL'); ?></td>
					</tr>

					<tr>
						<td>Accepted</td>
						<td><?php echo GrabAccountInfo('ACTIVE'); ?></td>
					</tr>

					<tr>
						<td>Declined</td>
						<td><?php echo GrabAccountInfo('DECLINED'); ?></td>
					</tr>

					<tr>
						<td>Pending</td>
						<td><?php echo GrabAccountInfo('PENDING'); ?></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>
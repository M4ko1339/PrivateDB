<?php

include('header.php');

?>

<div class="row">
	<div class="content column small-12 medium-7 large-8">
		<div class="content-box column small-12">
			<div class="content-box-header column small-12">
				<div class="title-text">
					<?php echo GrabAddonInfo($_GET['id'], 'addon_name'); ?>
				</div>

				<div class="title-date">
					
				</div>
			</div>

			<div class="content-box-content column small-12">
				<div class="content-text">
					<?php echo GrabAddonInfo($_GET['id'], 'addon_description'); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="content column small-12 medium-5 large-4">
		<div class="content-box-sidebar border column small-12">
			<div class="content-box-header column small-12">
				<div class="title-text">
					Download Information
				</div>
			</div>

			<div class="content-box-content column small-12">
				<table class="addons-info">
					<tr>
						<td>Filename</td>
						<td><?php echo ucfirst(GrabFileInfo($_GET['id'], 'file_name')); ?></td>
					</tr>

					<tr>
						<td>Size</td>
						<td><?php echo ConvertBytes(GrabFileInfo($_GET['id'], 'file_size')); ?></td>
					</tr>

					<tr>
						<td>Uploader</td>
						<td><?php echo ucfirst(GrabAddonInfo($_GET['id'], 'addon_uploader')); ?></td>
					</tr>

					<tr>
						<td>Downloads</td>
						<td><?php echo GrabAddonInfo($_GET['id'], 'downloads'); ?></td>
					</tr>

					<tr>
						<td>Category</td>
						<td><?php echo GrabCategory(GrabAddonInfo($_GET['id'], 'category')); ?></td>
					</tr>

					<tr>
						<td>Expansion</td>
						<td><?php echo GrabExpansion(GrabAddonInfo($_GET['id'], 'expansion')); ?></td>
					</tr>
				</table>

				<div class="download-info">
					<center>
						<a href="download.php?id=<?php echo (int)$_GET['id']; ?>" class="small button">DOWNLOAD</a>
					</center>
				</div>
			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>
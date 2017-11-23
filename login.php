<?php

include('header.php');

isLoggedIn();

?>

<div class="row">
	<div class="content column small-12">
		<div class="input-box">
			<div class="input-box-header">
				Login | <span class="white">UserCP</span>
			</div>

			<div class="input-box-content">
				<form method="POST">
					<label>Username</label>
					<input type="text" name="username" />

					<label>Password</label>
					<input type="password" name="password" />

					<input type="submit" class="small button" name="login" value="Login" />
				</form>
			</div>
		</div>

		<div class="response">
			<?php Login(); ?>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>
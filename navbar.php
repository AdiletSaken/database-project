<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
	<a class="navbar-brand" href="index.php">Bonus.kz</a>
  	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
    	<span class="navbar-toggler-icon"></span>
  	</button>
  	<div class="collapse navbar-collapse" id="navbarToggler">
    	<ul class="navbar-nav mr-auto mt-2 mt-lg-0">
      		<li class="nav-item <?php if ($page == 'home') { echo 'active'; } ?>">
        		<a class="nav-link" href="index.php">Home</a>
     		</li>
			<li class="nav-item <?php if ($page == 'companies') { echo 'active'; } ?>">
				<a class="nav-link" href="companies.php">Companies</a>
			</li>
			<?php if (isset($_SESSION['email'])) { ?>
				<li class="nav-item <?php if ($page == 'account') { echo 'active'; } ?>">
					<a class="nav-link" href="account.php">Account</a>
				</li>
			<?php } else { ?>
				<li class="nav-item <?php if ($page == 'sign_up') { echo 'active'; } ?>">
					<a class="nav-link" href="sign_up.php">Sign up</a>
				</li>
				<li class="nav-item <?php if ($page == 'sign_in') { echo 'active'; } ?>">
					<a class="nav-link" href="sign_in.php">Sign in</a>
				</li>
			<?php } ?>
		</ul>
		<?php if (isset($_SESSION['email'])) { ?>
			<a href="/api/sign_out.php" class="btn btn-outline-light">Sign out</a>
		<?php } ?>
	</div>
</nav>
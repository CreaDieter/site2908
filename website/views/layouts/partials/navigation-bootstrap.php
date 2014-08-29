<div id="menu">
	<nav class="navbar navbar-default" role="navigation">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only"><?= $this->translate('menu') ?></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
			</div>
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<?php
				// Render the navigational html
				$this->navigation()->menu()->setPartial("partials/menu-bootstrap.php");
				echo $this->navigation()->menu()->render();
				?>
			</div>
		</div>
	</nav>
</div>
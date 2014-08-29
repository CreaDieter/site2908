<div id="menu">
	<nav>
			<?php
			// Render the navigational html
			$this->navigation()->menu()->setPartial("partials/menu.php");
			echo $this->navigation()->menu()->render();
			?>
	</nav>
</div>
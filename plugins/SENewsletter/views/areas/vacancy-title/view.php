<?= $this->renderlet(
	"gallery",
	array(
		"controller" => "newsletter",
		"action"     => "vacancy-title",
		"module"     => "SENewsletter",
		"title"      => "drop vacancy here",
		"height"     => 100,
		"language"   => $this->language,
		"editmode"   => $this->editmode
	)
); ?>
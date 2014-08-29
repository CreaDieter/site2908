<?= $this->renderlet(
	"gallery",
	array(
		"controller" => "newsletter",
		"action"     => "vacancy-detail",
		"module"     => "SENewsletter",
		"title"      => "drop vacancy here",
		"height"     => 400,
		"language"   => $this->language,
		"editmode"   => $this->editmode
	)
); ?>
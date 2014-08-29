<?= $this->renderlet(
	"gallery",
	array(
		"controller" => "newsletter",
		"action"     => "event-detail",
		"module"     => "SENewsletter",
		"title"      => "drop event here",
		"height"     => 400,
		"language"   => $this->language,
		"editmode"   => $this->editmode
	)
); ?>
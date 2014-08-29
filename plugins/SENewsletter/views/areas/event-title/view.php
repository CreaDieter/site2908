<?= $this->renderlet(
	"gallery",
	array(
		"controller" => "newsletter",
		"action"     => "event-title",
		"module"     => "SENewsletter",
		"title"      => "drop event here",
		"height"     => 100,
		"language"   => $this->language,
		"editmode"   => $this->editmode
	)
); ?>
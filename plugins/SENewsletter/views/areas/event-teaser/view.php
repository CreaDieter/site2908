<?= $this->renderlet(
	"gallery",
	array(
		"controller" => "newsletter",
		"action"     => "event-teaser",
		"module"     => "SENewsletter",
		"title"      => "drop event here",
		"height"     => 400,
		"language"   => $this->language,
		"editmode"   => $this->editmode
	)
); ?>
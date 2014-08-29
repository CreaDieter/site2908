<?= $this->renderlet(
	"gallery",
	array(
		"controller" => "newsletter",
		"action"     => "news-teaser",
		"module"     => "SENewsletter",
		"title"      => "drop news here",
		"height"     => 400,
		"language"   => $this->language,
		"editmode"   => $this->editmode
	)
); ?>
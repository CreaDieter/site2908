<?= $this->renderlet(
	"gallery",
	array(
		"controller" => "newsletter",
		"action"     => "news-title",
		"module"     => "SENewsletter",
		"title"      => "drop news here",
		"height"     => 100,
		"language"   => $this->language,
		"editmode"   => $this->editmode
	)
); ?>
<?= $this->renderlet(
	"gallery",
	array(
		"controller" => "newsletter",
		"action"     => "news-detail",
		"module"     => "SENewsletter",
		"title"      => "drop news here",
		"height"     => 400,
		"language"   => $this->language,
		"editmode"   => $this->editmode
	)
); ?>
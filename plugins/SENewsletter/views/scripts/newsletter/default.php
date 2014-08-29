<table width="100%" >
	<tr>
		<td>
			<?= $this->areablock("myAreablock",array(
					"allowed"		=> [ /* @TODO automagically fill in these areas */
						'event-detail',
						'event-teaser',
						'event-title',
						'news-detail',
						'news-teaser',
						'news-title',
						'newsletter-content',
						'newsletter-image',
						'newsletter-ruler',
						'newsletter-subtitle',
						'newsletter-title',
						'vacancy-detail',
						'vacancy-teaser',
						'vacancy-title'
					],
					"areablock_toolbar"	=> array(
						"title"               => "",
						"width"               => 230,
						"x"                   => 20,
						"y"                   => 50,
						"xAlign"              => "right",
						"buttonWidth"         => 218,
						"buttonMaxCharacters" => 35
					),
				));
			?>
		</td>
	</tr>
</table>

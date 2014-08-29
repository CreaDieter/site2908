TRUNCATE TABLE `cache`;

/*--NEXT--*/

TRUNCATE TABLE `cache_tags`;

/*--NEXT--*/

ALTER TABLE `cache_tags` ENGINE=InnoDB;

/*--NEXT--*/

CREATE TABLE `se_i18n_keys` (
								  `document_id` bigint(20) NOT NULL,
								  `language` varchar(2) DEFAULT NULL,
								  `sourcePath` varchar(255) DEFAULT NULL,
								  PRIMARY KEY (`document_id`)
								) ENGINE=MyISAM DEFAULT CHARSET=utf8;
							;

/*--NEXT--*/


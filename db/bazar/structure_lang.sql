CREATE TABLE `bazar_lang` (
`id` bigint(20) unsigned NOT NULL,
`lang` varchar(2) NOT NULL,
`title` TINYTEXT NULL ,
`description` TEXT NULL ,
 UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;
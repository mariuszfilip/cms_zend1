CREATE TABLE `ek_wysylka` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `adres` varchar(100) NOT NULL,
  `temat` varchar(255) NOT NULL,
  `tresc` text NOT NULL,
  `data_dodania` datetime NOT NULL,
  `data_wysylki` datetime NOT NULL,
  `data_wyslania` datetime DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ekw_01` (`status`,`data_wysylki`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `ek_wysylka` MODIFY `adres` text NOT NULL;
ALTER TABLE `ek_wysylka` ADD `adres_dw` text AFTER `adres`;
ALTER TABLE `ek_wysylka` ADD `adres_udw` text AFTER `adres_dw`;

ALTER TABLE `ek_wysylka` ADD `status_komunikat` text after `status`;


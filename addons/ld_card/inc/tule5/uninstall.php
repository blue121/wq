<?php

$sql =<<<EOF
DROP TABLE IF EXISTS `ld_card_cards`;
DROP TABLE IF EXISTS `ld_card_cardticket`;
DROP TABLE IF EXISTS `ld_card_carousel`;
DROP TABLE IF EXISTS `ld_card_category`;
DROP TABLE IF EXISTS `ld_card_log`;
DROP TABLE IF EXISTS `ld_card_users`;
EOF;
pdo_run($sql);
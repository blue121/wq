<?php
defined('IN_IA') || die('Access Denied');
class Ld_cardModuleProcessor extends WeModuleProcessor
{
	public function respond()
	{
		$content = $this->message['content'];
	}
}
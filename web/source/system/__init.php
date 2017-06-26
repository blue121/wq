<?php
/**
 * [WeEngine System] Copyright (c) 2014 tule5.com
 * WeEngine is NOT a free software, it under the license terms, visited http://www.tule5.com/ for more details.
 */

define('IN_GW', true);
if ($controller == 'system' && $action == 'content_provider') {
	$system_activie = 2;
} else {
	$system_activie = 1;
}

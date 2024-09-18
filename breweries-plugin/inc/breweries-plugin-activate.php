<?php
/**
 * @package  breweriesPlugin
 */

class breweriesPluginActivate
{
	public static function activate() {
		flush_rewrite_rules();
	}
}
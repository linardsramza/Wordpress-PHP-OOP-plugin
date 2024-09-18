<?php
/**
 * @package  breweriesPlugin
 */

class breweriesPluginDeactivate
{
	public static function deactivate() {
		flush_rewrite_rules();
	}
}
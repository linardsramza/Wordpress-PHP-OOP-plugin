<?php

/**
 * @package  breweriesPlugin
 */
/*
Plugin Name: Breweries Plugin
Description: Cron, fetch items button, delete upon deactivation, create table upon activation, some colors. TODO: Namespaces, refactor admin.
Version: 1.0.0
Author: Linards Ramza
*/

defined('ABSPATH') or die('Go away');

if (!class_exists('breweriesPlugin')) {

	class breweriesPlugin
	{

		public $plugin;
		public $admin;

		public function __construct()
		{
			require_once plugin_dir_path(__FILE__) . 'templates/admin.php';
			$this->plugin = plugin_basename(__FILE__);
			$this->admin = plugin_dir_path(__FILE__) . 'templates/admin.php';
		}

		public function register()
		{
			add_action('admin_enqueue_scripts', array($this, 'enqueue'));
			add_filter("plugin_action_links_$this->plugin", array($this, 'settings_link'));
		}

		private function settings_link($links)
		{
			$settings_link = '<a href="admin.php?page=breweries_plugin">Settings</a>';
			array_push($links, $settings_link);
			return $links;
		}

		private function enqueue()
		{
			wp_enqueue_style('breweries-style', plugins_url('/assets/style.css', __FILE__));
			wp_enqueue_script('breweries-script', plugins_url('/assets/script.js', __FILE__));
		}

		public function activate()
		{
			//require_once plugin_dir_path(__FILE__) . 'inc/breweries-plugin-activate.php';
			//breweriesPluginActivate::activate();
			require_once plugin_dir_path(__FILE__) . 'inc/breweries-plugin-create-breweries-db.php';
			breweriesPluginCreateBreweriesDb::create_breweries_db();
		}

		public function deactivate()
		{
			//require_once plugin_dir_path(__FILE__) . 'inc/breweries-plugin-deactivate.php';
			//breweriesPluginDeactivate::deactivate();
			require_once plugin_dir_path(__FILE__) . 'inc/breweries-plugin-destroy-breweries-db.php';
			breweriesPluginDestroyBreweriesDb::destroy_breweries_db();
		}
	}

	$breweriesPlugin = new breweriesPlugin();
	$breweriesPlugin->register();
	$admin = new breweriesPluginAdmin();

	register_activation_hook(__FILE__, array($breweriesPlugin, 'activate'));
	register_deactivation_hook(__FILE__, array($breweriesPlugin, 'deactivate'));
}

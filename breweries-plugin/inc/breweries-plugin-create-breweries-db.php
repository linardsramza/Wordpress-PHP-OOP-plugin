<?php

/**
 * @package  breweriesPlugin
 */

class breweriesPluginCreateBreweriesDb
{
        public static function create_breweries_db()
        {
                global $wpdb;
                $table_name = $wpdb->prefix . 'breweries';
                $charset_collate = $wpdb->get_charset_collate();

                $sql = "CREATE TABLE $table_name (
                    id INT NOT NULL AUTO_INCREMENT,
                    name VARCHAR(100) NOT NULL,
                    brewery_type VARCHAR(255) NOT NULL,
                    address_1 VARCHAR(100) NOT NULL,
                    city VARCHAR(255) NOT NULL,
                    country VARCHAR(255) NOT NULL,
                    phone BIGINT,
                    state VARCHAR(100) NOT NULL,
                    street VARCHAR(100) NOT NULL,
                    PRIMARY KEY (id)
                ) $charset_collate;";

                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);
        }
}

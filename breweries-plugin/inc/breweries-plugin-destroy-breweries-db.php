<?php

/**
 * @package  breweriesPlugin
 */

class breweriesPluginDestroyBreweriesDb
{
        public static function destroy_breweries_db()
        {
                global $wpdb;
                $table_name = $wpdb->prefix . 'breweries';

                $sql = "DROP TABLE IF EXISTS $table_name;";
                $wpdb->query($sql);
        }
}

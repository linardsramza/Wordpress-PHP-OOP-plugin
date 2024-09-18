<?php

/**
 * @package  breweriesPlugin
 */

class breweriesPluginAdmin
{
    
    public function __construct()
    {
        add_action('brewery_fetch_data_cron', array($this, 'fetch_data_cron_job'));
        add_action('admin_init', array($this, 'handle_manual_fetch'));
        add_action('admin_menu', array($this, 'add_admin_page'));
        //add_action('admin_notices', array($this, 'list_imported_items'));
        add_action('wp', array($this, 'schedule_cron_event'));
    }

    private function list_imported_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'breweries';

        $imported_items = $wpdb->get_results("SELECT * FROM $table_name");

        if ($imported_items) {
            echo '<h2>Imported Breweries</h2>';
            echo '<ul>';
            foreach ($imported_items as $item) {
                echo '<li>' . esc_html($item->name) . '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p>No items have been imported.</p>';
        }
    }

    private function add_admin_page()
    {
        add_menu_page(
            'breweries Plugin',
            'Breweries',
            'manage_options',
            'breweries_plugin',
            array($this, 'admin_page'),
            'dashicons-beer',
            10
        );
    }

    private function admin_page()
    {
?>
        <div class="container breweries">
            <h2>Brewery Plugin</h2>
            <p>Import breweries</p>
            <form method="post" action="">
                <?php wp_nonce_field('brewery_fetch_data', 'brewery_fetch_data_nonce'); ?>
                <input type="submit" class="button button-primary" name="brewery_fetch_data" value="Fetch Data">
            </form>
            <?php
            $this->list_imported_items();    
            ?>
        </div>
<?php
    }

    private function get_breweries_json()
    {

        $res = wp_remote_get('https://api.openbrewerydb.org/v1/breweries');
        $res_body = wp_remote_retrieve_body($res);
        $res_json = json_decode($res_body);

        return $res_json;
    }

    private function schedule_cron_event()
    {
        if (!wp_next_scheduled('brewery_fetch_data_cron')) {
            wp_schedule_event(time(), 'daily', 'brewery_fetch_data_cron');
        }
    }

    private function fetch_data_cron_job()
    {
        $api_data[] = $this->get_breweries_json();

        if (!empty($api_data)) {
            global $wpdb;

            $table = $wpdb->prefix . 'breweries';

            foreach ($api_data[0] as $data) {

                $data_exists = $wpdb->get_var(
                    $wpdb->prepare("SELECT COUNT(*) FROM $table WHERE name = %s", $data->name)
                );

                if (!$data_exists) {
                    $sanitized_data = array(
                        'name' => sanitize_text_field($data->name),
                        'brewery_type' => sanitize_text_field($data->brewery_type),
                        'address_1' => sanitize_text_field($data->address_1),
                        'city' => sanitize_text_field($data->city),
                        'country' => sanitize_text_field($data->country),
                        'phone' => sanitize_text_field($data->phone),
                        'state' => sanitize_text_field($data->state),
                        'street' => sanitize_text_field($data->street),
                    );

                    $wpdb->insert($table, $sanitized_data);
                }
            }
        }
    }

    private function handle_manual_fetch()
    {
        if (isset($_POST['brewery_fetch_data']) && check_admin_referer('brewery_fetch_data', 'brewery_fetch_data_nonce')) {
            if (current_user_can('manage_options')) {
                do_action('brewery_fetch_data_cron');
            }
        }
    }
}

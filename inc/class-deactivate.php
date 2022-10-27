<?php

class Deactivate
{
    function __construct() {
        self::deactivate();
    }
    
    public function deactivate() {
        self::delete_tables();
    }
    
    public function delete_tables() {
        global $wpdb;
        
        $table_name_country = $wpdb->prefix . "tbc_country";
        $table_name_rules = $wpdb->prefix . "tbc_rules";

        $sql="DROP TABLE ".$table_name_country;
        $wpdb->get_results( $sql );

        $sql="DROP TABLE ".$table_name_rules;
        $wpdb->get_results( $sql );
        
        delete_option( 'tbc_white_list' );
        delete_option( 'tbc_black_list' );
        delete_option( 'tbc_custom_style_wp_head' );
        delete_option( 'tbc_debug_mode' );
        delete_option( 'tbc_view_mode' );
        delete_option( 'tbc_cloud_flare' );
    }
}
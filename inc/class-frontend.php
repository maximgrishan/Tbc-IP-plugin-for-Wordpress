<?php

class Frontend
{   
    function __construct() {
        add_action( 'wp_head', array( $this, 'tbc_ip_text' ) );
        
        // Регистрация шорткода
        add_shortcode( 'tbc', array( $this, 'tbc_shortcode' ) );
    }
    
    function tbc_ip_text() {
        $tbc_view_mode  = get_option( 'tbc_view_mode' );
        
        //  Если активен View mode - вывод вверху, снизу, скрытие поста/записи
        if ( ( $tbc_view_mode == 'top' ) || ( $tbc_view_mode == '' ) ) {            
            add_action( 'wp_body_open', array( $this, 'tbc_outer_text' ) ); 
        } else if ( $tbc_view_mode == 'bottom') {
            add_action( 'get_footer', array( $this, 'tbc_outer_text' ) );
        } else {
            return false;
        }
    }
    
    function tbc_outer_text() {
        echo $this->tbc_text( );
    }
    
    function tbc_text( ){        
        $result_ip = $this->tbc_ip_geo()['ip'];
        $result_country = $this->tbc_ip_geo()['country'];
        $outer = '';
        
        $tbc_debug_mode = get_option( 'tbc_debug_mode' );
        $tbc_white_list = get_option( 'tbc_white_list' );
        $tbc_black_list = get_option( 'tbc_black_list' );
        
        $rules_active = $this->tbc_check_rules( $result_country );
        $pages_active = $this->tbc_check_pages( $result_country );
        
        if ( ( $rules_active == 'true' ) && ( $pages_active == 'true' ) ) 
            $outer = '<div class="tbc_inner_white_text">' . $tbc_white_list . '</div>';
        else
            $outer = '<div class="tbc_inner_black_text">' . $tbc_black_list . '</div>';
        
        //  Если активен Debug mode - добавляется вывод ip пользователя
        if ( $tbc_debug_mode == 'true' ) 
            $outer .= '<div class="tbc_inner_debug">' . __( 'Your ip: ' ) . $result_ip . '</div>';
        
        return $outer;
    }
    
    function tbc_ip_geo(){
        $tbc_cloude_flare_mode = get_option( 'tbc_cloud_flare' );
        
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = @$_SERVER['REMOTE_ADDR'];
        $result  = array('country'=>'', 'ip'=>'');
        
        if ( $tbc_cloude_flare_mode == 'true') {
            if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
                $http_x_headers = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );
                $_SERVER['REMOTE_ADDR'] = $http_x_headers[0];
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } else {
            if( filter_var($client, FILTER_VALIDATE_IP) ){
                $ip = $client;
            } elseif (filter_var($forward, FILTER_VALIDATE_IP)){
                $ip = $forward;
            } else {
                $ip = $remote;
            }
        }
        
        $ip_data = @json_decode( file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip) );
        
        if ( $ip_data && $ip_data->geoplugin_countryName != null ){
            $result['country'] = $ip_data->geoplugin_countryName;
            $result['ip'] = $ip;
        }
        return $result;
    }
    
    function tbc_check_rules( $country ) {
        global $wpdb;
        $rules_active = 'false';
        
        $table_name_rules = $wpdb->prefix . "tbc_rules";
        $query_rules = "select country from " . $table_name_rules;
        $result_rules_active = $wpdb->get_results($query_rules, ARRAY_A);
        
        $array_rules_active = wp_list_pluck( $result_rules_active, 'country' );
        
        // Создание массива, для помещения стран, одномерный
        $new_array_active = array();
        
        // Перебор окончательного массива, занесение данных в одномерный массив
        foreach ( $array_rules_active as $key => $item ) {
            $s_items = maybe_unserialize ( $item );
            foreach ( $s_items as $s_item ) {
                array_push( $new_array_active, $s_item );
            }
        }
        
        $new_list_country = implode(", ", maybe_unserialize( $new_array_active ) );
        $table_name = $wpdb->prefix . "tbc_country";
        $query = "select nicename from " . $table_name . " WHERE id IN (" . $new_list_country . ")";
        $result = $wpdb->get_results($query, ARRAY_A);
                            
        $array_name_country = reset( ( call_user_func_array( 'array_merge_recursive', $result ) ) );
        
        if ( in_array( $country, (array)$array_name_country ) ) {
            $rules_active = 'true';
        } else {
            $rules_active = 'false';
        }
                        
        return $rules_active;
    }
    
    function tbc_check_pages( $country ) {
        global $wpdb;
        $pages_active = 'false';
        $post_id = get_the_ID();
        $country_id = 0;
        
        $table_name_country = $wpdb->prefix . "tbc_country";
        $query_country_id = "select id from " . $table_name_country . " WHERE nicename = '". $country ."' LIMIT 1";
        $result_country = $wpdb->get_results($query_country_id, ARRAY_A);
        $country_id = $result_country[0]['id'];
        
        $table_name_rules = $wpdb->prefix . "tbc_rules";
        $query_rules = "select id, country, pages from " . $table_name_rules . " WHERE pages<>''";
        $result_rules_active = $wpdb->get_results($query_rules, ARRAY_A);
        
        foreach ( $result_rules_active as $key => $item ) {
            $pages_temp = maybe_unserialize ( $item['pages'] );
            if ( in_array( $post_id, (array)$pages_temp ) ) {
                $country_temp = maybe_unserialize ( $item['country'] );
                if ( in_array( $country_id, (array)$country_temp ) ) {
                    $pages_active = 'true';
                }
            }
        }
        
        return $pages_active;
    }
    
    function tbc_shortcode( $atts ) {
        $rules_id = $atts['id'];
        
        $result_ip = $this->tbc_ip_geo()['ip'];
        $result_country = $this->tbc_ip_geo()['country'];
        $outer = '';
        
        $tbc_debug_mode = get_option( 'tbc_debug_mode' );
        $tbc_white_list = get_option( 'tbc_white_list' );
        $tbc_black_list = get_option( 'tbc_black_list' );
        
        $rules_active = $this->tbc_shortcode_check_rules( $result_country, $rules_id );
        $pages_active = $this->tbc_shortcode_check_pages( $result_country, $rules_id );
        
        if ( ( $rules_active == 'true' ) && ( $pages_active == 'true' ) ) 
            $outer = '<div class="tbc_inner_white_text">' . $tbc_white_list . '</div>';
        else
            $outer = '<div class="tbc_inner_black_text">' . $tbc_black_list . '</div>';
        //  Если активен Debug mode - добавляется вывод ip пользователя
        
        if ( $tbc_debug_mode == 'true' ) 
            $outer .= '<div class="tbc_inner_debug">' . __( 'Your ip: ' ) . $result_ip . '</div>';
    
        return $outer;
    }
    
    function tbc_shortcode_check_rules( $country, $rules_id ) {
        global $wpdb;
        $rules_active = 'false';
        
        $table_name_rules = $wpdb->prefix . "tbc_rules";
        $query_rules = "select country from " . $table_name_rules . ' WHERE id='.$rules_id;
        $result_rules_active = $wpdb->get_results($query_rules, ARRAY_A);
        $array_rules_active = wp_list_pluck( $result_rules_active, 'country' );
        
        // Создание массива, для помещения стран, одномерный
        $new_array_active = array();

        // Перебор окончательного массива, занесение данных в одномерный массив
        foreach ( $array_rules_active as $key => $item ) {
            $s_items = maybe_unserialize ( $item );
            foreach ( $s_items as $s_item ) {
                array_push( $new_array_active, $s_item );
            }
        }
        
        $new_list_country = implode(", ", maybe_unserialize( $new_array_active ) );
        $table_name = $wpdb->prefix . "tbc_country";
        $query = "select nicename from " . $table_name . " WHERE id IN (" . $new_list_country . ")";
        $result = $wpdb->get_results($query, ARRAY_A);
                            
        $array_name_country = reset( ( call_user_func_array( 'array_merge_recursive', $result ) ) );
        
        if ( in_array( $country, (array)$array_name_country ) ) {
            $rules_active = 'true';
        } else {
            $rules_active = 'false';
        }
                                
        return $rules_active;
    }
    
    function tbc_shortcode_check_pages( $country, $rules_id ) {
        global $wpdb;
        $pages_active = 'false';
        $post_id = get_the_ID();
        $country_id = 0;
        
        $table_name_country = $wpdb->prefix . "tbc_country";
        $query_country_id = "select id from " . $table_name_country . " WHERE nicename = '". $country ."' LIMIT 1";
        $result_country = $wpdb->get_results($query_country_id, ARRAY_A);
        $country_id = $result_country[0]['id'];
        
        $table_name_rules = $wpdb->prefix . "tbc_rules";
        $query_rules = "select id, country, pages from " . $table_name_rules . " WHERE id=" . $rules_id;
        $result_rules_active = $wpdb->get_results($query_rules, ARRAY_A);
        
        foreach ( $result_rules_active as $key => $item ) {
            $pages_temp = maybe_unserialize ( $item['pages'] );
            if ( in_array( $post_id, (array)$pages_temp ) ) {
                $country_temp = maybe_unserialize ( $item['country'] );
                if ( in_array( $country_id, (array)$country_temp ) ) {
                    $pages_active = 'true';
                }
            }
        }
                
        return $pages_active;
    }
}
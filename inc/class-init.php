<?php

class Init
{
    private $options_rules;
    private $options_settings;
    private $options_css;
    private $_Table;
    
    function __construct() {
        add_action( 'admin_menu', array( $this, 'tbc_add_pages' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'tbc_load_plugin_enqueue' ) );
        add_action( 'wp_head', array( $this, 'tbc_style_wp_head' ), 100);
        
        // Инициализирование класса для вывода во фронтенд
        require_once plugin_dir_path( __FILE__ ) . 'class-frontend.php';
        $Frontend = new Frontend();
    }
    
    public function tbc_add_pages() {
        add_menu_page( 'TBC', 'TBC', 'manage_options' , 'tbc', array($this, 'tbc_page'), 'dashicons-admin-site-alt2', 8 );
    }
    
    function tbc_load_plugin_enqueue() {
        wp_enqueue_style( 'tpc_admin', plugins_url( '/assets/css/admin.css' , dirname(__FILE__) ) );
        wp_enqueue_script( 'tpc_admin', plugins_url( 'assets/js/tbc.multiselect.js' , dirname(__FILE__) ) );
    }
    
    public function tbc_page() {
        $this->options_rules     = get_option( 'tbc_rules' );
		$this->options_settings  = get_option( 'tbc_settings' );
		$this->options_css       = get_option( 'tbc_styles' );
        
        // Проверка страницы - Setting ? true : false
        $settings_page = ( isset( $_GET['action'] ) && 'settings' == $_GET['action'] ) ? true : false;
        
        // Проверка страницы - Style ? true : false
        $styles_page = ( isset( $_GET['action'] ) && 'styles' == $_GET['action'] ) ? true : false;
        ?>

        <div class="wrap">
            <div class="tbc-container">
                <h1>TBC panel</h1>
                <h2 class="nav-tab-wrapper">
                    <a href="<?php echo admin_url( 'admin.php?page=tbc' ); ?>" class="nav-tab<?php if ( ! isset( $_GET['action'] ) || isset( $_GET['action'] ) && 'settings' != $_GET['action']  && 'styles' != $_GET['action'] ) echo ' nav-tab-active'; ?>"><?php esc_html_e( 'Rules' ); ?></a>
                    <a href="<?php echo esc_url( add_query_arg( array( 'action' => 'settings' ), admin_url( 'admin.php?page=tbc' ) ) ); ?>" class="nav-tab<?php if ( $settings_page ) echo ' nav-tab-active'; ?>"><?php esc_html_e( 'Settings' ); ?></a>
                    <a href="<?php echo esc_url( add_query_arg( array( 'action' => 'styles' ), admin_url( 'admin.php?page=tbc' ) ) ); ?>" class="nav-tab<?php if ( $styles_page ) echo ' nav-tab-active'; ?>"><?php esc_html_e( 'CSS customisation' ); ?></a>
                </h2>
            </div>
        </div>
        <?php
        if ( $settings_page ) {
             // Генерируется содержимое таба Setting
            require_once plugin_dir_path( __FILE__ ) . 'class-tab-setting.php';
            $Setting = new Setting();
        } else if ( $styles_page ) {
             // Генерируется содержимое таба CSS customisation
            require_once plugin_dir_path( __FILE__ ) . 'class-tab-style.php';
            $Style = new Style();
        } else {
            // Подключается таблица для вывода списка Rules
            if( class_exists('WP_List_Table') == FALSE ) {
                require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
            }
             // Генерируется содержимое таба Rules
            require_once plugin_dir_path( __FILE__ ) . 'class-tab-rule.php';
            $Rule = new Rule();
        }
    }
    
    function tbc_style_wp_head() {
        $text_css = $this->custom_style_query();
        $text_css = preg_replace_callback( '/{\s*bloginfo:([^\s}]*)\s*}/', $this->mycallback, $text_css );
        echo '<style type="text/css">';	
            echo $text_css;
        echo '</style>';
    }
    
    function custom_style_query() {
        return get_option( $this->custom_style_key() );
    }
    
    function custom_style_key() {
        return 'tbc_custom_style_wp_head';
    }
    
    function mycallback($match) {
        $key = $match[1];
        $val = get_bloginfo($key);
        return $val;
    }
}
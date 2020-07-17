<?php
/*
Plugin Name: Text by country
Description: Показываем текст по правилам
Version: 1.0
Author: Maxim Grishan
Commit 2
*/
    
class tbcInit {
    
    private $options_general;
    private $options_social;
    private $options_footer;
    
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'tbc_add_pages' ) );
        add_action( 'admin_init', array( $this, 'tbc_options_init' ) );
    }

    public function tbc_add_pages() {
        add_menu_page( 'TBC', 'TBC', 'manage_options' , 'tbc', array($this, 'tbc_page'), 'dashicons-admin-site-alt2', 8 );
    }
    
    public function tbc_page() {
        $this->options_general = get_option( 'tbc_general' );
		$this->options_social  = get_option( 'tbc_social' );
		$this->options_footer  = get_option( 'tbc_footer' );
                
        $social_Screen = ( isset( $_GET['action'] ) && 'social' == $_GET['action'] ) ? true : false; //check page: social ? true : false
        
        $footer_Screen = ( isset( $_GET['action'] ) && 'footer' == $_GET['action'] ) ? true : false; //check page: footer ? true : false
        ?>
        
        <div class="wrap">
            <h1>TBC Setting</h1>
            <h2 class="nav-tab-wrapper">
				<a href="<?php echo admin_url( 'admin.php?page=tbc' ); ?>" class="nav-tab<?php if ( ! isset( $_GET['action'] ) || isset( $_GET['action'] ) && 'social' != $_GET['action']  && 'footer' != $_GET['action'] ) echo ' nav-tab-active'; ?>"><?php esc_html_e( 'General' ); ?></a>
				<a href="<?php echo esc_url( add_query_arg( array( 'action' => 'social' ), admin_url( 'admin.php?page=tbc' ) ) ); ?>" class="nav-tab<?php if ( $social_Screen ) echo ' nav-tab-active'; ?>"><?php esc_html_e( 'Social' ); ?></a>
				<a href="<?php echo esc_url( add_query_arg( array( 'action' => 'footer' ), admin_url( 'admin.php?page=tbc' ) ) ); ?>" class="nav-tab<?php if ( $footer_Screen ) echo ' nav-tab-active'; ?>"><?php esc_html_e( 'Footer' ); ?></a>
			</h2>
        	<form method="post" action="options.php">
                
                <?php
                if ( $social_Screen ) {
                    settings_fields( 'tbc_social' );
                    do_settings_sections( 'tbc-setting-social' );
                    submit_button();
				} elseif ( $footer_Screen ) {
					settings_fields( 'tbc_footer' );
					do_settings_sections( 'tbc-setting-footer' );
					submit_button();
				} else { 
					settings_fields( 'tbc_general' );
					do_settings_sections( 'tbc-setting-admin' );
					submit_button();
				}
                ?>
                
			</form>
        </div>

        <?php
    }
    
    public function tbc_options_init() {
        register_setting (
            'tbc_general',
            'tbc_general',
            array( $this, 'sanitize' )
        );

        add_settings_section (
            'setting_section_id',
            'All Settings',
            array( $this, 'print_section_info' ),
            'tbc-setting-admin'
        );
        
        add_settings_field (
            'logo_image', 
            'Logo Image', 
            array( $this, 'logo_image_callback' ), 
            'tbc-setting-admin', 
            'setting_section_id'
        );		
	}
    
    //вывод доп инфы
    public function print_section_info(){
        echo 'print_section_info';
	}
    
    //обработчик поля link fb
    public function fb_url_callback() {
        printf (
            '<input type="text" id="fb_url" name="tbc_social[fb_url]" value="%s" />',
            isset( $this->options_social['fb_url'] ) ? esc_attr( $this->options_social['fb_url']) : ''
        );
    }
    
    //обработчик поля hide themes
    public function hide_more_themes_callback(){
        printf (
            '<input type="checkbox" id="hide_more_themes" name="tbc_footer[hide_more_themes]" value="yes" %s />',
            (isset( $this->options_footer['hide_more_themes'] ) && $this->options_footer['hide_more_themes'] == 'yes') ? 'checked' : ''
        );
    }
    
    //обработчик поля logo img
    public function logo_image_callback() {
        printf (
            '<input type="text" name="tbc_general[logo_image]" id="logo_image" value="%s"> <a href="#" id="logo_image_url" class="button" > Select </a>',
            isset( $this->options_general['logo_image'] ) ? esc_attr( $this->options_general['logo_image']) : ''
        );  
    }
    
    //обрабатываем поля
    public function sanitize( $input )  {
        $new_input = array();
        if( isset( $input['fb_url'] ) )
            $new_input['fb_url'] = sanitize_text_field( $input['fb_url'] );
      
        if( isset( $input['hide_more_themes'] ) )
            $new_input['hide_more_themes'] = sanitize_text_field( $input['hide_more_themes'] );
       
        if( isset( $input['logo_image'] ) )
            $new_input['logo_image'] = sanitize_text_field( $input['logo_image'] );

        return $new_input;
    }
    
}

defined('ABSPATH') or die('No script!');

if( is_admin() == TRUE ) {
    new tbcInit();
}
<?php

class Setting
{
    function setting() {
        $updated = false;
        
        // Вызова обработчика для сохранения изменений
        if( $_POST['action'] == 'submit_setting' ) {
            $tbc_debug_mode = $_POST[ 'tbc_debug_mode' ];
            $this->tbc_debug_mode_update($tbc_debug_mode);
            
            $tbc_view_mode = $_POST[ 'tbc_view_mode' ];
            $this->tbc_view_mode_update($tbc_view_mode);
            
            $tbc_cloud_flare = $_POST[ 'tbc_cloud_flare' ];
            $this->tbc_cloud_flare_update($tbc_cloud_flare);
            
            $updated = true;
        }
        
        $tbc_cloud_flare = $this->tbc_cloud_flare();
        if ( $tbc_cloud_flare == '' ) $tbc_cloud_flare = 'true';
          else if ( $tbc_cloud_flare == 'true' ) $checked_cloud_flare = 'checked';
        
        $tbc_debug_mode = $this->tbc_debug_mode();
        if ( $tbc_debug_mode == '' ) $tbc_debug_mode = 'true';
          else if ( $tbc_debug_mode == 'true' ) $checked = 'checked';
        
        $tbc_view_mode = $this->tbc_view_mode();
        if ( $tbc_view_mode == 'none' ) {
            
            $tbc_view_none_cheched = 'checked';
            
        } else if ( $tbc_view_mode == 'top' ) {
            
            $tbc_view_top_cheched = 'checked';
            
        } else if ( $tbc_view_mode == 'bottom' ) {
            
            $tbc_view_bottom_cheched = 'checked';
            
        }
        
        if ($updated) {
            echo sprintf("<div class='updated'><p><strong>%s</strong></p></div>",
            __('Options saved.', 'mt_trans_domain' ));
        }
        
        echo '<div class="wrap">';
            echo '<h2>' . __( "Setting TBC" ) . '</h2>';
            echo '<div class="tbc-container">';
                echo sprintf("<form method='post' action='%s'>",$_SERVER['REQUEST_URI']);
                wp_nonce_field('update-options');
                    echo "<table class='form-table'>";
        
                        echo "<tr valign='top'>";
                            echo "<th scope='row'>";
                                echo __( "Debug mode" );
                            echo "</th>";
                            echo "<td>";
                                echo sprintf('<input type="checkbox" id="tbc_debug_mode" name="tbc_debug_mode" %s value="%s" />', $checked, $tbc_debug_mode);
                            echo "</td>";
                        echo "</tr>";
        
                        echo "<tr valign='top'>";
                            echo "<th scope='row'>";
                                echo __( "View mode" );
                            echo "</th>";
                            echo "<td>";
                                echo '<label>';
                                    echo sprintf('<input type="radio" name="tbc_view_mode" %s value="%s" />', $tbc_view_none_cheched, 'none');
                                echo __( "None" ) . '</label>';
                                echo '<br>';
        
                                echo '<label>';
                                    echo sprintf('<input type="radio" name="tbc_view_mode" %s value="%s" />', $tbc_view_top_cheched, 'top');
                                echo __( "Top of page" ) . '</label>';
                                echo '<br>';
        
                                echo '<label>';
                                    echo sprintf('<input type="radio" name="tbc_view_mode" %s value="%s" />', $tbc_view_bottom_cheched, 'bottom');
                                echo __( "End of page" ) . '</label>';
                            echo "</td>";
                        echo "</tr>";
        
                        echo "<tr valign='top'>";
                            echo "<th scope='row'>";
                                echo __( "CloudFlare for ip" );
                            echo "</th>";
                            echo "<td>";
                                echo sprintf('<input type="checkbox" id="tbc_cloud_flare" name="tbc_cloud_flare" %s value="%s" />', $checked_cloud_flare, $tbc_cloud_flare);
                            echo "</td>";
                        echo "</tr>";
        
                        echo "<tr valign='top'>";
                            echo "<th scope='row'>";
                                echo __( "Shortcode" );
                            echo "</th>";
                            echo "<td>";
                                echo '<p>To display the shortcode use the following construction <code>[tbc id="6"]</code> which allows you to display information according to a certain rule.</p><p> Value "id" you can see in the table Rules</p>';
                            echo "</td>";
                        echo "</tr>";
                    echo "</table>";
                    echo "<button class='button button-primary button-large tbc-btn' name='action' value='submit_setting' type='submit'>" . __( 'Save' ) . "</button>";
                echo '</form>';
            echo '</div>';
        echo '</div>';
    }
    
    function tbc_debug_mode() {
        return get_option( $this->custom_key() );
    }
    
    function tbc_view_mode() {
        return get_option( $this->custom_key_view_mode() );
    }
    
    function tbc_cloud_flare() {
        return get_option( $this->custom_key_cloud_flare_mode() );
    }
    
    function custom_key() {
        return 'tbc_debug_mode';
    }
    
    function custom_key_view_mode() {
        return 'tbc_view_mode';
    }
    
    function custom_key_cloud_flare_mode() {
        return 'tbc_cloud_flare';
    }
    
    function tbc_debug_mode_update($text) {
        $text = stripslashes( $text );
        update_option( $this->custom_key(), $text );
    }
    
    function tbc_view_mode_update($text) {
        $text = stripslashes( $text );
        update_option( $this->custom_key_view_mode(), $text );
    }
    
    function tbc_cloud_flare_update($text) {
        $text = stripslashes( $text );
        update_option( $this->custom_key_cloud_flare_mode(), $text );
    }
}
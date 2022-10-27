<?php

class Style
{
    function style() {
        $updated = false;
        
        if( $_POST['action'] == 'submit_style' ) {
            $val = $_POST[ 'customstyle' ];
            $this->custom_style_update($val);
            $updated = true;
        }
        
        $val = $this->custom_style_query();
        
        if ($updated) {
            echo sprintf("<div class='updated'><p><strong>%s</strong></p></div>",
            __('Options saved.', 'mt_trans_domain' ));
        }
        
        echo "<div class='wrap'>";
            echo "<h2>Custom Style</h2>";
            echo sprintf("<form method='post' action='%s'>",$_SERVER['REQUEST_URI']);
            wp_nonce_field('update-options');
            echo "<table class='form-table'>";
              echo "<tr valign='top'>";
              echo "<td>";
              echo sprintf("<textarea cols='80' rows='20' name='customstyle'>%s</textarea>",$val);
              echo "</td>";
              echo "<td><hr /><p>The text you add will be between the tags <code>&lt;style>..&lt;/style></code> at the top of the html on every page of the site.</p><p>For example:<pre>.tbc_inner_white_text {\n  background: red;\n}</pre></p>";
              echo "<hr />";
              echo "<button class='button button-primary button-large tbc-btn' name='action' value='submit_style' type='submit'>" . __( 'Save' ) . "</button>";
              echo "</td>";
              echo "</tr>";
            echo "</table>";
            echo "</form>";
        echo "</div>";
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
    
    function custom_style_update($text) {
      $text = stripslashes( $text );
      update_option( $this->custom_style_key(), $text );
    }
}
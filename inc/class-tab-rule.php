<?php

class Rule
{
    public function rule() {
        // Проверка action массового удаления из таблицы. Нижнее или верхняя панель таблицы
        if ( $_REQUEST['action'] == '-1' ) $action_table = $_REQUEST['action2'];
            else $action_table = $_REQUEST['action'];
        
        // Действия сохранения данных, удаление одиночных данных, сохранение текста White/Black list
        switch ( $_REQUEST['act'] ) {
            case "save":
                $this->rule_set_country();
                break;
            case "delete":
                $this->rule_delete();
                break;
            case "save_text":
                $this->tbc_set_text();
                break;
        }
        
        // Действия массового удаления, редактирования таблицы
        switch ( $action_table ) {
            case "delete":
                $this->rule_delete_list();
                break;
            case "edit":
                $rule_edit_array = $this->rule_edit();
                break;
        }
        
        if ( count( $rule_edit_array ) != 0 ) {
            $rule_edit_array_country = implode(", ", maybe_unserialize( $rule_edit_array['country'] ) );
            $rule_edit_array_pages = maybe_unserialize( $rule_edit_array['pages'] );
        }

        ?>
        <div class="wrap">
            <div class="tbc-container">
                <form name="post_form" method="post" action="" enctype="multipart/form-data">
                    
                    <?php
                        // Выборка данных для блокирования выбранных селектов стран
                        global $wpdb, $table_prefix;
                        $table_name_rules = $wpdb->prefix . "tbc_rules";
                        $query_rules = "select country from " . $table_name_rules;
                        $result_rules_active = $wpdb->get_results($query_rules, ARRAY_A);
                        
                        // Сбор данных в один массив, многомерный
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
                        
                        // Выборка всех стран, для отображения в селекте
                        $table_name = $wpdb->prefix . "tbc_country";
                        $query = "select * from " . $table_name;
                        $result_rules = $wpdb->get_results( $query, ARRAY_A );
                        
                        $args = array(
                            'post_type'      => array('page', 'post'),
                            'post_status'    => 'publish',
                            'posts_per_page' => -1,
                            'fields'         => 'ids'
                        );
        
                        $query_pages = new WP_Query($args);
                        $pages_id = $query_pages->posts;
        
                        echo '<h2>White listed countries</h2>';
                        echo '<div class="form-field">';
                            echo '<select name="set_country[]" id="set_country" multiple="multiple" size="7">';
                                // Формируется селект всех стран, уже выбранным присваевается значение disabled
                                foreach( $result_rules as $key => $val ){
                                    if ( in_array( $val['id'], (array)$new_array_active ) ) { $dis='disabled=disabled'; } else { $dis=''; }
                                    echo '<option value="' . $val['id'] . '"' . $sel . $dis . '>' . $val['nicename'] . '</option>';
                                }
                            echo '</select>';
        
                            echo '<div class="tbc_action_country">';
                                echo '<a id="tbc_add_country" class="tbc_action button button-second button-large" > » </a>';
                                echo '<a id="tbc_remove_country" class="tbc_action button button-second button-large"> « </a>';
                            echo '</div>';
                            
                            echo '<select name="active_country[]" id="active_country" multiple="multiple" size="7">';
                                // Селект, для добавления нужных стран. При редактировании заполняется имеющимися значениями
                                if ( !empty($rule_edit_array_country) ) {
                                    $table_name = $wpdb->prefix . "tbc_country";
                                    $query = "select id, nicename from " . $table_name . " WHERE id IN (" . $rule_edit_array_country . ")";
                                    $result_country_array = $wpdb->get_results( $query, ARRAY_A );
                                    
                                    foreach( $result_country_array as $key => $item_country ){
                                        echo '<option value="' . $item_country['id'] . '" selected>' . $item_country['nicename'] . '</option>';
                                    }
                                }
                            echo '</select>';
                            
                        echo '</div>';
        
                        echo '<h2>Effected pages</h2>';
                        echo '<div class="form-field">';
                            echo '<select name="set_pages[]" id="set_pages" multiple="multiple" size="7">';
                                // Формируется селект всех страниц, записей
                                foreach( $pages_id as $key => $val ){
                                    echo '<option value="' . $val . '"' . $sel . '>' . get_the_title($val) . '</option>';
                                }
                            echo '</select>';
        
                            echo '<div class="tbc_action_pages">';
                                echo '<a id="tbc_add_pages" class="tbc_action button button-second button-large" > » </a>';
                                echo '<a id="tbc_remove_pages" class="tbc_action button button-second button-large"> « </a>';
                            echo '</div>';
        
                            echo '<select name="active_pages[]" id="active_pages" multiple="multiple" size="7">';
                                // Селект, для добавления нужных страниц, записей. При редактировании заполняется имеющимися значениями
                                if ( !empty($rule_edit_array_pages) ) {
                                    foreach( $rule_edit_array_pages as $key => $item_pages ){
                                        echo '<option value="' . $item_pages . '" selected>' . get_the_title( $item_pages ) . '</option>';
                                    }
                                }
                            echo '</select>';
                        echo '</div>';

                        echo '<div class="form-field action-form-btn">';
                            echo '<input id="rule_action_save" type="submit" name="submit" value="Save rules" class="button button-primary button-large" />';
                            echo '<input type="hidden" name="act" value="save" />';
        
                            // Если режим редактирования, выводится ссылка для обратного перехода к созданию праавил
                            if ( $_REQUEST['action'] == 'edit' )
                                echo '<a href="?page='.$_REQUEST['page'].'" class="tbc-add-new-rules button button-second button-large">' . __("Add new Rules") . '</a>';
                        echo '</div>';
                    ?>
                    </form>
                    <hr>
                    <form name="post_form" method="post" action="" enctype="multipart/form-data">
                        <?php
                        echo '<div class="form-field tbc-editor">';
                            $settings = array(
                                'media_buttons' => 0,
                                'textarea_rows' => 7,
                                'tinymce' => array(
                                    'resize' => false,
                                    'wp_autoresize_on' => true
                                )
                            );
                            
                            // Область для ввода текста White List
                            $editor_id_white = "tbc_white_list";
                            $content_white_text = get_option ( 'tbc_white_list' );
                            echo '<div class="tbc_editor_container">';
                                echo '<h2>White list text</h2>';
                                wp_editor( $content_white_text, $editor_id_white, $settings );
                            echo '</div>';
                            
                            // Область для ввода текста Black List
                            $editor_id_black = "tbc_black_list";
                            $content_black_text = get_option ( 'tbc_black_list' );
                            echo '<div class="tbc_editor_container">';
                                echo '<h2>Black list text</h2>';
                                wp_editor( $content_black_text, $editor_id_black, $settings );
                            echo '</div>';
        
                        echo '</div>';
        
                        echo '<div class="form-field action-form-btn">';
                            echo '<input type="submit" name="submit" value="Save text" class="button button-primary button-large" />';
                            echo '<input type="hidden" name="act" value="save_text" />';
                        echo '</div>';
        
                        echo '<hr>';
                    ?>
                </form>
            </div>
        </div>
        <?php
            // Добавление таблицы с выводом правил
            require_once plugin_dir_path( __FILE__ ) . 'class-table-rule.php';
            $tbc_table = new tbcTable();
            $tbc_table->prepare_items(); ?>

            <div class="wrap">
                <form name="post_form" method="post" action="" enctype="multipart/form-data">
                    <?php $tbc_table->display(); ?>
                </form>
            </div>

        <?php
    }
    
    function rule_set_country() {
		$new_list_country = $_REQUEST['active_country'];
		$new_list_pages = $_REQUEST['active_pages'];
		$rule_id = $_REQUEST['rule'];
        
        global $wpdb;
        $table_name_rules = $wpdb->prefix . "tbc_rules";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        
        // Сериализованный массив для добавления в бд, как активные страны данного правила
        $serialized_value_country = maybe_serialize( $new_list_country );
        // Сериализованный массив для добавления в бд, как активные страницы, записи данного правила
        $serialized_value_pages = maybe_serialize( $new_list_pages );            

        if ( ( !empty($rule_id) ) && ( $_REQUEST['action'] == 'edit' ) ) {
            $query = "UPDATE `" . $table_name_rules . "` SET `country`= '" . $serialized_value_country ."', `pages`  = '" . $serialized_value_pages . "' WHERE id = " . $rule_id;            
            $wpdb->query( $query );
        } else {
            $query = "INSERT INTO `" . $table_name_rules . "` (`id`, `country`, `pages`) VALUES ('', '" . $serialized_value_country . "', '" . $serialized_value_pages . "')";
            $wpdb->query( $query );
        }
        
        echo '<div class="updated" id="message" style="position:relative; clear:both;"><p>' . __("Save successfully.") . '</p></div>';
        
        return $result_update;
    }
    
    function rule_delete() {
        $option_name1 = 'set_country' ;
		$rule_id = $_REQUEST['rule'];
        
        global $wpdb;
        $table_name_rules = $wpdb->prefix . "tbc_rules";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $wpdb->delete( $table_name_rules, array( 'id' => $rule_id ) );
        
        echo '<div class="updated" id="message" style="position:relative; clear:both;"><p>' . __("Delete successfully.") . '</p></div>';
    }
    
    function rule_delete_list() {
        $id_delete = implode( ', ', $_REQUEST['id'] );
        
        global $wpdb;
        $table_name = $wpdb->prefix . "tbc_rules";
        $query = "DELETE FROM " . $table_name . " WHERE ID IN(" . $id_delete . ")";
        $wpdb->query( $query );
        
        echo '<div class="updated" id="message" style="position:relative; clear:both;"><p>' . __("Delete successfully.") . '</p></div>';
    }
    
    function rule_edit() {   
        $rule_id = $_REQUEST['rule'];
        
        global $wpdb;
        $table_name = $wpdb->prefix . "tbc_rules";
        $query = "select * from " . $table_name . " WHERE id = " . $rule_id . " LIMIT 1";
        $result_rules = $wpdb->get_results( $query, ARRAY_A );
                
        return $result_rules[0];
    }
    
    function tbc_set_text() {
        $option_name3 = 'tbc_white_list';
        $option_name4 = 'tbc_black_list';
        
        $new_white_list = $_REQUEST['tbc_white_list'];
		$new_black_list = $_REQUEST['tbc_black_list'];
        
        if ( !get_option('tbc_white_list') ) add_option( $option_name3, $new_white_list );
            else update_option( $option_name3, $new_white_list );
        
        if ( !get_option('tbc_black_list') ) add_option( $option_name4, $new_black_list );
            else update_option( $option_name4, $new_black_list );
    }
}
<?php

class tbcTable extends WP_List_Table
{
    /**
      * Создаём и выводим таблицу текущих rules
      */
    public function prepare_items() {
        $per_page = 5;
        $data = $this->table_data();
                        
        $this->set_pagination_args( array(
            'total_items' => count($data),
            'per_page'    => $per_page
        ));
        
        /**
          * Деление массива на части для пагинации
          */
        $data = array_slice(
            $data,
            ( ( $this->get_pagenum() - 1 ) * $per_page ),
            $per_page
        );
        
        $this->_column_headers = array(
            $this->get_columns(),
            $this->get_hidden_columns(),
        );
        
        $this->items = $data;
    }
    
    public function get_columns() {
        return array(
            'cb'		=> '<input type="checkbox" />',
            'id'		=> 'ID',
            'country'	=> 'Country',
            'pages'		=> 'Pages',
            'action'    => 'Action'
        );
    }
    
    public function get_hidden_columns() {
        return array();
    }
    
    private function table_data() {
        global $wpdb, $table_prefix;
        $table_name = $wpdb->prefix . "tbc_rules";
        $query = "select * from " . $table_name . " ORDER by id DESC";
        $result = $wpdb->get_results($query, ARRAY_A);
        
        return($result);
    }
    
    /**
     * Формируем колонки таблицы
     */
    public function column_default($item, $column_name ) {
        switch($column_name) {
			case 'id':
                return $item[$column_name];
			case 'country':
                $new_list_country = implode(", ", maybe_unserialize( $item[$column_name] ) );
                global $wpdb, $table_prefix;
                $table_name = $wpdb->prefix . "tbc_country";
                $query = "select nicename from " . $table_name . " WHERE id IN (" . $new_list_country . ")";
                $result = $wpdb->get_results($query, ARRAY_A);
                                
                $array_name_country = reset( ( call_user_func_array( 'array_merge_recursive', $result ) ) );
                
                if ( count($array_name_country) <= 1 ) return $array_name_country;
                else return implode(", ", maybe_unserialize( $array_name_country ) );
                
			case 'pages':
                $new_list_pages = maybe_unserialize( $item[$column_name] );
                $title_pages = array();
                foreach ( $new_list_pages as $title_page ) {
                    array_push( $title_pages, get_the_title( $title_page ) );
                }
                $list_title_pages = implode( ", ", $title_pages );
				return $list_title_pages;
            case 'action':
                return '';
            default:
				return print_r($item, true);
        }
    }
    
    function column_cb($item) {
		return '<input type="checkbox" name="id[]" value="'.$item['id'].'" />';
	}
    
    function column_action($item) {
		return $this->row_actions ( array(
			'edit'	 => '<a href="?page='.$_REQUEST['page'].'&action=edit&rule='.$item['id'].'">' . __("Edit") . '</a>',
			'delete' => '<a href="?page='.$_REQUEST['page'].'&act=delete&rule='.$item['id'].'">' . __("Delete") . '</a>',
		) );
	}
    
    function get_bulk_actions() {
		return array(
			'delete' => __('Delete')
		);
	}
}
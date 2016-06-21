<?php

namespace DevelopmentTools;

class Taxonomy {

	public $name;
    public $post_type;
	public $urlSlug;
    public $hierarchical;
	public $labelPlural;
	public $labelSingular;
	public $supports;
	public $excerptTitle;
	public $excerptHelp;

	public function __construct(){
		$this->hierarchical = true;
	}

	public function create(){
        $labels = array(
            'name' => __( $this->labelPlural ),
            'singular_name' => __( $this->labelSingular ),
            'search_items' =>  __( 'Search '.$this->labelPlural ),
            'all_items' => __( 'All '.$this->labelPlural ),
            'parent_item' => __( 'Parent '.$this->labelSingular ),
            'parent_item_colon' => __( 'Parent '.$this->labelSingular.':' ),
            'edit_item' => __( 'Edit '.$this->labelSingular ),
            'update_item' => __( 'Update '.$this->labelSingular ),
            'add_new_item' => __( 'Add New '.$this->labelSingular ),
            'new_item_name' => __( 'New '.$this->labelSingular.' Name' ),
            'menu_name' => __( $this->labelPlural ),
        );

        register_taxonomy($this->name,array($this->post_type), array(
            'hierarchical' => $this->hierarchical,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array( 'slug' => $this->urlSlug ),
        ));
	}

}
<?php

namespace DevelopmentTools;

class PostType {

	public $name;
	public $urlSlug;
	public $labelPlural;
	public $labelSingular;
	public $supports;
	public $excerptTitle;
	public $excerptHelp;
	public $iconCSSContent;
	public $taxonomies;
	public $customFields=array();

	public function __construct(){
		$this->supports = array(
			'title',
			'excerpt',
			'thumbnail',
			'page-attributes'
		);
		$this->taxonomies = array();
	}

	public function addCustomField($field){
		$this->customFields[] = $field;
	}

	public function create(){
		$registerPostArgs = array(
			'menu_icon' => '',
			'labels' => array(
				'name' => __( $this->labelPlural ),
				'menu_name' => __( $this->labelPlural ),
				'name_admin_bar' => __( $this->labelSingular ),
				'singular_name' => __( $this->labelSingular ),
				'add_new' => __( 'Add New' ),
				'add_new_item' => __( 'Add New '.$this->labelSingular ),
				'edit_item' => __( 'Edit '.$this->labelSingular ),
				'new_item' => __( 'New '.$this->labelSingular ),
				'view_item' => __( 'View '.$this->labelSingular ),
				'all_items' => __( 'All '.$this->labelPlural ),
				'search_items' => __( 'Search '.$this->labelPlural ),
				'parent_item_colon'  => __( 'Parent '.$this->labelPlural.':' ),
				'not_found'          => __( 'No '.$this->labelPlural.' found.' ),
				'not_found_in_trash' => __( 'No '.$this->labelPlural.' found in Trash.' )
			),
			'public' => true,
			'has_archive' => true,
			'rewrite' => array('slug' => $this->urlSlug),
			'supports' => $this->supports,
		);
		if(!empty($this->taxonomies)){
			$registerPostArgs['taxonomies'] = $this->taxonomies;
		}
		register_post_type($this->name, $registerPostArgs);
		//Text overrides
		add_filter('gettext', array($this,'_override_post_title_placeholder'));
		if($this->excerptTitle){
			add_filter('gettext',array($this,'_override_post_excerpt_title'));
		}
		if($this->excerptHelp){
			add_filter('gettext',array($this,'_override_post_excerpt_help'));
		}
		add_filter('gettext',array($this,'_override_featured_image_title'));
		add_filter('gettext',array($this,'_override_featured_image_link'));
		//Custom fields
		if(!empty($this->customFields)){
			add_action( 'add_meta_boxes_'.$this->name, array($this,'customFieldAddMetaBoxes') );
			add_action( 'save_post', array($this,'savePostCustomFields') );
		}
		//Custom icon
		if(!empty($this->iconCSSContent)){
			add_action( 'admin_head', array($this,'customMenuIconStyles') );
		}
	}

	public function customMenuIconStyles(){
		echo '<style>';
		echo '#adminmenu .menu-icon-'.$this->name.' div.wp-menu-image:before {';
		echo 'content: "'.$this->iconCSSContent.'"';
		echo '}';
		echo '</style>';
	}

	public function savePostCustomFields($post_id){
		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		// Check the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}

		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}
		//
		foreach($this->customFields as $customField){
			$fieldName = $customField['name'];
			if (isset( $_POST[$fieldName] ) ) {
				$postData = implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_POST[$fieldName] ) ) );
				update_post_meta( $post_id, $fieldName, $postData );
			}
		}

	}

	public function customFieldAddMetaBoxes(){
		foreach($this->customFields as $customField){
			switch ($customField['type']) {
				case 'textarea':
					$outputCallback = 'customFieldOutputTextArea';
					break;
				case 'select':
					$outputCallback = 'customFieldOutputSelect';
					break;
				default:
					$outputCallback = 'customFieldOutputText';
					break;
			}
			add_meta_box(
				$customField['name'].'_container',
				__( $customField['label']),
				array($this, $outputCallback)
			);
		}
	}

	public function customFieldOutputText($post, $metabox){
		$fieldName = preg_replace('/_container$/', '', $metabox['id']);
		$value = get_post_meta( $post->ID, $fieldName, true );
		echo '<input type="text" style="width:98%;" id="'.$fieldName.'" name="'.$fieldName.'" value="'.esc_attr( $value ).'" />';
	}

	public function customFieldOutputTextArea($post, $metabox){
		$fieldName = preg_replace('/_container$/', '', $metabox['id']);
		$value = get_post_meta( $post->ID, $fieldName, true );
		echo '<textarea style="width:98%; height:4em;" id="'.$fieldName.'" name="'.$fieldName.'" />'.esc_attr( $value ).'</textarea>';
	}

	public function customFieldOutputSelect($post, $metabox){
		$fieldName = preg_replace('/_container$/', '', $metabox['id']);
		$value = get_post_meta( $post->ID, $fieldName, true );
		$options = array();
		if(!empty($metabox['callback']) && !empty($metabox['callback'][0]->customFields)){
			foreach($metabox['callback'][0]->customFields as $customField){
				if($customField['name'] == $fieldName && !empty($customField['options'])){
					$options = $customField['options'];
				}
			}
		}
		//print_r();
		echo '<select style="width:98%; height:4em;" id="'.$fieldName.'" name="'.$fieldName.'" />';
		foreach($options as $optionK=>$optionV){
			$selected = '';
			if($value == $optionK){
				$selected = 'selected="selected"';
			}
			echo '<option value="'.$optionK.'" '.$selected.'>'.$optionV.'</option>';
		}
		echo '</select>';
		esc_attr( $value ).'</select>';
	}

	public function _override_post_title_placeholder($input){
		global $post_type;
		if( is_admin() && 'Enter title here' == $input && $post_type == $this->name )
			return $this->labelSingular.' Title';
		return $input;
	}
	function _override_post_excerpt_title( $input ) {
		global $post_type;
		if( is_admin() && 'Excerpt' == $input && $post_type == $this->name )
			return $this->excerptTitle;
		return $input;
	}
	function _override_post_excerpt_help( $input ) {
		global $post_type;
		if( is_admin() && 'Excerpts are optional hand-crafted summaries of your content that can be used in your theme. <a href="https://codex.wordpress.org/Excerpt" target="_blank">Learn more about manual excerpts.</a>' == $input && $post_type == $this->name )
			return $this->excerptHelp;
		return $input;
	}
	function _override_featured_image_title( $input ) {
		global $post_type;
		if( is_admin() && 'Featured Image' == $input && $post_type == $this->name )
			return $this->labelSingular.' Image';
		return $input;
	}
	function _override_featured_image_link( $input ) {
		global $post_type;
		if( is_admin() && 'Set featured image' == $input && $post_type == $this->name )
			return 'Set '.$this->labelSingular.' Image';
		return $input;
	}
}
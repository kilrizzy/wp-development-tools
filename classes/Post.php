<?php

namespace DevelopmentTools;

class Post
{

    public $type;
    public $title;
    public $slug;
    public $excerpt;
    public $status;
    public $date;

    public function __construct(){
        $this->status = 'publish';
    }

    public function generateSlug(){
        $this->slug = sanitize_title($this->title);
    }
    public function setDate($date){
        $this->date = strtotime($date);
    }
    public function insert()
    {
        if(empty($this->slug)){
            $this->generateSlug();
        }
        $post = array(
            'post_content'   => '',
            'post_name'      => $this->slug,
            'post_title'     => $this->title,
            'post_status'    => $this->status,
            'post_type'      => $this->type,
            'post_excerpt'   => $this->excerpt,
        );
        if(!empty($this->date)){
            $post['post_date'] = date('Y-m-d H:i:s',$this->date);
            $post['post_date_gmt'] = gmdate('Y-m-d H:i:s',$this->date);
        }
        return wp_insert_post($post);
    }

}
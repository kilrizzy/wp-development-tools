<?php

class Template
{

    function get($url,$data=array()){
        //set dynamic vars
        foreach($data as $dk=>$dv){
            $$dk = $dv;
        }
        //load url
        ob_start();
        include($url);
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

}
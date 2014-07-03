<?php

class Tools {

     private $plugin_domain = null;

     public function __construct($plugin_domain=null){
          if(!empty($plugin_domain)){
               $this->plugin_domain=$plugin_domain;
          }
     }

     public function translate(){
          $arg_list=func_get_args();
          if(!empty($arg_list)){
               $string=array_shift($arg_list);

               if(empty($plugin_domain)){
                    $return_string="__(";
               }
               else{
                    $return_string="__d('{$plugin_domain}',";
               }

               $return_string.="'{$string}'";

               if(!empty($arg_list)){
                    $return_string.=','.implode(',',$arg_list);
               }

               $return_string.=")";
          }
          else{
               $return_string='';
          }

          return $return_string;
     }

}

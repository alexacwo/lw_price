<?php 
if(is_admin()){
    // Add pagination class
    if( ! class_exists('pagination') ){
        include("helpers/pagination.class.php");
    }
}
?>
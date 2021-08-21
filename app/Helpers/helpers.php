<?php

function my_app(){
    return app();
}

function is_app_env($str=['local']){
    return my_app()->environment($str);
}

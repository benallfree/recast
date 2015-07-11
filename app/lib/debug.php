<?php
  
if(!function_exists('dd'))
{
  function dd($val)
  {
    var_dump($val);
    die;
  }
}

if(!function_exists('log_looks'))
{
  add_action( 'all', 'log_hooks');
  $_log_hooks = array();
  function log_hooks( $tag ) {
    if(!WP_DEBUG) return;
    global $_log_hooks;
    if(isset($_log_hooks[$tag])) return;
    $_log_hooks[$tag]=true;
    error_log($tag);
  }
}
<?php
  
add_action('init', 'recast_less_init');
function recast_less_init() {
  $parser = new Less_Parser();
  $parser->parseFile(dirname(__FILE__)."/../less/app.less", plugins_url("recast/css") );
  file_put_contents(dirname(__FILE__)."/../recast.css", $parser->getCss());
}

add_action('wp_enqueue_scripts', 'recast_less_wp_enqueue_style',99);
function recast_less_wp_enqueue_style()
{
  wp_enqueue_style('recast', plugins_url("recast/app/recast.css"));
}

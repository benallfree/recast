<?php

require_once('lib/compat.php');
require_once('lib/debug.php');

require_once('classes/Recast.php');

add_action('init', 'recast_init');

function recast_init()
{
  if(is_admin() && current_user_can('switch_themes'))
  {
  }
}



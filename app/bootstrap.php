<?php

/* Version check */
if (version_compare(PHP_VERSION, '5.3.0') < 0) {
  add_action('admin_notices', 'recast_bad_version');
  function recast_bad_version()
  {
    ?>
    <div class="error">
        <p><?php _e( 'PHP 5.3 is required to run Recast.', 'recast' ); ?></p>
    </div>
    <?php
  }
  return;
}

define('RECAST_QS_SCOPE', '_rc');
define('RECAST_EPISODE_LIMIT', 50);

require(dirname(__FILE__)."/../vendor/autoload.php");

new Recast();

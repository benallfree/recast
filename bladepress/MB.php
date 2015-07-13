<?php
  
/* Mini Blade */
class MB
{
  static function make($fname, $args)
  {
    $s = file_get_contents($fname);
    $s = preg_replace('/@foreach(.*?)\n/', "<?php foreach\$1: ?>\n" , $s);
    $s = preg_replace('/@endforeach\n/', "<?php endforeach; ?>\n" , $s);
    $s = preg_replace('/@if(.*?)\n/', "<?php if\$1: ?>\n" , $s);
    $s = preg_replace('/@else\n/', "<?php else: ?>\n" , $s);
    $s = preg_replace('/@endif\n/', "<?php endif; ?>\n" , $s);
    $s = preg_replace("/{{{(.*?)}}}/", '<?php echo(htmlentities($1)) ?>', $s);
    $s = preg_replace("/{{(.*?)}}/", '<?php echo($1) ?>', $s);
    $tmpfname = tempnam(sys_get_temp_dir(), "recast_");
    file_put_contents($tmpfname, $s);
    add_filter( 'wp_audio_extensions', 'MB::trust_audio_extensions'));
    $content = self::sandbox($tmpfname, $args);
    unlink($tmpfname);
    return do_shortcode($content);
  }
  
  static function trust_audio_extensions($ext)
  {
    remove_filter( current_filter(), __FUNCTION__ );
    $ext[] = '';
    return $ext;
  }
  
  static function sandbox($__fname, $__vars)
  {
    extract($__vars);
    ob_start();
    require($__fname);
    return ob_get_clean();
  }
}
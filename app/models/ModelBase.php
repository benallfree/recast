<?php
use \Michelf\Markdown;

  
class ModelBase
{
  static function all($args=array())
  {
    $class = get_called_class();
    $defaults = array(
      'posts_per_page'   => -1,
      'offset'           => 0,
      'post_type'        => call_user_func("$class::post_type"),
    );
    $args = array_merge($defaults, $args);
    $posts = get_posts($args);
    $res = array();
    foreach($posts as $post)
    {
      
      $res[] = new $class($post);
    }
    return $res;
  }
  
  static function post_type()
  {
    return strtolower(get_called_class());
  }
  
  static function find($id)
  {
    $objs = self::all(array(
      'p'=>$id,
    ));
    if(count($objs)==0) return null;
    return $objs[0];
  }

  function __construct($post)
  {
    $this->_post = $post;
    $this->_attributes = array();
    $this->_cache = array();
  }
  
  function __get($name)
  {
    if(isset($this->_cache[$name])) return $this->_cache[$name];
    if(method_exists($this, $name))
    {
      return $this->_cache[$name] = $this->$name();
    }
    if(isset($this->_post->$name)) return $this->_post->$name;
    return $this->_meta($name);
  }
  
  function _meta($name)
  {
    if(isset($this->_attributes[$name])) return $this->_attributes[$name];
    return $this->_attributes[$name] = get_post_meta($this->_post->ID, $name, true);
  }
  
  function md_link($s)
  {
    return preg_replace('/https?:\/\/[\w\-\.!~#?&=+\*\'"(),\/]+/','[$0]($0)',$s);
  }
  
  function html_link($s)
  {
    return preg_replace('/https?:\/\/[\w\-\.!~#?&=+\*\'"(),\/]+/','<a href="$0">$0</a>',$s);
  }
  
  function md2html($s)
  {
    $s = $this->md_link($s);
    return Markdown::defaultTransform($s);
  }
}
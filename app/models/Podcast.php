<?php
class Podcast extends ModelBase
{
  function episodes($limit = null)
  {
    $args = array(
      'meta_key'=>'publish_date',
      'orderby'=>'meta_value_num',
      'meta_query'=>array(
        array(
          'key'=>'podcast_id',
          'value'=>$this->ID,
          'type'=>'NUMERIC',
        ),
      ),
      'posts_per_page'=>RECAST_EPISODE_LIMIT,
      'offset'=>0,
    );
    $episodes = Episode::all($args);
    
    return $episodes;
  }
  
  function podcast_logo_url()
  {
    $v = $this->_meta('podcast_logo_url');
    if($v) return $v;
    return plugins_url('recast/app/images/podcast.jpg');
  }
  
  function description_html()
  {
    return $this->md2html($this->description);
  }
}
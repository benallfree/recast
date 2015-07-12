<?php
use Carbon\Carbon;
use Html2Text\Html2Text;

class PublisherUpdater
{
  function update($post_id)
  {
    $this->update_publisher(get_post($post_id));
  }


  function v()
  {
    $args = func_get_args();
    $target = array_shift($args);
    $xml = array_shift($args);
    foreach($args as $arg)
    {
      $parts = explode(':',$arg);
      if(count($parts)==1)
      {
        $v = $xml->$parts[0]->__toString();
      }
      if($v) break;
    }
    if($v)
    {
      $target = $v;
    }
  }

  function update_episodes($parent_id, $rss)
  {
    foreach($rss->channel->item as $item)
    {
      if(!$item->enclosure) continue;
      $enclosure = $item->enclosure->attributes();
      $pubDate = new Carbon($item->pubDate->__toString());
      $arr = array(
        'parent_id'=>$parent_id,
        'title'=>$this->sanitize($item->title->__toString()),
        'pubDate'=>$pubDate->format('U'),
        'guid'=>$item->guid->__toString(),
        'link'=>$item->link->__toString(),
        'description'=>$this->sanitize($item->description->__toString()),
        'enclosure'=>array(
          'length'=>$enclosure['length']->__toString(),
          'type'=>$enclosure['type']->__toString(),
          'url'=>$enclosure['url']->__toString(),
        ),
        'itunes'=>array(
          'image'=>'',
          'duration'=>$item->children('itunes', true)->duration->__toString(),
          'explicit'=>$item->children('itunes', true)->explicit->__toString(),
          'keywords'=>$this->sanitize($item->children('itunes', true)->keywords->__toString()),
          'subtitle'=>$this->sanitize($item->children('itunes', true)->subtitle->__toString()),
        ),
        'raw'=>$item->asXml(),
      );
      $node = $item->children('itunes', true)->image; // Needed to force evaluation
      if($node)
      {
        $itunes_image = $node->attributes(); 
        $arr['itunes']['image'] = $itunes_image['href']->__toString();
      }
      $items[] = $arr;
    }

    usort($items, function($a, $b) {
      if($a['pubDate']==$b['pubDate']) return 0;
      if($a['pubDate'] < $b['pubDate']) return 1;
      return -1;
    });
    
    $items = array_slice($items, 0, RECAST_EPISODE_LIMIT);
    
    for($i=0;$i<count($items);$i++)
    {
      self::process_episode($items[$i]);
    }
    $args = array(
    	'posts_per_page'   => -1,
    	'offset'           => 0,
    	'post_type'        => 'episode',
    	'meta_key'=>'podcast_id',
    	'meta_value'=>$parent_id,
    );
    $q = new WP_Query($args);
    update_post_meta($parent_id, 'episode_count', $q->found_posts);
  }
  
  static function process_episode($item)
  {
    $parent_id = $item['parent_id'];

    Recast::log("Processing episode {$item['title']} for parent {$parent_id}");
    $args = array(
    	'posts_per_page'   => -1,
    	'post_type'        => 'episode',
    	'meta_key'=>'guid',
    	'meta_value'=>$item['guid'],
    );
    $posts = get_posts( $args );
    if(count($posts)>0)
    {
      $post = $posts[0];
    } else {
      $post_id = wp_insert_post( array(
        'post_title'=>$item['title'],
        'post_status'=>'publish',
        'post_type'=>'episode',
      ));
      $post = get_post($post_id);
    }
    $post->post_title = $item['title'];
    wp_update_post($post);
    
    $episode_info = array(
      'podcast_id'=>$parent_id,
      'guid'=>$item['guid'],
      'title'=>$item['title'],
      'description'=>$item['description'],
      'mp3_url'=>$item['enclosure']['url'],
      'duration'=>$item['itunes']['duration'],
      'publish_date'=>$item['pubDate'],
      'episode_url'=>$item['link'],
      'summary'=>$item['itunes']['subtitle'],
      'episode_image_url'=>$item['itunes']['image'],
      'raw_feed'=>$item['raw'],
    );
    foreach($episode_info as $k=>$v)
    {
      update_post_meta($post->ID, $k, $v);
    }
  }
    
  function tag($t)
  {
    $t = preg_quote($t, '/');
    return '/<\s*'.$t.'\s*>/';
  }
  
  function sanitize($v)
  {
    if(is_object($v)) return $v;
    $v = trim($v);
    if(!$v) return '';
    if(preg_match($this->tag('p'), $v))
    {
      $markdown = new HTML_To_Markdown($v, array('strip_tags'=>true, 'remove_nodes'=>'img'));
      $v = $markdown->output();
      $v = preg_replace('/\[\s*\]/', '[link]', $v);
    } else {
      $v = strip_tags($v);
      $v = preg_replace("/\n/", "  \n", $v); // Convert line breaks to markdown style
    }
    $v = trim($v, " \t\n\r\0\x0B\xA0\xC2");
    return $v;
  }

  function update_publisher($post)
  {
    $rss_url = get_post_meta($post->ID, 'feed_url', true);
    
    Recast::log($rss_url);
    
    if(!$rss_url) return;
    $xml = file_get_contents($rss_url);

    libxml_use_internal_errors(true);
    try { 
      $rss = new SimpleXmlElement($xml); 
    } catch(Exception $e) {
      echo("Exception\n");
      continue;
    }
    $err = libxml_get_errors();
    if(count($err)>0)
    {
      $should_skip_feed = false;
      foreach($err as $e)
      {
        if($e->level != LIBXML_ERR_FATAL) continue;
        self::log($e);
        $should_skip_feed = true;
      }
      if($should_skip_feed) return false;
    }
    
    /* Title */
    $title = $this->sanitize($rss->channel->title->__toString());
    if($title)
    {
      $post->post_title = $title;
      wp_update_post($post);
      update_post_meta($post->ID, 'title', $title);
    }
    
    /* Raw feed */
    update_post_meta($post->ID, 'raw_feed', $xml);
    
    /* Description */
    $content = $this->sanitize($rss->channel->children('itunes', true)->summary->__toString());
    if(!$content)
    {
      $content = $this->sanitize($rss->channel->description->__toString());
    }
    if($content)
    {
      update_post_meta($post->ID, 'description', $content);
    }
    
    /* Logo */
    $logo = $rss->channel->children('itunes', true)->image->__toString();
    if(!$logo && $rss->channel->image->url)
    {
      $logo = $rss->channel->image->url->__toString();
    }
    if($logo)
    {
      update_post_meta($post->ID, 'podcast_logo_url', $logo);
    }
    
    /* Tagline */
    $v = $this->sanitize($rss->channel->children('itunes', true)->subtitle->__toString());
    if($v)
    {
      update_post_meta($post->ID, 'tagline', $v);
    }
    
    /* Author */
    $v = $this->sanitize($rss->channel->children('itunes', true)->author->__toString());
    if($v)
    {
      update_post_meta($post->ID, 'author', $v);
    }
    
    /* RSS Redirect */
    $v = $rss->channel->children('itunes', true)->{"new-feed-url"}->__toString();
    if($v)
    {
      update_post_meta($post->ID, 'feed_url', $v);
    }
    
    /* Website URL */
    $v = $rss->channel->link->__toString();
    if($v)
    {
      update_post_meta($post->ID, 'website_url', $v);
    }
    $this->update_episodes($post->ID, $rss);    
  }
}

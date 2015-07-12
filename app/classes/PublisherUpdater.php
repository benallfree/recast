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

  function update_episodes($post_id, $rss)
  {
    foreach($rss->channel->item as $item)
    {
      if(!$item->enclosure) continue;
      $enclosure = $item->enclosure->attributes();
      $arr = array(
        'title'=>$this->sanitize($item->title->__toString()),
        'pubDate'=>new Carbon($item->pubDate->__toString()),
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
        )
      );
      if($item->children('itunes', true)->image==true)
      {
        $itunes_image = $item->children('itunes', true)->image->attribtues(); 
        $arr['itunes']['image'] = $itunes_image['href']->__toString();
        
        if($arr['itunes']['image']) dd($arr);
        
      }
      $items[] = $arr;
      
    }

    usort($items, function($a, $b) {
      if($a['pubDate']==$b['pubDate']) return 0;
      if($a['pubDate'] < $b['pubDate']) return 1;
      return -1;
    });
    
    $episodes = array();
    
    foreach($items as $item)
    {
      $episodes[] = array(
        'guid'=>$item['guid'],
        'title'=>$item['title'],
        'description'=>$item['description'],
        'mp3_url'=>$item['enclosure']['url'],
        'duration'=>$item['itunes']['duration'],
        'publish_date'=>$item['pubDate']->format('U'),
        'episode_url'=>$item['link'],
        'summary'=>$item['itunes']['subtitle'],
        'episode_image_url'=>$item['itunes']['image'],
      );
    }
    update_post_meta($post_id, 'feed_episodes', array_slice($episodes,0,25));
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
      update_post_meta($post->ID, 'title', $title);
    }
    
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
    if(!$logo)
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

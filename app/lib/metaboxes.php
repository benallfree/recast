<?php
  

add_action( 'cmb2_init', 'recast_podcast_metabox' );
add_action( 'cmb2_init', 'recast_episode_metabox' );

function recast_podcast_metabox() {
  $feed_info = new_cmb2_box( array(
      'id'            => 'feed_meta',
      'title'         => __( 'Feed Information', 'recast' ),
      'object_types'  => array( 'podcast', ), // Post type
      'context'       => 'normal',
      'priority'      => 'high',
      'show_names'    => true, // Show field names on the left
      // 'cmb_styles' => false, // false to disable the CMB stylesheet
      // 'closed'     => true, // Keep the metabox closed by default
  ) );
  
  $fields = array(
    'feed_url'=>array(
      'name' => 'Feed RSS URL',
      'desc' => 'Paste the podcast RSS feed URL here, and Recast will automatically populate the rest of the information.',
      'type' => 'text_url',
    ),
    'title'=>array(
      'name' => 'Feed title',
      'desc' => 'Auto-populated when feed is refreshed.',
      'type' => 'text',
      'attributes'=>array(
        'readonly' => 'readonly',
        'disabled' => 'disabled',
      )
    ),
    'description'=>array(
      'name' => 'Feed description',
      'desc' => 'Auto-populated when feed is refreshed.',
      'type' => 'textarea',
      'attributes'=>array(
        'readonly' => 'readonly',
        'disabled' => 'disabled',
      )
    ),
    'tagline'=>array(
       'name' => 'Podcast tagline',
       'desc' => 'Auto-populated when feed is refreshed.',
       'type'=>'text',
       'attributes'=>array(
         'readonly' => 'readonly',
         'disabled' => 'disabled',
       )
    ),
    'author'=>array(
      'name'=>'Podcast author',
      'desc' => 'Auto-populated when feed is refreshed.',
      'type'=>'text',
      'attributes'=>array(
        'readonly' => 'readonly',
        'disabled' => 'disabled',
      )
    ),
    'website_url'=>array(
      'name'=>'Website URL',
      'type' => 'text_url',
      'desc' => 'Auto-populated when feed is refreshed.',
      'attributes'=>array(
        'readonly' => 'readonly',
        'disabled' => 'disabled',
      )
    ),
    'podcast_logo_url'=>array(
      'name'=>'Podcast Logo URL',
      'type' => 'text_url',
      'desc' => 'Auto-populated when feed is refreshed.',
      'attributes'=>array(
        'readonly' => 'readonly',
        'disabled' => 'disabled',
      )
    ),
    'episode_count'=>array(
      'name'=>'Episode Count',
      'type' => 'text',
      'desc' => 'Auto-populated when feed is refreshed.',
      'attributes'=>array(
        'readonly' => 'readonly',
        'disabled' => 'disabled',
      )
    ),
    'raw_feed'=>array(
      'name'=>'Raw feed XML',
      'type'=>'textarea',
      'desc'=>'Auto-populated when feed is refreshed.',
      'attributes'=>array(
        'readonly' => 'readonly',
        'disabled' => 'disabled',
      ),
    ),
  );

  foreach($fields as $k=>$f)
  {
    $f['id'] = $k;
    $feed_info->add_field($f);
  }
}
  
  
function recast_episode_metabox() {
  $cmb = new_cmb2_box( array(
      'id'            => 'feed_episodes',
      'title'         => __( 'Episode', 'recast' ),
      'object_types'  => array( 'episode', ), // Post type
      'context'       => 'normal',
      'priority'      => 'high',
      'show_names'    => true, // Show field names on the left
      // 'cmb_styles' => false, // false to disable the CMB stylesheet
      //'closed'     => true, // Keep the metabox closed by default
  ) );
  
  $fields = array(
    'podcast_id'=>array(
      'name'=>'Podcast ID',
      'type'=>'text',
      'desc'=>'ID of the Podcast that owns this episode.',
    ),
    'guid'=>array(
      'name'=>'GUID',
      'type'=>'text',
    ),
    'title'=>array(
      'name'=>'Title',
      'type'=>'text',
    ),
    'description'=>array(
      'name'=>'Description',
      'type'=>'textarea',
    ),
    'summary'=>array(
      'name'=>'Summary',
      'type'=>'textarea',
    ),
    'mp3_url'=>array(
      'name'=>'MP3 URL',
      'type'=>'text_url',
    ),
    'duration'=>array(
      'name'=>'Duration',
      'type'=>'text',
    ),
    'publish_date'=>array(
      'name'=>'Publish Date',
      'type'=>'text_datetime_timestamp',
    ),
    'episode_url'=>array(
      'name'=>'Episode URL',
      'type'=>'text_url',
    ),
    'episode_image_url'=>array(
      'name'=>'Episode Image URL',
      'type'=>'text_url',
    ),
    'raw_feed'=>array(
      'name'=>'Raw feed XML',
      'type'=>'textarea',
    ),
    
  );
  
  foreach($fields as $k=>$f)
  {
    $f['id'] = $k;
    $f['desc']='Auto-populated when feed is refreshed.';
    $f['attributes'] = array(
      'readonly' => 'readonly',
      'disabled' => 'disabled',
    );
    $cmb->add_field($f);
  }
}
  
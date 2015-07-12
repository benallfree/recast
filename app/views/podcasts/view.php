<div class="single-publisher">
  <div class="logo">
    <img src="{{{$podcast->podcast_logo_url}}}">
  </div>
  <h1>{{{$podcast->title}}}</h1>
  <h2>{{{$podcast->tagline}}}</h2>
  <h3>with <i>{{{$podcast->author}}}</i></h3>
  <p style="margin-bottom: 0px">Subscribe:
    <a href="{{{$podcast->feed_url}}}">
      <img class=" size-thumbnail" style="  width: 44px;   top: 0px;   position: relative;" src="{{{plugins_url('recast/app/images/rss.png')}}}"/>
    </a>
  </p>
  @if($podcast->website_url)
    <p><a href="{{{$podcast->website_url}}}">{{{$podcast->website_url}}}</a></p>
  @endif
  <h2>About the Podcast</h2>
  {{$podcast->description}}
  <h2>Latest Episodes</h2>
  @foreach($podcast->episodes(50) as $episode)
    <div class="episode">
      <!-- {{$episode->ID}} -->
      <div class="metabox">
        @if($episode->episode_image_url)
          <img src='{{{$episode->episode_image_url}}}' />
        @endif
        <div class="info">
          <h3><a href='{{{$episode->episode_url}}}'>{{{$episode->title}}} ({{{$episode->duration}}})</a></h3>
          <p><i>Published {{{$episode->publish_date->toFormattedDateString()}}}</i></p>
          <p>[audio src="{{{$episode->mp3_url}}}"/]</p>
        </div>
      </div>
      {{$episode->description_html}}
      <div class="clearfix"></div>
    </div>
  @endforeach
</div>
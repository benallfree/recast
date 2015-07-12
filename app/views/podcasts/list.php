<div class="podcast-listing">
  @foreach($podcasts as $podcast)
      <a href="{{Recast::url(array('p'=>$podcast->ID))}}" class="podcast">
        <img src='{{{$podcast->podcast_logo_url}}}'  class="logo"/>
        <h1>{{{$podcast->title}}}</h1>
        <div class="episode-count">
          {{{$podcast->episode_count}}} episodes
        </div>
      </a>
  @endforeach
</div>
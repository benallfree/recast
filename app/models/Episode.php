<?php
use Carbon\Carbon;
  
class Episode extends ModelBase
{
  
  function publish_date()
  {
    $dt = Carbon::createFromTimeStampUTC($this->_meta('publish_date'));
    return $dt;
  }
  
  function description_html()
  {
    return $this->md2html($this->description);
  }
}
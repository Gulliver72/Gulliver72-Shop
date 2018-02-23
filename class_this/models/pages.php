<?php
/**
 * To make IDEs autocomplete happy
 *
 * @property int pages_id
 * @property string pages_title
 * @property string pages_html_text
 * @property int language_id
 * @property int sort_order
 * @property string status
 */

  class pages extends dbObject {
    protected $dbTable = "pages";
    protected $primaryKey = "pages_id";
    protected $dbFields = Array (
        'pages_id' => Array ('int'),
        'pages_title' => Array ('text'),
        'pages_html_text' => Array ('text'),
        'language_id' => Array ('int'),
        'sort_order' => Array ('int'),
        'status' => Array ('int'),
    );
//    protected $timestamps = Array ();
//    protected $relations = Array ();
  }
?>
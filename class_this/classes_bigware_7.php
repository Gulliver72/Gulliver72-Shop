<?php
/*
###################################################################################
  Bigware Shop 2.3
  Release Datum: 23.08.2015
  
  Bigware Shop
  http://www.bigware.de 

  Copyright (c) 2015 Bigware LTD
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2015  Bigware LTD

  Copyrightvermerke duerfen nicht entfernt werden.
  ------------------------------------------------------------------------
  Dieses Programm ist freie Software. Sie koennen es unter den Bedingungen
  der GNU General Public License, wie von der Free Software Foundation
  veroeffentlicht, weitergeben und/oder modifizieren, entweder gemaess Version 2 
  der Lizenz oder (nach Ihrer Option) jeder spaeteren Version.
  Die Veroeffentlichung dieses Programms erfolgt in der Hoffnung, dass es Ihnen
  von Nutzen sein wird, aber OHNE IRGENDEINE GARANTIE, sogar ohne die
  implizite Garantie der MARKTREIFE oder der VERWENDBARKEIT FUER EINEN
  BESTIMMTEN ZWECK. Details finden Sie in der GNU General Public License.
  
  Sie sollten eine Kopie der GNU General Public License zusammen mit diesem
  Programm erhalten haben. Falls nicht, schreiben Sie an die Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, USA.

  Infos:
  ------------------------------------------------------------------------
  Der Bigware Shop wurde vor vielen Jahren bereits aus dem bekannten Shopsystem osCommerce
  weiter- und neuentwickelt.
  Der Bigware Shop legt im hohen Masse Wert auf Bedienerfreundlichkeit, beinhaltet eine leichte
  Installation, viele neue professionelle Werkzeuge und zeichnet sich aus durch eine grosse 
  Community, die bei Problemen weiterhelfen kann.
  
  Der Bigware Shop ist auf jedem System lauffaehig, welches eine PHP Umgebung
  (ab PHP 4.1.3) und mySQL zur Verfuegung stellt und auf Linux basiert.
 
  Hilfe erhalten Sie im Forum auf www.bigware.de 
  
  -----------------------------------------------------------------------
  
 ##################################################################################




*/
?>
<?php
  class tableBox {
    var $table_border = '0';
    var $table_width = '100%';
    var $table_cellspacing = '0';
    var $table_cellpadding = '2';
    var $table_parameters = '';
    var $table_row_parameters = '';
    var $table_data_parameters = ''; 
    function __construct($contents, $direct_output = false, $bevor_table = '', $after_table = '') {
      $this->tableBox($contents, $direct_output = false, $bevor_table = '', $after_table = '');
    }
    function tableBox($contents, $direct_output = false, $bevor_table = '', $after_table = '') {
      global $PHP_SELF;
      $tableBox_string = '';
      // Tabelle Zentriert Fixiert Richy //
      //$tableBox_string .= '<div style="z-index:0;">';
      if ($bevor_table != '') {
        $tableBox_string .= $bevor_table;
      }
      $tableBox_string .= '<table class="table table-hover col-md-12">' . "\n";
      for ($i=0, $n=sizeof($contents); $i<$n; $i++) {
        if (isset($contents[$i]['form']) && go_not_null($contents[$i]['form'])) $tableBox_string .= $contents[$i]['form'] . "\n";
        $tableBox_string .= '  <tr';
        if (go_not_null($this->table_row_parameters)) $tableBox_string .= ' ' . $this->table_row_parameters;
        if (isset($contents[$i]['params']) && go_not_null($contents[$i]['params'])) $tableBox_string .= ' ' . $contents[$i]['params'];
        if ( $i == '0' && isset($contents['heading_tr_param']) && go_not_null($contents['heading_tr_param']) ) {
          $tableBox_string .= ' ' . $contents['heading_tr_param'];
        }
        $tableBox_string .= '>' . "\n";
        if (isset($contents[$i][0]) && is_array($contents[$i][0])) {
          $count_rowspan = '0';
          for ($x=0, $n2=sizeof($contents[$i]); $x<$n2; $x++) {
            if (isset($contents[$i][$x]['text']) && go_not_null($contents[$i][$x]['text'])) {
              $tableBox_string .= '    <td';
              if (isset($contents[$i][$x]['align']) && go_not_null($contents[$i][$x]['align'])) $tableBox_string .= ' align="' . go_output_string($contents[$i][$x]['align']) . '"';
              if (isset($contents[$i][$x]['params']) && go_not_null($contents[$i][$x]['params'])) {
                $tableBox_string .= ' ' . $contents[$i][$x]['params'];
              } elseif (go_not_null($this->table_data_parameters)) {
                $tableBox_string .= ' ' . $this->table_data_parameters;
              }
              if (isset($contents[$i]['ad_cell']) && go_not_null($contents[$i]['ad_cell'])) {
                $col_pos = $x+1;
                if ($col_pos <= $contents[$i]['cell_pos'] AND $contents[$i]['cell_pos'] <= round($contents[$i]['activ_col']/2)){
                  $first = '1';
                  $tableBox_string .= ' rowspan="2"';
                  $count_rowspan++;
                }
                if ($col_pos >= $contents[$i]['cell_pos'] AND $contents[$i]['cell_pos'] >= round($contents[$i]['activ_col']/2) AND $first != '1'){
                  $tableBox_string .= ' rowspan="2"';
                  $count_rowspan++;
                }
              }
              if (isset($contents[$i][$x]['all_last_cell_params']) && go_not_null($contents[$i][$x]['all_last_cell_params'])) {
                $tableBox_string .= ' ' . $contents[$i][$x]['all_last_cell_params'];
              }
              $tableBox_string .= '>';
              if (isset($contents[$i][$x]['form']) && go_not_null($contents[$i][$x]['form'])) $tableBox_string .= $contents[$i][$x]['form'];
              $tableBox_string .= $contents[$i][$x]['text'];
              if (isset($contents[$i][$x]['form']) && go_not_null($contents[$i][$x]['form'])) $tableBox_string .= '</form>';
              $tableBox_string .= '</td>' . "\n";
            }
          }
        } else {
          $tableBox_string .= '    <td';
          if (isset($contents[$i]['align']) && go_not_null($contents[$i]['align'])) $tableBox_string .= ' align="' . go_output_string($contents[$i]['align']) . '"';
          if (isset($contents[$i]['params']) && go_not_null($contents[$i]['params'])) {
            $tableBox_string .= ' ' . $contents[$i]['params'];
          } elseif (go_not_null($this->table_data_parameters)) {
            $tableBox_string .= ' ' . $this->table_data_parameters;
          }
          $tableBox_string .= '>' . $contents[$i]['text'] . '</td>' . "\n";
        }
        $tableBox_string .= '  </tr>' . "\n";
        if (isset($contents[$i]['ad_cell']) && go_not_null($contents[$i]['ad_cell'])) {
          $count_colspan = $contents[$i]['activ_col'] - $count_rowspan;
          $tableBox_string .= '  <tr';
          if (isset($contents[$i]['ad_cell_params_tr']) && go_not_null($contents[$i]['ad_cell_params_tr'])) $tableBox_string .= ' ' . $contents[$i]['ad_cell_params_tr'];
          $tableBox_string .= '>' . "\n";
          $tableBox_string .= '  <td';
          if (isset($contents[$i]['ad_cell_params_td']) && go_not_null($contents[$i]['ad_cell_params_td'])) $tableBox_string .= '  colspan="' . $count_colspan . '" ' . $contents[$i]['ad_cell_params_td'];
          $tableBox_string .= '>' . "\n";
          $tableBox_string .= $contents[$i]['ad_cell_text'] . "\n";
          $tableBox_string .= '</td>' . "\n";
          $tableBox_string .= '</tr>' . "\n";
        }
        if (isset($contents[$i]['form']) && go_not_null($contents[$i]['form'])) $tableBox_string .= '</form>' . "\n";
      }
      $tableBox_string .= '</table>' . "\n";
      if ($after_table != '') {
        $tableBox_string .= $after_table;
      }
      //$tableBox_string .= '<div>';
      if ($direct_output == true) echo $tableBox_string;
      return $tableBox_string;
    }
  }
  class infoBox extends tableBox {
    function __construct($contents) {
      $this->infoBox($contents);
    }
    function infoBox($contents) {
      $info_frame_contents = array();
      $info_frame_contents[] = array('text' => $this->infoFrameInsides($contents));
      $this->table_cellpadding = '1';
      $this->table_parameters = 'class="infoBox"';
      $this->tableBox($info_frame_contents, true);
    }
    function infoFrameInsides($contents) {
      $this->table_cellpadding = '3';
      $this->table_parameters = 'class="infoFrameInsides"';
      $info_frame_contents = array();
      $info_frame_contents[] = array(array('text' => go_fetch_dividing_up('tranparentes.gif', '100%', '1')));
      for ($i=0, $n=sizeof($contents); $i<$n; $i++) {
        $info_frame_contents[] = array(array('align' => (isset($contents[$i]['align']) ? $contents[$i]['align'] : ''),
                                           'form' => (isset($contents[$i]['form']) ? $contents[$i]['form'] : ''),
                                           'params' => 'class="frameText"',
                                           'text' => (isset($contents[$i]['text']) ? $contents[$i]['text'] : '')));
      }
      $info_frame_contents[] = array(array('text' => go_fetch_dividing_up('tranparentes.gif', '100%', '1')));
      return $this->tableBox($info_frame_contents);
    }
  }
  class infoFrameUp extends tableBox {
    function __construct($contents, $left_corner = true, $right_corner = true, $right_arrow = false) {
      $this->infoFrameUp($contents, $left_corner = true, $right_corner = true, $right_arrow = false);
    }
    function infoFrameUp($contents, $left_corner = true, $right_corner = true, $right_arrow = false) {
      $this->table_cellpadding = '0';
      if ($left_corner == true) {
        $left_corner = go_picture(FOLDER_RELATIV_PICTURES . '');
      } else {
        $left_corner = go_picture(FOLDER_RELATIV_PICTURES . '');
      }
      if ($right_arrow == true) {
        $right_arrow = '<a href="' . $right_arrow . '">' . go_picture(FOLDER_RELATIV_TEMPLATES . 'infoframe/arrow_right.gif', ICON_ARROW_RIGHT) . '</a>';
      } else {
        $right_arrow = '';
      }
      if ($right_corner == true) {
        $right_corner = $right_arrow . go_picture(FOLDER_RELATIV_PICTURES . '');
      } else {
        $right_corner = $right_arrow . go_fetch_dividing_up('tranparentes.gif', '11', '14');
      }
      $info_frame_contents = array();
      $info_frame_contents[] = array(array('params' => 'height="14" class="infoFrameUp"',
                                         'text' => $left_corner),
                                   array('params' => 'style="width:100%" height="14" class="infoFrameUp"',
                                         'text' => $contents[0]['text']),
                                   array('params' => 'height="14" class="infoFrameUp" nowrap',
                                         'text' => $right_corner));
      $this->tableBox($info_frame_contents, true);
    }
  }
  class contentBox extends tableBox {
    function __construct($contents) {
      $this->contentBox($contents);
    }
    function contentBox($contents) {
      $info_frame_contents = array();
      $info_frame_contents[] = array('text' => $this->contentBoxContents($contents));
      $this->table_cellpadding = '1';
      $this->table_parameters = 'class="infoBox"';
      $this->tableBox($info_frame_contents, true);
    }
    function contentBoxContents($contents) {
      $this->table_cellpadding = '4';
      $this->table_parameters = 'class="infoFrameInsides"';
      return $this->tableBox($contents);
    }
  }
  class contentBoxGeneralsign extends tableBox {
    function __construct($contents) {
      $this->contentBoxGeneralsign($contents);
    }
    function contentBoxGeneralsign($contents) {
      $this->table_width = '100%';
      $this->table_cellpadding = '0';
      $info_frame_contents = array();
      $info_frame_contents[] = array(array('params' => 'height="14" class="infoFrameUp"',
                                         'text' => go_picture(FOLDER_RELATIV_PICTURES . '')),
                                   array('params' => 'height="14" class="infoFrameUp" style="width:100%"',
                                         'text' => $contents[0]['text']),
                                   array('params' => 'height="14" class="infoFrameUp"',
                                         'text' => go_picture(FOLDER_RELATIV_PICTURES . '')));
      $this->tableBox($info_frame_contents, true);
    }
  }
  class errorBox extends tableBox {
    function __construct($contents) {
      $this->errorBox($contents);
    }
    function errorBox($contents) {
      $this->table_data_parameters = 'class="errorBox"';
      $this->tableBox($contents, true);
    }
  }
  class itemListingBox extends tableBox {
    function __construct($contents) {
      $this->itemListingBox($contents);
    }
    function itemListingBox($contents) {
      $this->table_parameters = 'class="itemListing"';
      if (defined('ITEM_LISTING_BEFOR')) {$bevor_table = ITEM_LISTING_BEFOR;}
      if (defined('ITEM_LISTING_AFTER')) {$after_table = ITEM_LISTING_AFTER;}
      $this->tableBox($contents, true, $bevor_table, $after_table);
    }
  }
?>
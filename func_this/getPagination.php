<?php
  function getPagination ($page, $totalCount, $params = '') {
  
    $totalPages = ceil($totalCount/MAX_SHOW_SEARCH_RESULTS);
    
    $startDisabled = '';
    if ( ($page - 1 ) < 1 ) {
      $startLink = '';
      $startDisabled = ' disabled';
    } else {
      $startLink = 'href="' . go_href_link(basename($_SERVER['SCRIPT_FILENAME']), $params . 'page=' . ($page - 1)) . '"';
    }
    $endDisabled = '';
    if ( ($page + 1) > $totalPages ) {
      $endDisabled = ' disabled';
      $endLink = '';
    } else {
      $endLink = 'href="' . go_href_link(basename($_SERVER['SCRIPT_FILENAME']), $params . 'page=' . ($page + 1)) . '"';
    }
    
    $pagination = '
      <nav aria-label="items navigation">
        <ul class="pagination pagination-sm">
          <li class="page-item' . $startDisabled . '">
            <a class="page-link"' . $startLink . ' aria-label="Previous">
              <span aria-hidden="true">&laquo;</span>
              <span class="sr-only">Previous</span>
            </a>
          </li>';
    
    for ( $i = 1; $i <= $totalPages; $i++ ) {
      if ( $i == ( $page - 2 ) ) $pagination .= '<li class="page-item"><a class="page-link" href="' . go_href_link(basename($_SERVER['SCRIPT_FILENAME']), $params . 'page=' . ( $page - 2 )) . '">' . $i . '</a></li>';
      if ( $i == ( $page - 1 ) ) $pagination .= '<li class="page-item"><a class="page-link" href="' . go_href_link(basename($_SERVER['SCRIPT_FILENAME']), $params . 'page=' . ( $page - 1 )) . '">' . $i . '</a></li>';
      if ( $i == $page ) $pagination .= '<li class="page-item active"><a class="page-link" href="' . go_href_link(basename($_SERVER['SCRIPT_FILENAME']), $params . 'page=' . $page) . '">' . $i . '</a></li>';
      if ( $i == ( $page + 1 ) ) $pagination .= '<li class="page-item"><a class="page-link" href="' . go_href_link(basename($_SERVER['SCRIPT_FILENAME']), $params . 'page=' . ( $page + 1 )) . '">' . $i . '</a></li>';
      if ( $i == ( $page + 2 ) ) $pagination .= '<li class="page-item"><a class="page-link" href="' . go_href_link(basename($_SERVER['SCRIPT_FILENAME']), $params . 'page=' . ( $page + 2 )) . '">' . $i . '</a></li>';
    }
    
    $pagination .= '
          <li class="page-item' . $endDisabled . '">
            <a class="page-link" ' . $endLink . ' aria-label="Next">
              <span aria-hidden="true">&raquo;</span>
              <span class="sr-only">Next</span>
            </a>
          </li>
        </ul>
      </nav>';
    
    return $pagination;
  }
?>
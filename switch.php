<?php
$view = isset($_GET['view']) ? $_GET['view'] : 'search';
if (isset($view)) {
  switch ($view) {
    case "search":
      include "views/search.php";
    break;
    case "explore":
      include "views/explore.php";
    break;
    case "about":
      include "views/about.php";
    break;
  }
}
?>
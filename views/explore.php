<h2>Explore MSU Library Digital Collections</h2>
<h3>Recent Searches</h3>
<p class="terms">
<?php
  //reads the number of last lines from file that you specify
  $file = array_reverse(file("search-log.txt"));
  //remove repeated terms
  $file = array_unique($file);
  $limit = 15;
  for ($i = 0; $i < $limit; $i++ ) {
    //check for empty values and strip comma from end of term string
    $term = (empty($file[$i])) ? null : str_replace(',', '', "$file[$i]");
    echo '<a href="./index.php?view=search&q='.urlencode($term).'">'.urldecode($term).'</a>'."\n";
  }
  /*
  //reads all lines from text file
  $handle = fopen("searchLog.txt", "r");
  while (list($term) = fgetcsv($handle, 1024, ",")) {
    echo '<li><a href="./index.php?view=search&q='.urlencode($term).'">'.$term.'</a></li>'."\n";
  }
  fclose($handle);
  */
?>
</p>
<h3>Popular Searches</h3>
<p class="terms">
<?php
  //set user API key for Google Custom Search API
  $key = isset($_GET['key']) ? $_GET['key'] : 'AIzaSyBPBEbLXzgvDhB8Pl9WGHHXPvSxj5TyBmg';
  //set most popular search terms RDF URL
  $request = 'http://www.google.com/cse/api/010001021870615082419/cse/sgtwcccfbiq/queries?key='.$key.'&view=overall';
  //read feed into SimpleXML object
  $result = simplexml_load_file($request);
  //parse and display results for most popular search terms in this CSE
  foreach ($result->item as $item) {
    $popularTerm = htmlentities($item->title);
?>
<a href="./index.php?view=search&q=<?php echo urlencode($popularTerm); ?>"><?php echo urldecode($popularTerm); ?></a>
<?php
}
?>
<!--<div id="queries"></div>-->
<!--<script src="http://www.google.com/cse/query_renderer.js"></script>-->
<!--<script src="http://www.google.com/cse/api/010001021870615082419/cse/sgtwcccfbiq/queries/js?callback=(new+PopularQueryRenderer(document.getElementById(%22queries%22))).render"></script>-->
<!--http://www.google.com/cse/api/010001021870615082419/cse/sgtwcccfbiq/queries/js?key=AIzaSyBPBEbLXzgvDhB8Pl9WGHHXPvSxj5TyBmg&callback=result&view=overall-->
</p>

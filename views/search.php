<h2>Search MSU Library Digital Collections</h2>

<?php
	// Number of records to display per page (1 - 10)
	$recordsPerPage = 10;

	// String that initially appears in the search box
	$suggestedSearch = "keyword, name, title...";

	// Set default value for query
	$q = isset($_GET['q']) ? urlencode(strip_tags(trim($_GET['q']))) : null;

	// Set default value for API format
	$form = isset($_GET['form']) ? htmlentities(strip_tags($_GET['form'])) : 'json';

	// Set default value for page length (number of entries to display)
	$limit = isset($_GET['limit']) ? strip_tags((int)$_GET['limit']) : "$recordsPerPage";

	// Set default value for page start index
	$start = isset($_GET['start']) ? strip_tags((int)$_GET['start']) : '1';

	// Set default value for facet browse
	$facet = isset($_GET['facet']) ? htmlentities(strip_tags($_GET['facet'])) : null;

	// Set default value for results sorting
	$sort = isset($_GET['sort']) ? htmlentities(strip_tags($_GET['sort'])) : null;

	// Set API version for Google Custom Search API
	$v = isset($_GET['v']) ? strip_tags((int)$_GET['v']) : 'v1';

	// Set user API key for Google Custom Search API
	$key = isset($_GET['key']) ? $_GET['key'] : 'AIzaSyBPBEbLXzgvDhB8Pl9WGHHXPvSxj5TyBmg';

	// Set user ID for Google custom search engine
	$id = isset($_GET['id']) ? $_GET['id'] : '010001021870615082419:sgtwcccfbiq';

?>
	<form id="searchBox" method="get" action="./index.php?view=search">
		<fieldset>
			<label for="q">Search</label>
			<input type="text" maxlength="200" name="q" id="q" tabindex="1" value="<?php echo (!is_null($q) ? $q : $suggestedSearch); ?>" onclick="if (this.value == '<?php echo $suggestedSearch; ?>') { this.value = ''; }" onblur="if (this.value == '') { this.value = '<?php echo $suggestedSearch; ?>'; }" />
			<button type="submit" class="button">Search</button>
		</fieldset>
	</form>

<?php
	if (!is_null($q)) {
		// Process query

		// Log search query to a text file
		session_start();
		$logged = isset($_SESSION['logged']) ? $_SESSION['logged'] : null;
		if ($logged != 'yes') {
			$fp = fopen("search-log.txt", "a");
			$queryLog = trim($q);

			$logLine = "$queryLog,\n";

			fwrite($fp, $logLine);
			fclose($fp);
			$_SESSION['logged'] = 'yes';
		}

		// Set URL for the Google Custom Search API call
		$url = "https://www.googleapis.com/customsearch/$v?key=$key&cx=$id&alt=$form".(is_null($sort) ? "" : "&sort=$sort")."&num=$limit&start=$start&prettyprint=true&q=$q".(is_null($facet) ? "" : "&hq=$facet");

		// View source to see raw API call - REMOVE from production code
		//echo '<!--' . $url . '-->';

		// Build request and send to Google Ajax Search API
    	$request = file_get_contents($url);

    	if ($request === FALSE) {
			// API call failed, display message to user
			echo '<p><strong>It looks like we can\'t communicate with the API at the moment.</strong></p>'."\n";
			exit();
    	}

    	// Decode json object(s) out of response from Google Ajax Search API
		$result = json_decode($request, true);

    	// Get values in json data for number of search results returned
		$totalItems = isset($_GET['totalItems']) ?  strip_tags((int)$_GET['totalItems']) : $result['queries']['request'][0]['totalResults'];

		if ($totalItems <= 0) {
			// Empty results, display message to user
			echo '<p><strong>Sorry, there were no results</strong></p>'."\n";
		}
		else {
			// Make sure some results were returned, show results as html with result numbering and pagination
?>
	<h2 class="result">Search for <strong><?php echo urldecode($q); ?></strong> (About <?php echo $totalItems; ?> results)</h2>
		<div class="result-facet">
			<p class="facet-filter facet"><span class="facet-heading">Filter</span>
			<a class="facet-link facet" href="./index.php?q=<?php echo urlencode($_GET['q']); ?>">All</a>
<?php
			foreach ($result['context']['facets'] as $key) {
				echo "<a class=\"facet-link facet\" href=\"./index.php?q=" . urlencode($_GET['q']). "&amp;facet={$key[0]['label']}\">" . ucfirst($key[0]['anchor']) . "</a> ";
			 }
?>
			</p>
			<p class="facet-filter facet"><span class="facet-heading">Sort</span><a class="facet-link facet" href="./index.php?q=<?php echo urlencode($_GET['q']); ?>">Relevance</a> <a class="facet-link facet" href="./index.php?sort=date&amp;q=<?php echo urlencode($_GET['q']); ?>">Date</a>
			</p>
			<p class="facet-filter facet popular"><span class="facet-heading">Popular Searches</span>
			<?php
			// Set most popular search terms RDF URL
			$request = 'http://www.google.com/cse/api/010001021870615082419/cse/sgtwcccfbiq/queries?key='.$key.'&view=overall';
			// Read feed into SimpleXML object
			$getPopular = simplexml_load_file($request, 'SimpleXMLIterator');
			// Set limit of 5 terms to display
			$entries = new LimitIterator($getPopular->item, 0, 5);
			foreach ($entries as $entry) {
				$popularTerm = htmlentities($entry->title);
?>
			<a class="facet-link facet" href="./index.php?view=search&q=<?php echo urlencode($popularTerm); ?>"><?php echo urldecode($popularTerm); ?></a>
<?php
			}
?>
			</p>
			<p class="facet-filter facet recent"><span class="facet-heading">Recent Searches</span>
<?php
// Reads the number of last lines from file that you specify
			$file = array_reverse(file("search-log.txt"));
      // Remove repeated terms
			$file = array_unique($file);
			$limit = 5;
      for ($i = 0; $i < $limit; $i++ ) {
				// Check for empty values and strip comma from end of term string
				$term = (empty($file[$i])) ? null : str_replace(',', '', "$file[$i]");
				echo '<a class="facet-link facet" href="./index.php?view=search&q='.urlencode($term).'">'.urldecode($term).'</a>'."\n";
        }
?>
			</p>
		</div>
		<ul class="result">
<?php
			foreach ($result['items'] as $item) {
				$link = rawurldecode($item['link']);
?>
			<li>
			<p class="result-object">
			<a href="<?php echo $link; ?>"><img alt="<?php echo htmlentities($item['title']); ?>"
			src="<?php $thumbnail = isset($item['pagemap']['metatags'][0]['thumbnailurl']) ? $item['pagemap']['metatags'][0]['thumbnailurl'] : (isset($item['pagemap']['cse_thumbnail'][0]['src']) ? $item['pagemap']['cse_thumbnail'][0]['src'] : (isset($item['pagemap']['cse_image'][0]['src']) ? $item['pagemap']['cse_image'][0]['src'] : './meta/img/thumbnail-default.png'));
			echo rawurldecode($thumbnail);?>" /></a>
			</p>
			<p class="result-description">
			<a href="<?php echo $link; ?>"><?php echo $item['htmlTitle']; ?></a>
			<br />
			<?php echo $item['htmlFormattedUrl']; ?>
			<br />
			<?php echo $item['htmlSnippet']; ?>
			<br />
			<?php //echo 'id: '.$sr['cacheId']; ?>
			<a class="expand" href="<?php echo $link; ?>">more</a>
			<br />
			<br />
			</p>
			</li>
<?php
			}
?>
		</ul>
<?php
		// Calculate new start value for "previous" link
		$previous = ($start > 1) ? ($start - $recordsPerPage) : null;
		$previous = (!is_null($previous) && ($previous < 1)) ? 1 : $previous;

		// Calculate new start value for "next" link
		$next = (($start + $recordsPerPage) <= $totalItems) ? ($start + $recordsPerPage) : null;
		if ($next >= 100) { $next = null; }

		// Display previous and next links if applicable
		if (!is_null($previous) || !is_null($next)) {
?>
		<ul class="pages">
<?php
			if (!is_null($previous)) {
?>
			<li><a href="./index.php?q=<?php echo urlencode($_GET['q']);?><?php if (!is_null($facet)) echo '&amp;facet=' . $facet;?>&amp;totalItems=<?php echo $totalItems; ?>&amp;start=<?php echo $previous; ?>">Previous</a></li>

<?php
			}
			if (!is_null($next)) {
?>
			<li><a  href="./index.php?q=<?php echo urlencode($_GET['q']);?><?php if (!is_null($facet)) echo '&amp;facet=' . $facet;?>&amp;totalItems=<?php echo $totalItems; ?>&amp;start=<?php echo $next; ?>">Next</a></li>
<?php
			}
?>
		</ul>
<?php
			}
		} // End else -- $totalItems <= 0
	} // End (!is_null($q))
?>

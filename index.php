<?php
//set value for title of page
$pageTitle = 'Search Digital Collections - Montana State University (MSU) Library';
$subTitle = 'app';
//set default tab and page view
$view = isset($_GET['view']) ? strip_tags(htmlentities($_GET['view'])) : 'search';
//set filename for additional stylesheet - default is "none"
$customCSS = 'none';
//create an array with filepaths for multiple page scripts - default is meta/scripts/global.js
$customScript[0] = null;
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Cache-Control" content="max-age=200" />
<meta name="description" content="<?php echo $pageTitle.', '.$subTitle; ?>" />
<meta id="viewport" name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?php echo $pageTitle.', '.$subTitle; ?></title>
<link rel="apple-touch-icon" href="./meta/img/msu-mobile.png" />
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,700'" rel="stylesheet" type="text/css">
<link href="./meta/styles/app.css" media="screen" rel="stylesheet" type="text/css" />
<?php 
if ($customCSS != 'none') {
	echo '<link href="'.dirname($_SERVER['PHP_SELF']).'./meta/styles/'.$customCSS.'" media="screen" rel="stylesheet" type="text/css" />'."\n";
}
if ($customScript) {
	$counted = count($customScript);
	for ($i = 0; $i < $counted; $i++) {
		echo '<script type="text/javascript" src="'.$customScript[$i].'"></script>'."\n";
	}
}
?>
<!--
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'ADD-YOUR-GOOGLE-ANALYTICS-ID-HERE']);
  _gaq.push(['_setDomainName', 'ADD-YOUR-BASE-DOMAIN-URL-HERE']);
  _gaq.push(['_trackPageview']);
  _gaq.push(['_gat._anonymizeIp']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
-->
</head>
<body class="<?php echo $view; ?>">
<div id="doc">
	<div id="hd">
	<h1><?php echo $pageTitle; ?></h1>
    <ul id="nav">
        <li id="tab1"><a accesskey="1" class="icon-search" href="./index.php?view=search">Search</a></li>
        <li id="tab2"><a accesskey="2" class="icon-map" href="./index.php?view=explore">Explore</a></li>
        <li id="tab3"><a accesskey="3" class="icon-location" href="./index.php?view=about">About</a></li>
    </ul><!-- end nav list -->
    </div><!-- end hd div -->
	<div id="main">
    	<?php include "switch.php"; ?>
    	</div><!-- end main div -->
	<div id="ft">
	<p class="info">
        <a accesskey="4" class="site icon-browser" title="full site" href="http://www.lib.montana.edu/digital/">Full site</a>
        <a accesskey="5" class="about icon-info-circle" title="about this app" href="./index.php?view=about">About</a>
        <!--<a accesskey="6" class="feed icon-feed" title="feed for collection" href="../feed.xml">Feed</a>-->
	</p>
	</div><!-- end ft div -->
</div><!-- end doc div -->
</body>
</html>
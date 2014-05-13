<!DOCTYPE html>

<?php
require('./include/ionDataProducts.php');

$html = '';

# Data Products json files
define('DP_ROOT', './json/observatoryDataProducts');

?>

<html lang="en">

<head>
<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="initial-scale = 1.0, maximum-scale = 1.0" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="mobile-web-app-capable" content="yes">
<title>ION DataProduct Browser</title>

<!--
<meta name="author" content="John Kerfoot, johnkerfoot@gmail.com"/>
<meta name="keywords" content=""/>
-->

<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.css" />
<link href="./js/Leaflet.MousePosition-master/src/L.Control.MousePosition.css" rel="stylesheet" />
<link href="./css/ooi.css" rel="stylesheet">

</head>

<body>

<?php
# Add header bar
$html .= '<nav class="navbar navbar-inverse navbar-static-top" role="navigation">';
/*
$html .= '<p id="utc-clock" class="navbar-text pull-right"></p>';
*/
$html .= '<div class="container-fluid">';
$html .= '<div class="navbar-header">';

$html .= '<button type="button" ';
$html .= 'class="navbar-toggle" ';
$html .= 'data-toggle="collapse" ';
$html .= 'data-target="#navbar-collapse">';
$html .= '<span class="sr-only"></span>';
$html .= '<span class="icon-bar"></span>';
$html .= '<span class="icon-bar"></span>';
$html .= '<span class="icon-bar"></span>';
$html .= '</button>';

$html .= '<a class="navbar-brand" href="#">ION Status</a>';
$html .= '</div> <!-- .navbar-header -->';

$html .= createObservatoriesPulldown();

$html .= '</div> <!-- .container-fluid -->';
$html .= '</nav>';

$html .= '<div id="main" class="container-fluid">';

$html .= '<div class="row">';

$html .= '<div class="col-md-3">';

$html .= '<div class="row">';
$html .= '<div id="menu"></div>';
$html .= '</div> <!-- .row -->';

$html .= '<div class="row">';
$html .= '<div id="map"></div>';
$html .= '</div> <!-- .row -->';

$html .= '</div> <!-- .col-md-3 -->';

$html .= '<div class="col-md-9">';

$html .= '<div class="row">';

$html .= '</div> <!-- .row -->';

$html .= '<div class="row">';
$html .= '<div id="content" class="col-md-12">';
$html .= '<div class="jumbotron">';
$html .= '<h2>OOI ION Status</h2>';
$html .= '<p>';
$html .= 'Welcome to the OOI ION status page.  ';
$html .= 'This application was developed as an internal prototype for ';
$html .= 'displaying the near real-time status of all <b>Observatory</b>, ';
$html .= '<b>PlatformSite</b> and <b>DataProduct</b> resources in the OOI network. ';
$html .= 'Select a site from the Sites menu to view the status of all ';
$html .= 'available data products for that site.';
$html .= '</p>';
$html .= '</div> <!-- .jumbotron -->';

$html .= '</div>';
$html .= '</div> <!-- #content -->';
$html .= '</div> <!-- .row -->';

$html .= '</div> <!-- .row -->';

$html .= '</div>';
$html .= '</div> <!-- .container-fluid -->';

print $html;
?>

<!-- Javascript -->
<script type="text/javascript" src="http://code.jquery.com/jquery.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
<script src="http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.js"></script>
<script src="./js/Leaflet.MousePosition-master/src/L.Control.MousePosition.js"></script>
<script src="http://marine.rutgers.edu/~kerfoot/js/Url.js"></script>
<script src="./js/ClockUtc.js"></script>
<script src="./js/Ooi.js"></script>
</body>

</html>

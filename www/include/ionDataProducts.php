<?php

function showOoiNavbar() {

    $dpRoot = DP_ROOT . '/*dataProducts.cluster.json';
    $jsonFiles = glob($dpRoot);
/*
    print '<pre>';
    print_r($jsonFiles);
    print '</pre>';
*/

    $seenSites = array();
    $html = '';

    $html .= '<div id="ooi-sites" class="btn-group">';
    foreach ($jsonFiles as $jsonFile) {

        $json = json_decode(file_get_contents($jsonFile));
/*
        print '<pre>';
        print_r($json);
        print '</pre>';
*/

        # Must have a reference_designator which is the Observatory name
        if (!$json->{'reference_designator'}) {
            continue;
        }

        $siteTokens = explode(' ', $json->{'spatial_area_name'});
        $site = implode(' ', array_slice($siteTokens, 1));
        $ref = substr($json->{'reference_designator'},0,2);

        if (in_array($site, $seenSites)) {
            continue;
        }

        $seenSites[] = $site;

        $html .= '<a class="btn btn-primary" ';
        $html .= "href=\"?sid=$ref\">";
        $html .= $site;
        $html .= '</a>';

    }
    $html .= '</div>';

    return $html;
        
}

function createObservatoriesPulldown() {

    $dpRoot = DP_ROOT . '/*dataProducts.cluster.json';
    $jsonFiles = glob($dpRoot);
/*
    print '<pre>';
    print_r($jsonFiles);
    print '</pre>';
*/

    $seenSites = array();
    $html = '';

    $html .= '<div class="collapse navbar-collapse" id="navbar-collapse">';
    $html .= '<ul class="nav navbar-nav">';
    $html .= '<li class="dropdown">';
    $html .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown">';
    $html .= 'Sites';
    $html .= '<b class="caret"></b>';
    $html .= '</a>';
    $html .= '<ul id="ooi-sites" class="dropdown-menu">';
    foreach ($jsonFiles as $jsonFile) {

        $json = json_decode(file_get_contents($jsonFile));
/*
        print '<pre>';
        print_r($json);
        print '</pre>';
*/

        # Must have a reference_designator which is the Observatory name
        if (!$json->{'reference_designator'}) {
            continue;
        }

        $siteTokens = explode(' ', $json->{'spatial_area_name'});
        $site = implode(' ', array_slice($siteTokens, 1));
        $ref = substr($json->{'reference_designator'},0,2);

        if (in_array($site, $seenSites)) {
            continue;
        }

        $seenSites[] = $site;

        $html .= '<li>';
        $html .= "<a href=\"?sid=$ref\">";
        $html .= $site;
        $html .= '</a>';
        $html .= '</li>';

    }
    $html .= '</ul> <!-- .dropdown-menu -->';
    $html .= '</li> <!-- .dropdown -->';
    $html .= '<li id="utc-clock" class="navbar-text"></li>';
    $html .= '</ul> <!-- .nav.navbar-collapse -->';
    $html .= '</div> <!-- #navbar-collapse -->';

    return $html;
        
}

?>

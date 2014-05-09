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

?>

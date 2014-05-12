<?php

function getSiteLocations() {

    $geoJson = array();

    $jsonFiles = glob(DP_ROOT . "/*dataProducts.cluster.json");

/*
    print '<pre>';
    print_r($jsonFiles);
    print '</pre>';
*/

    foreach ($jsonFiles as $jsonFile) {
        $json = json_decode(file_get_contents($jsonFile));
        $geoJson[$json->{'reference_designator'}] = $json->{'geometry'};
    }

    return json_encode($geoJson);

}

function showPlatformDataProductStatus($pid) {

    $html = '';

    list($obs, $platform) = explode('-', $pid);

    $jsonFile = DP_ROOT . "/${obs}_dataProducts.cluster.json";
    $json = json_decode(file_get_contents($jsonFile));

    # Modification time
    $modTime = gmstrftime('%Y-%m-%d %H:%M Z', filemtime($jsonFile));
    $html .= "<h2>$pid DataProducts <small>&#40;Updated: $modTime&#41;</small></h2>";

    $platform = array();
    foreach ($json->{'children'} as $child) {

        if ($child->{'reference_designator'} == $pid) {
            $platform = $child;
        }

    }

    if (empty($platform)) {
        return $html;
    }

    $html .= '<table id="data-products" class="table table-bordered table-striped table-condensed">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th>Data Product</th>';
    $html .= '<th class="center">ERDDAP</th>';
    $html .= '<th class="center">Size</th>';
    $html .= '<th class="center">Proc Level</th>';
    $html .= '<th class="center">QC Level</th>';
/*
    $html .= '<th>Resource ID</th>';
    $html .= '<th>Availability</th>';
*/
    $html .= '<th class="center">Status</th>';
    $html .= '<th class="center">Updated</th>';
    $html .= '</tr>';
    $html .= '</thead>';

    usort($platform->{'children'}, "sortIonName");
    $html .= '<tbody>';
    foreach ($platform->{'children'} as $dp) {

        $html .= '<tr>';

        # DataProduct name
        $spatialArea = $json->{'spatial_area_name'};
        $product = preg_replace("/\s?\-\s+$spatialArea.*$/", '', $dp->{'ion_name'});
        $html .= '<td>';
        $html .= '<i class="glyphicon glyphicon-info-sign" ';
        $html .= 'rel="popover" ';
        $html .= 'data-placement="left" ';
        $html .= 'data-content="';
        $html .= $dp->{'name'};
        $html .= '"></i>&nbsp;';
        $html .= "<a class=\"data-product-params\" href=\"./?pid=${pid}\">";
        $html .= wordwrap($product, 70, '<br />');
        $html .= '</a>';
        $html .= '</td>';

        # ERDDAP link
        $html .= '<td class="center">';
        $html .= '<a href="';
        $html .= $dp->{'erddap_url'};
        $html .= '" target="_blank">';
        $html .= '<i class="glyphicon glyphicon-download-alt"></i>';
        $html .= '</td>';

        # File download size
        if ($dp->{'product_download_size_estimated'} == 0) {
            $html .= '<td class="text-danger">';
        }
        else {
            $html .= '<td>';
        }
        $html .= sprintf('%0.1f Mb', $dp->{'product_download_size_estimated'}/1024);
        $html .= '</td>';

        # Processing level
        $html .= '<td class="center">';
        $html .= $dp->{'processing_level_code'};
        $html .= '</td>';

        # Quality control level
        $html .= '<td class="center">';
        $html .= $dp->{'quality_control_level'};
        $html .= '</td>';

        # Resource id
/*
        $html .= '<td>';
        $html .= $dp->{'_id'};
        $html .= '</td>';
*/

        # Availability
/*
        $html .= '<td>';
        $html .= $dp->{'availability'};
        $html .= '</td>';
*/

        # lcstate
        $html .= '<td>';
        $html .= $dp->{'lcstate'};
        $html .= '</td>';

        # Last updated
        $html .= '<td class="center">';
        if (!$dp->{'last_granule_update'}) {
            $html .= 'N/A';
        }
        else {
            $html .= gmstrftime('%Y-%m-%d<br />%H:%M Z', $dp->{'last_granule_update'});
        }
        $html .= '</td>';

        $html .= '</tr>';

    }
    $html .= '</tbody>';
    $html .= '</table>';

    return $html;

}

function showDataProductParameters($pid, $pnum) {

    $html = '';

    list($obs, $platform) = explode('-', $pid);

    $jsonFile = DP_ROOT . "/${obs}_dataProducts.cluster.json";
    $json = json_decode(file_get_contents($jsonFile));

    $platform = array();
    foreach ($json->{'children'} as $child) {

        if ($child->{'reference_designator'} == $pid) {
            $platform = $child;
        }

    }

    if (empty($platform)) {
        return $html;
    }

    usort($platform->{'children'}, "sortIonName");

    $params = $platform->{'children'}[$pnum]->{'children'};
/*
    print '<pre>';
    print_r($params);
    print '</pre>';
*/

    # Modification time
    $modTime = gmstrftime('%Y-%m-%d %H:%M Z', filemtime($jsonFile));
    $product = $platform->{'children'}[$pnum]->{'name'};
    $html .= "<h2>$product Parameters</h2>";

    $html .= '<table id="data-products" class="table table-bordered table-striped table-condensed">';
    $html .= '<tr>';
    $html .= '<th>Parameter</th>';
    $html .= '<th class="center">Variable</th>';
    $html .= '<th class="center">Units</th>';
//    $html .= '<th class="center">Description</th>';
    $html .= '</tr>';
    foreach ($params as $param) {

        $html .= '<tr>';

        # Parameter display_name
        $html .= '<td>';
        $html .= '<i class="glyphicon glyphicon-info-sign" ';
        $html .= 'rel="popover" ';
        $html .= 'data-placement="left" ';
        $html .= 'data-content="';
        $html .= $param->{'description'};
        $html .= '"></i>&nbsp;';
        $html .= $param->{'display_name'};
        $html .= '</td>';

        # Variable Name
        $html .= '<td class="center">';
        $html .= $param->{'name'};
        $html .= '</td>';

        # Parameter Units
        $html .= '<td>';
        $html .= $param->{'units'};
        $html .= '</td>';

        $html .= '</tr>';

    }
    $html .= '</table>';

    return $html;

}

function showSiteMenu($sid) {

    $dpRoot = DP_ROOT . "/$sid*dataProducts.cluster.json";
    $jsonFiles = glob($dpRoot);
/*
    print '<pre>';
    print_r($jsonFiles);
    print '</pre>';
*/

    $html = '<div class="panel-group" id="data-product-menu">';
    foreach ($jsonFiles as $f) {
        $json = json_decode(file_get_contents($f));

        # Must have a reference_designator which is the Observatory name
        if (!$json->{'reference_designator'}) {
            continue;
        }

        $html .= '<div class="panel panel-default">';
        $html .= '<div class="panel-heading">';

        # Observatory name
        $site = $json->{'reference_designator'};

        # Observatory description
        $obsName = $json->{'name'};
        $html .= '<i class="glyphicon glyphicon-info-sign" ';
        $html .= 'rel="popover" ';
        $html .= "data-content=\"$obsName\"></i>&nbsp;";

        $html .= $site;

        # Dropdown toggle
        $hasChildren = count($json->{'children'});
        $html .= '<a data-toggle="collapse" ';
        $html .= 'data-parent="#data-product-menu" ';
        $html .= "href=\"#$site\">";
        $html .= $hasChildren > 0 ?
            '<i class="glyphicon glyphicon-collapse-down pull-right"></i>' :
            '';

        $html .= '</a>';
        $html .= '</div>';

        if ($hasChildren == 0) {
            $html .= '</div>';
            continue;
        }

        $html .= '<div id="';
        $html .= $site;
        $html .= '" ';
        $html .= 'class="panel-collapse collapse list-group">';

        usort($json->{'children'}, "sortAlphabetical");
        foreach ($json->{'children'} as $child) {
            $ref = $child->{'reference_designator'};
            $html .= "<a id=\"$ref\" href=\"?pid=$ref\" class=\"list-group-item platform\">";
            $html .= $child->{'name'};
            $html .= '</a>';
        }
        $html .= '</div>';

        $html .= '</div>';
    }
    $html .= '</div>';

    return $html;

}

function sortAlphabetical($a, $b) {
    if ($a->{'name'} == $b->{'name'}) {
        return 0;
    }
    return ($a->{'name'} < $b->{'name'}) ? -1 : 1;
}

function sortReferenceDesignator($a, $b) {
    if ($a->{'name'} == $b->{'name'}) {
        return 0;
    }
    return ($a->{'name'} < $b->{'name'}) ? -1 : 1;
}

function sortIonName($a, $b) {
    if ($a->{'ion_name'} == $b->{'ion_name'}) {
        return 0;
    }
    return ($a->{'ion_name'} < $b->{'ion_name'}) ? -1 : 1;
}

?>

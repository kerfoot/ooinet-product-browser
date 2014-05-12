<?php

require('./include/ionContent.php');

# Data Products json files
define('DP_ROOT', './json/observatoryDataProducts');

$sid = isset($_GET['sid']) ? $_GET['sid'] : '';
$pid = isset($_GET['pid']) ? $_GET['pid'] : '';
$pnum = isset($_GET['pnum']) ? $_GET['pnum'] : -1;
$type = isset($_GET['type']) ? $_GET['type'] : '';

if (!empty($sid)) {
    $response = showSiteMenu($sid);
}
elseif (!empty($pid)) {
    if ($pnum > -1) {
        $response = showDataProductParameters($pid, $pnum);
    }
    else {
        $response = showPlatformDataProductStatus($pid);
    }
}
elseif ($type == 'sites') {
    $response = getSiteLocations();
}
else {
}

print $response;

?>

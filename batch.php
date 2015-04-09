<?php
require_once 'lib/loader.php';

$sites = $app->getSites();

foreach($sites as $site) {

    $number = $site['number'];

    echo "Destroying site $number\n";
    $app->destroySite($number);

    //echo "Creating site $number\n";
    //$app->createSite($number);
}


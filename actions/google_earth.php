<?php
class actions_google_earth {
    function handle(&$params){
    	header("Content-Disposition: attachment; filename=\"sites.kml\"");
    	include_once('kml.class.php');
    	$kml = new KML('GEOBASE');
    	$document = new KMLDocument('geobase', 'GEOBASE');
    	
    	$sitesFolder = new KMLFolder('', 'Sites');
    	
        $app =& Dataface_Application::getInstance();
        $query =& $app->getQuery();
        $query['-skip'] = 0;
        $query['-limit'] = 100000;
        $sites = df_get_records_array('sites', $query);
        foreach ($sites as $s){
            $siteDetail = new KMLPlaceMark('', $s->val('Name'), $s->htmlValue('Description'), true);
            $siteDetail->setGeometry(new KMLPoint($s->val('Lon'), $s->val('Lat'), 0));
            $style = new KMLStyle();
            $style->setBalloonStyle ($s->htmlValue('Description'));
            $siteDetail->addStyle($style);
            $sitesFolder->addFeature($siteDetail);
        }
        $document->addFeature($sitesFolder);
        $kml->setFeature($document);
        echo $kml->output('S');
    }
}

<?php
class actions_KML {
    function handle(&$params){
    	header("Content-Disposition: attachment; filename=\"sites.kml\"");
    	include_once('kml.class.php');
    	$kml = new KML('GEOBASE');
    	$document = new KMLDocument('geobase', 'GEOBASE');
    	
    	$sitesFolder = new KMLFolder('', 'Sites');
    	
        $app =& Dataface_Application::getInstance();
        $query =& $app->getQuery();
        $sites =& df_get_selected_records($query);
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
<?php
class tables_rocks {
    function getDescription(&$record){
        $skey = $record->getValue('SiteID');
        $sparent = df_get_record('sites', array('SiteID'=>$skey));
        return $sparent->getTitle();
    }

    function getBreadCrumbs(&$record){
        $skey = $record->getValue('SiteID');
        $sparent = df_get_record('sites', array('SiteID'=>$skey));
        return array(
            'Sites' => DATAFACE_SITE_HREF,
            $record->val('Sitename') => $sparent->getURL(),
            'Current rock' => $record->getURL()
        );
    }

	function beforeSave($record){
	    $user =& Dataface_AuthenticationTool::getInstance()->getLoggedInUsername();
        $record->setValue('ModifiedBy', $user);
	}
}
?>

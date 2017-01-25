<?php
class tables_samples {
    function getTitle(&$record){
        $rkey = $record->getValue('RockID');
        $rparent = df_get_record('rocks', array('RockID'=>$rkey));
        return $rparent->getTitle().' '.$record->val('Type').':'.$record->val('Sitename').$record->val('Label');
    }

    function getBreadCrumbs(&$record){
        $rkey = $record->getValue('RockID');
        $rparent = df_get_record('rocks', array('RockID'=>$rkey));
        $sparent = df_get_record('sites', array('SiteID'=>$rparent->getValue('SiteID')));
        return array(
            'Sites' => DATAFACE_SITE_HREF,
            $record->val('Sitename') => $sparent->getURL(),
            $record->val('Rockname') => $rparent->getURL(),
            'Current sample' => $record->getURL()
        );
    }

    function beforeSave($record){
        $user =& Dataface_AuthenticationTool::getInstance()->getLoggedInUsername();
        $record->setValue('ModifiedBy', $user);
    }
}
?>

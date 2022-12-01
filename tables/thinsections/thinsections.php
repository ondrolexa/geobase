<?php
class tables_thinsections {
    function getTitle(&$record){
        $skey = $record->getValue('SampleID');
        $sparent = df_get_record('samples', array('SampleID'=>$skey));
        $rparent = df_get_record('rocks', array('RockID'=>$sparent->getValue('RockID')));
        $typ = $record->val('Type');
        if( !isset($typ) )
        {
            return $rparent->getTitle().' '.$record->val('Sample');
        }
        else
        {
            return $rparent->getTitle().' '.$record->val('Sample').' ('.$typ.')';
        }
    }

    function getBreadCrumbs(&$record){
        $samkey = $record->getValue('SampleID');
        $samparent = df_get_record('samples', array('SampleID'=>$samkey));
        $rparent = df_get_record('rocks', array('RockID'=>$samparent->getValue('RockID')));
        $sparent = df_get_record('sites', array('SiteID'=>$rparent->getValue('SiteID')));
        return array(
            'Sites' => DATAFACE_SITE_HREF,
            $record->val('Sitename') => $sparent->getURL(),
            $record->val('Rockname') => $rparent->getURL(),
            $samparent->getTitle() => $samparent->getURL(),
            'Current thinsection' => $record->getURL()
        );
    }

    function beforeSave($record){
        $user =& Dataface_AuthenticationTool::getInstance()->getLoggedInUsername();
        $record->setValue('ModifiedBy', $user);
    }

    function Photo__renderCell( &$record ){
        $tskey = $record->getValue('ThinsectionID');
        $tsphoto = df_get_record('tsphotos', array('ThinsectionID'=>$tskey));
        if (!empty($tsphoto)) {
            return '<a href="'.$tsphoto->getURL('-action=view').'">Yes</a>';
        } else {
            return '-';
        }
    }
}
?>

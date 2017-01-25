<?php
class tables_strdata {

    function getTitle(&$record){
        $pa = $record->val('Pazimuth');
        if(empty($pa)){
            $ps = '';
            $pp = '';
            $cc = '';
        } else {
            $ps = $record->val('Pazimuth').'/'.$record->val('Pinclination');
            $typ = df_get_record('strtypes', array('StrtypeID'=>$record->getValue('PstrtypeID')));
            $pp = $typ->getValue('Label').':';
            $cc = '-';
        }
        $la = $record->val('Lazimuth');
        if(empty($la)){
            $ls = '';
            $lp = '';
            $cc = '';
        } else {
            $ls = $record->val('Lazimuth').'/'.$record->val('Linclination');
            $typ = df_get_record('strtypes', array('StrtypeID'=>$record->getValue('LstrtypeID')));
            $lp = $typ->getValue('Label').':';
        }
        return $pp.$ps.$cc.$lp.$ls;
    }

    function getBreadCrumbs(&$record){
        $rkey = $record->getValue('RockID');
        $rparent = df_get_record('rocks', array('RockID'=>$rkey));
        $sparent = df_get_record('sites', array('SiteID'=>$rparent->getValue('SiteID')));
        return array(
            'Sites' => DATAFACE_SITE_HREF,
            $record->val('Sitename') => $sparent->getURL(),
            $record->val('Rockname') => $rparent->getURL(),
            'Current structural data' => $record->getURL()
        );
    }
	
	function Plot__renderCell( &$record ){
		if ($record->getValue('Plot')) return "Yes"; else return "No";
	}
	
	function beforeSave($record){
	    $user =& Dataface_AuthenticationTool::getInstance()->getLoggedInUsername();
        $record->setValue('ModifiedBy', $user);
	}
}
?>

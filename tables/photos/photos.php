<?php
class tables_photos {

	function getTitle(&$record){
		return $record->val('ImageFile');
	}

	function ImageFile__renderCell(&$record){
	    $imageField =& $record->_table->getField('ImageFile');
		return '<img src="'.$imageField['thumbpath'].'/'.$record->val('ImageFile').'" />';
	}
	
	function getBreadCrumbs(&$record){
	    $skey = $record->getValue('SiteID');
        $sparent = df_get_record('sites', array('SiteID'=>$skey));
        return array(
            'Sites' => DATAFACE_SITE_HREF,
            $record->val('Sitename') => $sparent->getURL(),
            'Current photo' => $record->getURL()
        );
    }
    
    function section__photo(&$record){
        $imageField =& $record->_table->getField('ImageFile');
        return array(
            'content' => '<img src="'.data_uri($imageField['savepath'].'/'.$record->val('ImageFile')).'" width="50%" /><br/><a href="'.$record->display('ImageFile').'">Click to download original image file</a>',
            'class' => 'main',
            'label' => 'Photo',
            'order' => 0
        );
    }

	function beforeSave($record){
	    $user =& Dataface_AuthenticationTool::getInstance()->getLoggedInUsername();
        $record->setValue('ModifiedBy', $user);
	}
	
	function afterSave(&$record){
		$imageField =& $record->_table->getField('ImageFile');
        // path to the directory where images are saved for this field.
        if ( !$record->val('ImageFile') ) return;
        // Note that if no image is set in this record, we don't need to resize anything
        if ( isset($_FILES['ImageFile']['name']) ){
        	// A new image has just been uploaded, so we resize it.
			// I like to use the Imagemagick CONVERT command for simplicity.
			// This section saves a copy of the image as a thumbnail (scaled by the value labeled in the fields.ini file), and then prefixed with a "th_" before it.
        	$ResizeCommand= "convert " .$imageField['savepath']."/".$record->val('ImageFile')." -resize ".$imageField['thumbsize']."\> ".$imageField['thumbpath']."/".$record->val('ImageFile');
            system($ResizeCommand);
        }
	}
	
	function field__friend_picture(&$record){
		return '<img src="'.$imageField['savepath'].'/'.$record->val('ImageFile').'" />';
	}
	
	function afterDelete(&$record){
		$imageField =& $record->_table->getField('ImageFile');
		unlink($imageField['savepath']."/".$record->val('ImageFile'));
		unlink($imageField['thumbpath']."/".$record->val('ImageFile'));
    }
}
?>

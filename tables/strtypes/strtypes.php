<?php
class tables_strtypes {

	function beforeSave($record){
	    $user =& Dataface_AuthenticationTool::getInstance()->getLoggedInUsername();
        $record->setValue('ModifiedBy', $user);
	}
}
?>
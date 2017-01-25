<?php
class tables_units {

	function beforeSave($record){
	    $user =& Dataface_AuthenticationTool::getInstance()->getLoggedInUsername();
        $record->setValue('ModifiedBy', $user);
	}
	
	function Description__renderCell( &$record ){
	   $string = $record->strval('Description');
       return (strlen($string) > 43) ? substr($string,0,40).'...' : $string;
    }
}
?>

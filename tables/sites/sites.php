<?php
class tables_sites {

	function beforeSave($record){
	    $user = Dataface_AuthenticationTool::getInstance()->getLoggedInUsername();
        $record->setValue('ModifiedBy', $user);
	}

	function __import__csv(&$data, $defaultValues=array()){
		// build an array of Dataface_Record objects that are to be inserted based
		// on the CSV file data.
		$records = array();

		// first split the CSV file into an array of rows.
		$rows = explode("\n", $data);
		// remove last empty row
		array_pop($rows);
		foreach ( $rows as $row ){
		    // We iterate through the rows and parse the name, lon, and lat
		    // values to that they can be stored in a Dataface_Record object.
		    list($name, $lon, $lat) = explode(',', $row);
		    $record = new Dataface_Record('sites', array());

		    // We insert the default values for the record.
		    $record->setValues($defaultValues);

		    // Now we add the values from the CSV file.
		    $record->setValues(
		        array(
		            'Name'=>$name,
		            'Lon'=>$lon,
		            'Lat'=>$lat
		             )
		        );

		    // Now add the record to the output array.
		    $records[] = $record;
		}

		// Now we return the array of records to be imported.
		return $records;
	}
	function section__map(&$record){
		$map = '<img src="http://maps.googleapis.com/maps/api/staticmap?&zoom=12&size=400x250&maptype=satellite&markers=size:tiny|color:green';
		$myquery = sprintf("SELECT SiteID, 6371*2*ASIN(SQRT(POWER(SIN((%f-abs(sites.Lat))*pi()/180/2),2)+COS(%f*pi()/180)*COS(abs(sites.Lat)*pi()/180)*POWER(SIN((%f-sites.Lon)*pi()/180/2),2))) as distance FROM sites having distance<5 ORDER BY distance limit 15", $record->val('Lat'), $record->val('Lat'), $record->val('Lon'));
		$res = xf_db_query($myquery, df_db());
		$row = xf_db_fetch_assoc($res);
		$table = '<table class="details_table"><tr><th colspan=2>Neighbouring sites</th></tr>';
		while ($row = xf_db_fetch_assoc($res) ){
			$myrec = df_get_record('sites', array('SiteID'=>$row['SiteID']));
			$table .= '<tr><td><a href="'.$myrec->getURL().'">'.$myrec->val('Name').'</a></td><td align="right">'.round($row['distance']*1000).' m</td></tr>';
			$map .= sprintf('|%f,%f', $myrec->val('Lat'), $myrec->val('Lon'));
		}
		@xf_db_free_result($res);
		$map .= sprintf('&markers=color:blue|label:X|%f,%f', $record->val('Lat'), $record->val('Lon'));
		$map .= '&sensor=false&key=AIzaSyD4d0mS7W5LSpBF3SpX5FB9bSlhr1nSd-o" alt="google map" style="float:left; margin: 0 20px 0 0;"/>';
		$table .= '</table>';
		$link = sprintf('<div style="clear: both; display: block; position: relative;"><a href="https://maps.google.com/maps?q=loc:%f,%f&amp;ie=UTF8&amp;t=h&amp;z=13&amp;source=embed" style="color:#0000FF;text-align:left" target="_blank">Show in Google Maps</a></div>', $record->val('Lat'), $record->val('Lon'));
		return array(
			'content' => $map.$table.$link,
			'class' => 'main',
			'label' => 'Map view',
			'order' => -2
    );
    }

    function section__sitephoto(&$record){
        $sid = $record->getValue('SiteID');
        $photo = df_get_record('photos', array('SiteID'=>$sid));
        if (!empty($photo)) {
            $imageField =& $photo->_table->getField('ImageFile');
            return array(
                'content' => '<img src="'.data_uri($imageField['savepath'].'/'.$photo->val('ImageFile')).'" width="400" />',
                'class' => 'main',
                'label' => 'Site photo',
                'order' => -1
            );
        }
    }

    function section__samples(&$record){
		$myquery = sprintf("SELECT rocks.RockID,samples.SampleID FROM sites JOIN rocks ON sites.SiteID = rocks.SiteID JOIN samples ON rocks.RockID = samples.RockID WHERE sites.SiteID=%d", $record->val('SiteID'));
		$res = xf_db_query($myquery, df_db());
		if (xf_db_num_rows($res) > 0) {
			$table = '<table width="100%"><tr><th align="left">Sample</th><th align="right">Rock</th><th align="right">Thinsection</th></tr>';
			while ($row = xf_db_fetch_assoc($res) ){
				$samrec = df_get_record('samples', array('SampleID'=>$row['SampleID']));
				$rockrec = df_get_record('rocks', array('RockID'=>$row['RockID']));
				$table .= '<tr><td align="left"><a href="'.$samrec->getURL().'">'.$samrec->getTitle().'</a></td>';
				$table .= '<td align="right"><a href="'.$rockrec->getURL().'">'.$rockrec->getTitle().'</a></td>';
				if ($samrec->numRelatedRecords('Thinsections') > 0) {
				    $table .= '<td align="right"><a href="'.$samrec->getURL('-action=related_records_list&-relationship=Thinsections').'">Yes ('.$samrec->numRelatedRecords('Thinsections').')</a></td></tr>';
				}
		        else {
			        $table .= '<td align="right">No</td></tr>';
		        }
			}
			@xf_db_free_result($res);
			$table .= '</table>';
		}
		else {
			$table = '';
		}
		return array(
			'content' => $table,
			'class' => 'left',
			'label' => 'Samples',
			'order' => 10
    );
    }

    function section__structures(&$record){
		$myquery = sprintf("SELECT rocks.RockID,strdata.StrdataID FROM sites JOIN rocks ON sites.SiteID = rocks.SiteID JOIN strdata ON rocks.RockID = strdata.RockID WHERE sites.SiteID =%d", $record->val('SiteID'));
		$res = xf_db_query($myquery, df_db());
		if (xf_db_num_rows($res) > 0) {
			$table = '<table width="100%"><tr><th align="left">Structure</th><th align="right">Rock</th></tr>';
			while ($row = xf_db_fetch_assoc($res) ){
				$strrec = df_get_record('strdata', array('StrdataID'=>$row['StrdataID']));
				$rockrec = df_get_record('rocks', array('RockID'=>$row['RockID']));
				$table .= '<tr><td align="left"><a href="'.$strrec->getURL().'">'.$strrec->getTitle().'</a></td>';
				$table .= '<td align="right"><a href="'.$rockrec->getURL().'">'.$rockrec->getTitle().'</a></td></tr>';
			}
			@xf_db_free_result($res);
			$table .= '</table>';
		}
		else {
			$table = '';
		}
		return array(
			'content' => $table,
			'class' => 'left',
			'label' => 'Structures',
			'order' => 11
    );
    }

    function Description__renderCell( &$record ){
       $string = $record->strval('Description');
       return (strlen($string) > 43) ? substr($string,0,40).'...' : $string;
    }

    function Photo__renderCell( &$record ){
        $photo = $record->getValue('Photo');
        if (!empty($photo)) {
            return '<a href="'.$record->getURL('-action=related_records_list&-relationship=Photos').'">Yes</a>';
        } else {
            return '-';
        }
    }

    function Rock__renderCell( &$record ){
        $rock = $record->getValue('Rock');
        if (!empty($rock)) {
            return '<a href="'.$record->getURL('-action=related_records_list&-relationship=Rocks').'">Yes</a>';
        } else {
            return '-';
        }
    }

    function Sample__renderCell( &$record ){
        $sample = $record->getValue('Sample');
        if (!empty($sample)) {
            #return '<a href="'.$record->getURL('-action=related_records_list&-relationship=Samples').'">Yes</a>';
            return 'Yes';
        } else {
            return '-';
        }
    }

}
?>

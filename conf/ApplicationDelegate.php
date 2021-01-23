<?php
/**
 * A delegate class for the entire application to handle custom handling of 
 * some functions such as permissions and preferences.
 */

function imagecreatefromfile( $filename ) {
    if (!file_exists($filename)) {
        throw new InvalidArgumentException('File "'.$filename.'" not found.');
    }
    switch ( strtolower( pathinfo( $filename, PATHINFO_EXTENSION ))) {
        case 'jpeg':
        case 'jpg':
            return imagecreatefromjpeg($filename);
        break;

        case 'png':
            return imagecreatefrompng($filename);
        break;

        case 'gif':
            return imagecreatefromgif($filename);
        break;

        default:
            throw new InvalidArgumentException('File "'.$filename.'" is not valid jpg, png or gif image.');
        break;
    }
}

function data_uri($file) {
    if (!file_exists($filename)) {
        $image = imagecreatefromfile('images/notfound.png');
        $scaled = imagecreatefromfile('images/notfound.png');
    } else {
        list($width, $height) = getimagesize($file);
        $image = imagecreatefromfile($file);
        $newwidth = 800;
        $newheight = round($newwidth * $height / $width);
        $scaled = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($scaled, $image, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    }
    
    // start buffering
    ob_start();
    imagejpeg($scaled);
    $contents =  ob_get_contents();
    ob_end_clean();
    $base64 = base64_encode($contents);
    imagedestroy($image);
    imagedestroy($scaled);
    return ('data:image/jpeg;base64,' . $base64);
}

class conf_ApplicationDelegate {
    /**
     * Returns permissions array.  This method is called every time an action is 
     * performed to make sure that the user has permission to perform the action.
     * @param record A Dataface_Record object (may be null) against which we check
     *               permissions.
     * @see Dataface_PermissionsTool
     * @see Dataface_AuthenticationTool
     */
     function getPermissions(&$record){
         $auth =& Dataface_AuthenticationTool::getInstance();
         $user =& $auth->getLoggedInUser();
         if ( !isset($user) ) return Dataface_PermissionsTool::NO_ACCESS();
             // if the user is null then nobody is logged in... no access.
             // This will force a login prompt.
         $role = $user->val('Role');
         return Dataface_PermissionsTool::getRolePermissions($role);
             // Returns all of the permissions for the user's current role.
      }
      
      function beforeHandleRequest(){
        $app =& Dataface_Application::getInstance();
        $query =& $app->getQuery();
            // Make sure you assign by reference (i.e. =& )
            // for this if you want to make changes to the query
            
        if ( !isset($query['-sort']) and $query['-table'] <> 'users'){
        	$query['-sort'] = 'Modified desc';
        }
        $app->addHeadContent(
            sprintf('<link rel="stylesheet" type="text/css" href="%s"/>',
                htmlspecialchars(DATAFACE_SITE_URL.'/style.css')
            )
        );
        $app->addHeadContent('<style type="text/css">div#record-search { display: none; }</style>');
        if ( ($query['-table'] == 'photos') or ($query['-table'] == 'tsphotos') ){
            $app->addHeadContent('<style type="text/css">td#dataface-sections-left-column { display: none; }</style>');
        }
    }
}
?>

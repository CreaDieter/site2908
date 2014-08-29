<?php
class Website_Assets_Create {


    /**
     * Checks if an asset-folder exists and builds full path
     *
     * @param $path string path (eg. /user_uploads/user_id)
     */
    public static function checkIfAssetPathExists($path){
        // get the folder object
        $folderobj = Asset_Folder::getByPath($path);

        // folder doesn't exist
        if(!$folderobj){
            // get parent foldername
            $parentfoldere = dirname($path);

            // check if parent exists -> create parent
            self::checkIfAssetPathExists($parentfoldere);

            // get parent folder
            $parent = Asset_Folder::getByPath($parentfoldere);

            // parent exists
            if ($parent){
                // get foldername that needs to be created
                $foldername = str_replace($parent . '/', '', $path);

                // this wasn't the root folder
                if ($foldername != null){
                    $assetFolder = Asset_Folder::create($parent->getId(), array(
                        "type" => 'folder',
                        "userOwner" => 1,
                        "userModification" => 1,
                        "filename" => $foldername
                    ));
                    $assetFolder->save();
                }
            }
        }

        // now we are sure that the folder exists
        $folderobj = Asset_Folder::getByPath($path);
        if ($folderobj){
            return $folderobj->getId();
        }
    }
}
<?php
/**
 *  Copyright Information
 *
 * @copyright: 2018 agentur fipps e.K.
 * @author   : Arne Borchert
 * @license  : LGPL 3.0+
 */

namespace Fipps\FileinfoBundle\Listener;


use Contao\FilesModel;

class HooksListener
{
    /**
     * @param array $arrFiles
     */
    public function setFileInfoAfterUpload(array $arrFiles) {
        foreach ($arrFiles as $file) {
            $oFile = FilesModel::findByPath($file);
            $oFolder = FilesModel::findByUuid($oFile->pid);
            $oFile->meta = $oFolder->meta;
            if (isset($oFolder->copyright)) {
                $oFile->copyright = $oFolder->copyright;
            }
            $oFile->save();
        }
        return;
    }
}
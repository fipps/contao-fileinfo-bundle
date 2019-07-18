<?php
/**
 *  Copyright Information
 *
 * @copyright: 2019 agentur fipps e.K.
 * @author   : Arne Borchert <arne.borchert@fipps.de>
 * @license  : LGPL 3.0+
 */

namespace Fipps\FileinfoBundle\DataContainer;


use DC_Folder;
use FilesModel;
use DataContainer;
use StringUtil;

class FileCallback
{

    /**
     * Bugfix
     *
     * @param string         $val
     * @param \DataContainer $dc
     * @return string
     */
    public function loadMetaCallback($val, DataContainer $dc)
    {
        if (is_array(unserialize($val))) {
            return $val;
        }

        return;
    }

    /**
     * @param string         $val
     * @param \DataContainer $dc
     * @return string
     */
    public function saveMetaCallback($val, DataContainer $dc)
    {
        $arrMetaNew = unserialize($val);
        if (!is_array($arrMetaNew)) {
            return serialize([]);
        }
        if ($dc instanceof DC_Folder) {
            $activeRecord = $dc->activeRecord;
            $strOldMeta   = $activeRecord->meta;
            $arrOldMeta   = unserialize($strOldMeta);
            $folder       = FilesModel::findByUuid($activeRecord->uuid);
            $files        = FilesModel::findByPid($folder->uuid);

            if ($files !== null) {
                foreach ($files as $file) {
                    if ($file instanceof DC_Folder) {
                        continue;
                    }
                    // Falls File Meta leer oder dem alten Meta des Folders entspricht
                    if ($file->meta === null || $file->meta == '' || $file->meta == $strOldMeta) {
                        $file->meta = $val;
                        $file->save();
                        continue;
                    }

                    // Check every lang entry
                    $arrFileMeta = unserialize($file->meta);
                    foreach ($arrMetaNew as $lang => $arrMetaContent) {
                        if (isset($arrFileMeta[$lang])) {
                            if ($arrFileMeta[$lang] == $arrOldMeta[$lang]) {
                                $arrFileMeta[$lang] = $arrMetaContent;
                            }
                        } else {
                            $arrFileMeta[$lang] = $arrMetaContent;
                        }
                    }

                    $file->meta = serialize($arrFileMeta);
                    $file->save();
                }
            }
        }

        return $val;

    }


    /**
     * @param string         $val
     * @param \DataContainer $dc
     * @return string
     */
    public function saveCopyrightCallback(string $val, \DataContainer $dc)
    {
        if ($dc instanceof DC_Folder) {
            $activeRecord = $dc->activeRecord;
            $folder       = FilesModel::findByUuid($activeRecord->uuid);
            $files        = FilesModel::findByPid($folder->uuid);

            if ($files !== null) {
                foreach ($files as $file) {
                    if ($file instanceof DC_Folder) {
                        continue;
                    }
                    if ($file->copyright === null || $file->copyright == '' || $file->copyright == $activeRecord->copyright) {
                        $file->copyright = $val;
                        $file->save();
                        continue;
                    }

                    $aCopyright = StringUtil::deserialize($file->copyright);
                    foreach ($aCopyright as $copyright) {
                        if ($copyright != '') {
                            return $val;
                        }
                    }
                    $file->copyright = $val;
                    $file->save();
                }
            }
        }

        return $val;
    }
}
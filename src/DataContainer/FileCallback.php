<?php
/**
 *  Copyright Information
 *
 * @copyright: 2019 agentur fipps e.K.
 * @author   : Arne Borchert <arne.borchert@fipps.de>
 * @license  : LGPL 3.0+
 */

namespace Fipps\FileinfoBundle\DataContainer;


use Contao\DC_Folder;
use Contao\FilesModel;
use Contao\StringUtil;

class FileCallback
{

    /**
     * @param string         $val
     * @param \DataContainer $dc
     * @return string
     */
    public function saveMetaCallback(string $val, \DataContainer $dc)
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
                    if ($file->meta === null || $file->meta == '' || $file->meta == $activeRecord->meta) {
                        $file->meta = $val;
                        $file->save();
                        continue;
                    }

                    // if $file->meta has an empty array or the elements are the same with the folder's old metas
                    $aLang = StringUtil::deserialize($file->meta);
                    foreach ($aLang as $lang => $aMeta) {
                        foreach ($aMeta as $key => $meta) {
                            if ($meta != '' && $meta != $activeRecord->meta[$lang][$key]) {
                                return val;
                            }
                        }
                    }
                    $file->meta = $val;
                    $file->save();
                }
            }
        }

        return $val;
    }


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
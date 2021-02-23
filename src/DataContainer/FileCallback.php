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
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\System;

class FileCallback
{

    public function addMetaToFolder(DataContainer $dc)
    {
        if (!$dc->id) {
            return;
        }

        $projectDir  = System::getContainer()->getParameter('kernel.project_dir');
        $blnIsFolder = is_dir($projectDir.'/'.$dc->id);

        // Add the meta data when editing folders


        if ($blnIsFolder && version_compare(VERSION, '4.9', '>=')) {
            PaletteManipulator::create()
                ->addField('meta', 'copyright')
                ->applyToPalette('default', $dc->table);
        }
        $GLOBALS['TL_DCA']['tl_files']['fields']['meta']['eval']['tl_class'] = 'clr';

    }

    /**
     * Bugfix
     *
     * @param string         $val
     * @param \DataContainer $dc
     * @return string
     */
    public function loadMetaCallback($val, DataContainer $dc)
    {
        if (is_array(StringUtil::deserialize($val))) {
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
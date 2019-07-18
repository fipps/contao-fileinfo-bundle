<?php
/**
 *  Copyright Information
 *  @copyright: 2019 agentur fipps e.K.
 *  @author   : Arne Borchert <arne.borchert@fipps.de>
 *  @license  : LGPL 3.0+
 */


$GLOBALS['TL_DCA']['tl_files']['fields']['meta']['load_callback'][] = array(\Fipps\FileinfoBundle\DataContainer\FileCallback::class, 'loadMetaCallback');
$GLOBALS['TL_DCA']['tl_files']['fields']['meta']['save_callback'][] = array(\Fipps\FileinfoBundle\DataContainer\FileCallback::class, 'saveMetaCallback');

if (is_array($GLOBALS['TL_DCA']['tl_files']['fields']['copyright'])) {
    $GLOBALS['TL_DCA']['tl_files']['fields']['copyright']['save_callback'][] = array(\Fipps\FileinfoBundle\DataContainer\FileCallback::class, 'saveCopyrightCallback');
}
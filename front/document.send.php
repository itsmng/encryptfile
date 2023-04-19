<?php
/**
 * @package     encryptfile
 * @author      Charlene Auger
 * @copyright   Copyright (c) 2015-2023 FactorFX
 * @license     AGPL License 3.0 or (at your option) any later version
 *              http://www.gnu.org/licenses/agpl-3.0-standalone.html
 * @link        https://www.factorfx.com
 * @since       2023
 *
 * --------------------------------------------------------------------------
 */

include("../../../inc/includes.php");

if (!$CFG_GLPI["use_public_faq"]) {
    Session::checkLoginUser();
}
 
$doc = new Document();
$PluginEncryptfileDocument = new PluginEncryptfileDocument();
 
if (isset($_GET['docid'])) { // docid for document
    if (!$doc->getFromDB($_GET['docid'])) {
       	Html::displayErrorAndDie(__('Unknown file'), true);
    }
 
    if (!file_exists(GLPI_DOC_DIR."/".$doc->fields['filepath'])) {
       Html::displayErrorAndDie(__('File not found'), true); // Not found
 
    } else if ($doc->canViewFile($_GET)) {
		if ($doc->fields['sha1sum'] && $doc->fields['sha1sum'] != sha1_file(GLPI_DOC_DIR."/".$doc->fields['filepath'])) {
	
			Html::displayErrorAndDie(__('File is altered (bad checksum)'), true); // Doc alterated
		} else {
			$context = isset($_GET['context']) ? $_GET['context'] : null;
			$PluginEncryptfileDocument->sendEncrypt($doc, $context);
		}
    } else {
       	Html::displayErrorAndDie(__('Unauthorized access to this file'), true); // No right
    }
} else if (isset($_GET["file"])) { // for other file
    $splitter = explode("/", $_GET["file"], 2);

    if (count($splitter) == 2) {
		$expires_headers = false;
		$send = false;

		if (($splitter[0] == "_dumps")
			&& Session::haveRight("backup", CREATE)) {
			$send = GLPI_DUMP_DIR . '/' . $splitter[1];
		}
	
		if ($splitter[0] == "_pictures") {
			if (Document::isImage(GLPI_PICTURE_DIR . '/' . $splitter[1])) {
				// Can use expires header as picture file path changes when picture changes.
				$expires_headers = true;
				$send = GLPI_PICTURE_DIR . '/' . $splitter[1];
			}
		}
	
		if ($send && file_exists($send)) {
			Toolbox::sendFile($send, $splitter[1], null, $expires_headers);
		} else {
			Html::displayErrorAndDie(__('Unauthorized access to this file'), true);
		}
    } else {
       	Html::displayErrorAndDie(__('Invalid filename'), true);
    }
}
 
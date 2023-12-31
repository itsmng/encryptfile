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

Session::checkRight("plugin_encryptfile_configs", READ);

if (empty($_GET["id"])) {
	$_GET["id"] = "";
}

$pluginEncryptfileConfigs = new PluginEncryptfileConfig();
$pluginEncryptfileEncrypt = new PluginEncryptfileEncrypt();

if (isset($_POST["add"])) {
	$pluginEncryptfileConfigs->check(-1, CREATE);
	$_POST["key"] = $pluginEncryptfileEncrypt->generateKey();
	if ($newID = $pluginEncryptfileConfigs->add($_POST)) {
		if ($_SESSION['glpibackcreated']) {
			Html::redirect($pluginEncryptfileConfigs->getFormURL() . "?id=" . $newID);
		}
	}

	Html::redirect($CFG_GLPI["root_doc"] . "/plugins/encryptfile/front/config.php");
} else if (isset($_POST["purge"])) {
	$pluginEncryptfileConfigs->check($_POST["id"], PURGE);
	$associatedDocuments = $pluginEncryptfileConfigs->getAllAssociatedDocument($_POST["id"]);

	if(empty($associatedDocuments)) {
		$pluginEncryptfileConfigs->delete($_POST, 1);
		$pluginEncryptfileConfigs->removeAssociatedConfig($_POST["id"]);
		$pluginEncryptfileConfigs->redirectToList();
	} else {
		Session::addMessageAfterRedirect(__("Unable to purge the key because documents are still associated with it", "encryptfile"), true, ERROR);
		Html::back();	 
	}
} else if (isset($_POST["update"])) {
	$pluginEncryptfileConfigs->check($_POST["id"], UPDATE);
	if(isset($_POST["profiles_id_reading"])) {
		$pluginEncryptfileConfigs->updateReadingProfiles($_POST["id"], $_POST["profiles_id_reading"]);
	} elseif(isset($_POST["itemtype"])) {
		$pluginEncryptfileConfigs->updateItemtype($_POST["id"], $_POST["itemtype"]);
	} elseif(isset($_POST["forms_id"])) {
		$pluginEncryptfileConfigs->updateFormcreator($_POST["id"], $_POST["forms_id"], $_POST["sections_id"], $_POST["questions_id"]);
	} else {
		$pluginEncryptfileConfigs->update($_POST);
	}
	
	Html::back();
} elseif(isset($_GET["removeid"])) {
	$pluginEncryptfileConfigs->removeFormConfig($_GET["removeid"]);
	Html::back();
} else {
	Html::header(PluginEncryptfileConfig::getTypeName(2), $_SERVER['PHP_SELF'], "tools", "PluginEncryptfileConfig");
	$pluginEncryptfileConfigs->display(array('id' => $_GET["id"]));
	Html::footer();
}
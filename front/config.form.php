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

if (isset($_POST["add"])) {
	$pluginEncryptfileConfigs->check(-1, CREATE);

	if ($newID = $pluginEncryptfileConfigs->add($_POST)) {
		if ($_SESSION['glpibackcreated']) {
			Html::redirect($pluginEncryptfileConfigs->getFormURL() . "?id=" . $newID);
		}
	}

	Html::redirect($CFG_GLPI["root_doc"] . "/plugins/encryptfile/front/config.php");
} else if (isset($_POST["purge"])) {
	$pluginEncryptfileConfigs->check($_POST["id"], PURGE);
	$pluginEncryptfileConfigs->delete($_POST, 1);
	$pluginEncryptfileConfigs->redirectToList();
} else if (isset($_POST["update"])) {
	$pluginEncryptfileConfigs->check($_POST["id"], UPDATE);
	$pluginEncryptfileConfigs->update($_POST);
	Html::back();
} else {
	Html::header(PluginEncryptfileConfig::getTypeName(2), $_SERVER['PHP_SELF'], "tools", "PluginEncryptfileConfig");
	$pluginEncryptfileConfigs->display(array('id' => $_GET["id"]));
	Html::footer();
}
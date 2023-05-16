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
define('PLUGIN_ENCRYPTFILE_VERSION', '1.0.0');

/**
 * Init the hooks of the plugins -Needed
 **/
function plugin_init_encryptfile() {
	global $PLUGIN_HOOKS;

	$PLUGIN_HOOKS['csrf_compliant']['encryptfile'] = true;

	Plugin::registerClass(PluginEncryptfileProfile::class, ['addtabon' => ['Profile']]);
	Plugin::registerClass(PluginEncryptfileEncrypt::class);

	$PLUGIN_HOOKS['change_profile']['encryptfile']   = array(PluginEncryptfileProfile::class, 'initProfile');

	if(Session::haveRight("plugin_encryptfile_configs", READ)) {
        $PLUGIN_HOOKS['menu_toadd']['encryptfile'] = array('tools' => PluginEncryptfileConfig::class);
    }

	// Document 
	$PLUGIN_HOOKS['pre_item_add']['encryptfile']['Document'] = array(PluginEncryptfileEncrypt::class, 'beforeAddDocument');
	$PLUGIN_HOOKS['item_add']['encryptfile']['Document'] = array(PluginEncryptfileEncrypt::class, 'afterAddDocument');
	$PLUGIN_HOOKS['item_purge']['encryptfile']['Document'] = array(PluginEncryptfileConfig::class, 'afterPurgeDocument');

	// Ticket attachment
	$PLUGIN_HOOKS['pre_item_add']['encryptfile']['Ticket'] = array(PluginEncryptfileEncrypt::class, 'beforeAddTicket');

	// Formcreator submission
	if ((new Plugin())->isActivated('formcreator')) {
		$PLUGIN_HOOKS['pre_item_add']['encryptfile']['PluginFormcreatorFormAnswer'] = array(PluginEncryptfileEncrypt::class, 'beforeAddFormAnswer');
	}

	// Load js only if read right checked
	if(Session::haveRight("plugin_encryptfile_encrypt", READ)) {
		$PLUGIN_HOOKS['add_javascript']['encryptfile'][] = 'js/read.js';
	}

	if(Session::haveRight("plugin_encryptfile_configs", READ)) {
		$PLUGIN_HOOKS['add_javascript']['encryptfile'][] = 'js/function.js';
	}

	$PluginEncryptfileConfig = new PluginEncryptfileConfig();
	if(isset($_SESSION["glpiactiveprofile"]["id"])) {
		$secretKeyId = $PluginEncryptfileConfig->getSecretKeyId($_SESSION["glpiactiveprofile"]["id"]);
	}
	
	// Load js only if write right checked and have a configured key
	if(Session::haveRight("plugin_encryptfile_encrypt", UPDATE) && !is_null($secretKeyId)) {
		if(in_array(explode("?", $_SERVER['REQUEST_URI'])[0], $PluginEncryptfileConfig->getAuthorizedItem($secretKeyId))) {
			$PLUGIN_HOOKS['add_javascript']['encryptfile'][] = 'js/write.js';
		}
	}
}


/**
 * Get the name and the version of the plugin - Needed
 **/
function plugin_version_encryptfile() {
	return [
		'name'         => __("Encrypted file", "encryptfile"),
		'version'      => PLUGIN_ENCRYPTFILE_VERSION,
		'author'       => 'Charlène AUGER',
		'license'      => 'GPLv2+',
		'homepage'     => '',
		'requirements' => [
			'glpi' => [
				'min' => '9.5',
				'dev' => false
			]
		]
	];

}


/**
 * Optional : check prerequisites before install : may print errors or add to message after redirect
 **/
function plugin_encryptfile_check_prerequisites() {
	if (version_compare(GLPI_VERSION, '9.5', 'lt') || version_compare(GLPI_VERSION, '9.6', 'ge')) {
		return false;
	}
	
	return true;
}


// Uninstall process for plugin : need to return true if succeeded : may display messages or add to message after redirect
/**
 * @return bool
 */
function plugin_encryptfile_check_config() {
   	return true;
}

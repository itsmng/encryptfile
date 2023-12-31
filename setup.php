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
define('PLUGIN_ENCRYPTFILE_VERSION', '1.1.1');

define('PLUGIN_ENCRYPTFILE_MIN_ITSMNG', '1.5.1');
define('PLUGIN_ENCRYPTFILE_MAX_ITSMNG', '1.5.1');

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

	// Followup attachment
	$PLUGIN_HOOKS['pre_item_add']['encryptfile']['ITILFollowup'] = array(PluginEncryptfileEncrypt::class, 'beforeAddFollowup');

	//Task attachment
	$PLUGIN_HOOKS['pre_item_add']['encryptfile']['TicketTask'] = array(PluginEncryptfileEncrypt::class, 'beforeAddTask');

	// Formcreator submission
	if ((new Plugin())->isActivated('formcreator')) {
		$PLUGIN_HOOKS['pre_item_add']['encryptfile']['PluginFormcreatorFormAnswer'] = array(PluginEncryptfileEncrypt::class, 'beforeAddFormAnswer');
	}

	$canDecrypt = false;
	$canEncrypt = false;

	if(isset($_SESSION["glpiID"]) && isset($_SESSION["glpiactiveprofile"]["interface"]) && $_SESSION["glpiactiveprofile"]["interface"] == "helpdesk") {
		$canDecrypt = Profile::haveUserRight($_SESSION["glpiID"], "plugin_encryptfile_encrypt", READ, $_SESSION["glpiactive_entity"]);
		$canEncrypt = Profile::haveUserRight($_SESSION["glpiID"], "plugin_encryptfile_encrypt", UPDATE, $_SESSION["glpiactive_entity"]);
	}

	// Load js only if read right checked
	if(Session::haveRight("plugin_encryptfile_encrypt", READ) || $canDecrypt) {
		$PLUGIN_HOOKS['add_javascript']['encryptfile'][] = 'js/read.js';
	}

	if(Session::haveRight("plugin_encryptfile_configs", READ)) {
		$PLUGIN_HOOKS['add_javascript']['encryptfile'][] = 'js/function.js';
	}

	if ((new Plugin())->isInstalled('encryptfile')) {
		$PluginEncryptfileConfig = new PluginEncryptfileConfig();
		if(isset($_SESSION["glpiactiveprofile"]["id"])) {
			$secretKeyId = $PluginEncryptfileConfig->getSecretKeyId($_SESSION["glpiactiveprofile"]["id"]);
		}
		
		// Load js only if write right checked and have a configured key
		if((Session::haveRight("plugin_encryptfile_encrypt", UPDATE) || $canEncrypt) && !is_null($secretKeyId)) {
			if(in_array(explode("?", $_SERVER['REQUEST_URI'])[0], $PluginEncryptfileConfig->getAuthorizedItem($secretKeyId))) {
				$PLUGIN_HOOKS['add_javascript']['encryptfile'][] = 'js/write.js';
			}
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
		'homepage'     => 'https://github.com/itsmng/encryptfile',
		'requirements' => [
			'glpi' => [
				'min' => PLUGIN_ENCRYPTFILE_MIN_ITSMNG,
				'dev' => false
			]
		]
	];

}


/**
 * Optional : check prerequisites before install : may print errors or add to message after redirect
 **/
function plugin_encryptfile_check_prerequisites() {
	if (version_compare(ITSM_VERSION, PLUGIN_ENCRYPTFILE_MIN_ITSMNG, 'lt') && version_compare(ITSM_VERSION, PLUGIN_ENCRYPTFILE_MAX_ITSMNG, 'ge')) {
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

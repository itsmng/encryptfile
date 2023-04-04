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

	$PLUGIN_HOOKS['change_profile']['encryptfile']   = array(PluginEncryptfileProfile::class, 'initProfile');

	if(Session::haveRight("plugin_encryptfile_configs", READ)) {
        $PLUGIN_HOOKS['menu_toadd']['encryptfile'] = array('tools' => PluginEncryptfileConfig::class);
    }
}


/**
 * Get the name and the version of the plugin - Needed
 **/
function plugin_version_encryptfile() {
	return [
		'name'         => "Encrypted file",
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

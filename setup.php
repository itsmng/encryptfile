<?php
/*** 
-------------------------------------------------------------------------
 encryptfile plugin for GLPI
-------------------------------------------------------------------------
 ***/
define('PLUGIN_ENCRYPTFILE_VERSION', '1.0.0');

/**
 * Init the hooks of the plugins -Needed
 **/
function plugin_init_encryptfile() {
	global $PLUGIN_HOOKS;

	$PLUGIN_HOOKS['csrf_compliant']['encryptfile'] = true;

	Plugin::registerClass(PluginEncryptfileProfile::class, ['addtabon' => ['Profile']]);
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

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

include('../../../inc/includes.php');

Session::checkRight("plugin_encryptfile_configs", READ);

Html::header(PluginEncryptfileConfig::getTypeName(2), $_SERVER['PHP_SELF'], "tools", "PluginEncryptfileConfig");

Search::show(PluginEncryptfileConfig::class);

Html::footer();
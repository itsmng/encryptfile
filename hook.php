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

/**
 * plugin_encryptfile_install
 *
 * @return void
 */
function plugin_encryptfile_install() {
    global $DB;

    $migration = new Migration(100);

    // Create configuration table
    if(!$DB->tableExists("glpi_plugin_encryptfile_configs")) {
        $query = "CREATE TABLE `glpi_plugin_encryptfile_configs` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(255) NOT NULL,
            `status` BOOLEAN NOT NULL DEFAULT 0,
            `comment` VARCHAR(255) DEFAULT NULL,
            `profiles_id` INT(11) NOT NULL DEFAULT 0,
            `key` TEXT NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

        $DB->queryOrDie($query, "Create configuration table for Encrypted file plugin");
    }

    // Create profile table
    if(!$DB->tableExists("glpi_plugin_encryptfile_profiles")) {
        $query = "CREATE TABLE `glpi_plugin_encryptfile_profiles` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `keys_id` INT(11) NOT NULL DEFAULT 0,
            `profiles_id` INT(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

        $DB->queryOrDie($query, "Create profile table for Encrypted file plugin");
    }

    // Create item table
    if(!$DB->tableExists("glpi_plugin_encryptfile_items")) {
        $query = "CREATE TABLE `glpi_plugin_encryptfile_items` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `keys_id` INT(11) NOT NULL DEFAULT 0,
            `itemtype` varchar(255) NOT NULL DEFAULT '',
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

        $DB->queryOrDie($query, "Create item table for Encrypted file plugin");
    }

    if(!$DB->tableExists("glpi_plugin_encryptfile_documents")) {
        $query = "CREATE TABLE `glpi_plugin_encryptfile_documents` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `keys_id` INT(11) NOT NULL DEFAULT 0,
            `documents_id` INT(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

        $DB->queryOrDie($query, "Create item table for Encrypted file plugin");
    }

    if(!$DB->tableExists("glpi_plugin_encryptfile_formcreator")) {
        $query = "CREATE TABLE `glpi_plugin_encryptfile_formcreator` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `keys_id` INT(11) NOT NULL DEFAULT 0,
            `forms_id` INT(11) NOT NULL DEFAULT 0,
            `sections_id` INT(11) NOT NULL DEFAULT 0,
            `questions_id` INT(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

        $DB->queryOrDie($query, "Create item table for Encrypted file plugin");
    }

    $migration->executeMigration();
    return true;
}

/**
 * plugin_encryptfile_uninstall
 *
 * @return void
 */
function plugin_encryptfile_uninstall() {
    global $DB;

    $pluginEncryptfileConfigs = new PluginEncryptfileConfig();
    $associatedDocuments = $pluginEncryptfileConfigs->getAllAssociatedDocument(0, true, true);

    if(empty($associatedDocuments)) {
        $queries = [
            "glpi_plugin_encryptfile_configs"       => "DROP TABLE IF EXISTS `glpi_plugin_encryptfile_configs`;",
            "glpi_plugin_encryptfile_profiles"      => "DROP TABLE IF EXISTS `glpi_plugin_encryptfile_profiles`;",
            "glpi_plugin_encryptfile_items"         => "DROP TABLE IF EXISTS `glpi_plugin_encryptfile_items`;",
            "glpi_plugin_encryptfile_documents"     => "DROP TABLE IF EXISTS `glpi_plugin_encryptfile_documents`;",
            "glpi_plugin_encryptfile_formcreator"   => "DROP TABLE IF EXISTS `glpi_plugin_encryptfile_formcreator`;"
        ];

        foreach($queries as $table => $query) $DB->queryOrDie($query, "Drop table ".$table);

        return true;
    } else {
        Session::addMessageAfterRedirect(__("Unable to drop encryptfile tables because there are still documents associated with the keys ", "encryptfile"), true, ERROR);
        return false;
    }
}
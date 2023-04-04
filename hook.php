<?php
/*** 
-------------------------------------------------------------------------
 encryptfile plugin for GLPI
-------------------------------------------------------------------------
 ***/

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
            `description` VARCHAR(255) DEFAULT NULL,
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
            `status` TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

        $DB->queryOrDie($query, "Create profile table for Encrypted file plugin");
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

    $queries = [
        "glpi_plugin_encryptfile_configs" => "DROP TABLE IF EXISTS `glpi_plugin_encryptfile_configs`;",
        "glpi_plugin_encryptfile_profiles" => "DROP TABLE IF EXISTS `glpi_plugin_encryptfile_profiles`;"
    ];

    foreach($queries as $table => $query) $DB->queryOrDie($query, "Drop table ".$table);

    return true;
}
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

if(isset($_GET["forms_id"]) && $_GET["forms_id"] != "0") {
    $PluginFormcreatorSection = new PluginFormcreatorSection();
    $findFormSections = $PluginFormcreatorSection->find(["plugin_formcreator_forms_id" => $_GET["forms_id"]]);

    $formSections = [
        "0" => "-----"
    ];

    if(!empty($findFormSections)) foreach($findFormSections as $id => $sections) {
        $formSections[$id] = $sections["name"];
    }

    Dropdown::showFromArray("sections_id", $formSections, array("on_change" => "displaySectionQuestion();"));
}

if(isset($_GET["sections_id"]) && $_GET["sections_id"] != "0") {
    $PluginFormcreatorQuestion = new PluginFormcreatorQuestion();
    $findFormQuestions = $PluginFormcreatorQuestion->find(["plugin_formcreator_sections_id" => $_GET["sections_id"]]);

    $formQuestions = [
        "0" => "-----"
    ];

    if(!empty($findFormQuestions)) foreach($findFormQuestions as $id => $questions) {
        $formQuestions[$id] = $questions["name"];
    }

    Dropdown::showFromArray("questions_id", $formQuestions, array());
}
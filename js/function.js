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

function displayFormSection() {
    var formId = $('[name="forms_id"]').find(":selected").val();
    
    var RegexUrl = /^(.*)front\/.*\.form\.php/;
    var RegexUrlRes = RegexUrl.exec(window.location.pathname);

    $.ajax({
        url : RegexUrlRes[1] + 'ajax/getFormQuestion.php',
        type : 'GET',
        dataType : 'html',
        data : {
            'forms_id' : formId
        },
        success : function(data){
            $("#sections_to_replace").empty();
            $("#sections_to_replace").append(data);
        },
        error : function () {
            console.log("Error");
        }
    });
}

function displaySectionQuestion() {
    var sectionId = $('[name="sections_id"]').find(":selected").val();
    
    var RegexUrl = /^(.*)front\/.*\.form\.php/;
    var RegexUrlRes = RegexUrl.exec(window.location.pathname);

    $.ajax({
        url : RegexUrlRes[1] + 'ajax/getFormQuestion.php',
        type : 'GET',
        dataType : 'html',
        data : {
            'sections_id' : sectionId
        },
        success : function(data){
            $("#questions_to_replace").empty();
            $("#questions_to_replace").append(data);
        },
        error : function () {
            console.log("Error");
        }
    });
}
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

$(document).ajaxStop(function() {
    setTimeout(function() {
        $('form').each(function() {
            if (($(this).is('[action*="front/document.form.php"') || $(this).find('#fileupload_info_ticket').length == 1) && $('#encryptfile').length == 0) {
                addEncryptedFileCheckbox($(this));
            }
        });
    }, 500);
});

function addEncryptedFileCheckbox(form) {
    var RegexUrl = /^(.*)front\/.*\.form\.php/;
    var RegexUrlRes = RegexUrl.exec(window.location.pathname);

    $.ajax({
        url : RegexUrlRes[1] + 'plugins/encryptfile/ajax/getItem.php',
        type : 'GET',
        dataType : 'html',
        data : {
            'item_url' : RegexUrlRes[0]
        },
        success : function(data){
            var input = form.find('input[name="add"]');
            if(input.length == 0) input = form.find('button[name="add"]');
            $(data).insertBefore(input[0]);
        },
        error : function () {
            console.log("Error when attempt to insert encrypted file checkbox");
        }
    });
}
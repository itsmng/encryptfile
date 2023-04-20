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
        $('a').each(function() {
            if ($(this).is('[href*="docid"') && !$(this).is('[href*="encryptfile"')) {
                var href = $(this).attr('href');
                href = href.replace('front/document.send.php', 'plugins/encryptfile/front/document.send.php');
                $(this).attr('href', href);
            }
        });
    }, 500);
});
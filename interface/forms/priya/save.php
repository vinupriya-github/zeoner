<?php
/*
 * This saves the submitted form
 */
/**
 * example2 save.php
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2018 Brady Miller <brady.g.miller@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */


require_once("../../globals.php");
require_once("$srcdir/api.inc");
require_once("$srcdir/forms.inc");

use OpenEMR\Common\Csrf\CsrfUtils;

if (!CsrfUtils::verifyCsrfToken($_POST["csrf_token_form"])) {
    CsrfUtils::csrfNotVerified();
}

/** CHANGE THIS - name of the database table associated with this form **/
$table_name = "alergy";

/** CHANGE THIS name to the name of your form **/
$form_name = "Alergy";

/** CHANGE THIS to match the folder you created for this form **/
$form_folder = "priya";


if ($encounter == "") {
    $encounter = date("Ymd");
}
if ($_POST['alergyname']) {
//              print_r($_POST);die();
                if(!filter_input(INPUT_POST | INPUT_GET, 'alergyname', FILTER_SANITIZE_SPECIAL_CHARS)){
                $_POST['alergyname']=  preg_replace('/[^A-Za-z0-9 ]/', '', $_POST['alergyname']);
                }

        }
        if($_POST['notes']){
                if(!filter_input(INPUT_POST | INPUT_GET, 'notes', FILTER_SANITIZE_SPECIAL_CHARS)){
                $_POST['notes']=  preg_replace('/[^A-Za-z0-9 ]/', '', $_POST['notes']);
                }

        }

/*if(!$_POST['active']){
	$_POST['active']=0;
}*/
if(!isset($_POST['active'])){
$_POST['active']=0;
}
$_POST['startdate']= date("Y-m-d", strtotime($_POST['startdate']));
if ($_GET["mode"] == "new") {
    /* NOTE - for customization you can replace $_POST with your own array
     * of key=>value pairs where 'key' is the table field name and
     * 'value' is whatever it should be set to
     * ex)   $newrecord['parent_sig'] = $_POST['sig'];
     *       $newid = formSubmit($table_name, $newrecord, $_GET["id"], $userauthorized);
     */

	/* save the data into the form's own table */

	//$_POST['startdate']= date("Y-m-d", strtotime($_POST['startdate']));

   $newid = formSubmit($table_name, $_POST, $_GET["id"], $userauthorized);

    /* link the form to the encounter in the 'forms' table */
    addForm($encounter, $form_name, $newid, $form_folder, $pid, $userauthorized);
} elseif ($_GET["mode"] == "update") {
	/* update existing record */

//	print_r($_POST);die();

    $success = formUpdate($table_name, $_POST, $_GET["id"], $userauthorized);
}

$_SESSION["encounter"] = $encounter;
formHeader("Redirecting....");
formJump();
formFooter();


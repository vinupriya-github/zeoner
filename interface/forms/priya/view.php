<?php
/**
 * Sports Physical Form
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Jason Morrill
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2018 Brady Miller <brady.g.miller@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */


require_once("../../globals.php");
require_once("$srcdir/api.inc");

use OpenEMR\Common\Csrf\CsrfUtils;

/** CHANGE THIS - name of the database table associated with this form **/
$table_name = "alergy";

/** CHANGE THIS name to the name of your form **/
$form_name = "Alergy";

/** CHANGE THIS to match the folder you created for this form **/
$form_folder = "priya";

formHeader("Form: ".$form_name);
$returnurl = 'encounter_top.php';

/* load the saved record */
$record = formFetch($table_name, $_GET["id"]);

/* remove the time-of-day from the date fields */
if ($record['form_date'] != "") {
    $dateparts = explode(" ", $record['form_date']);
    $record['form_date'] = $dateparts[0];
}

if ($record['dob'] != "") {
    $dateparts = explode(" ", $record['dob']);
    $record['dob'] = $dateparts[0];
}

if ($record['sig_date'] != "") {
    $dateparts = explode(" ", $record['sig_date']);
    $record['sig_date'] = $dateparts[0];
}
?>
<html><head>

<!-- supporting javascript code -->
<script type="text/javascript" src="<?php echo $GLOBALS['assets_static_relative']; ?>/jquery/dist/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/textformat.js?v=<?php echo $v_js_includes; ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['assets_static_relative']; ?>/jquery-datetimepicker/build/jquery.datetimepicker.full.min.js"></script>

<!-- page styles -->
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" href="../../forms/<?php echo $form_folder; ?>/style.css?v=<?php echo $v_js_includes; ?>" type="text/css">
<link rel="stylesheet" href="<?php echo $GLOBALS['assets_static_relative']; ?>/jquery-datetimepicker/build/jquery.datetimepicker.min.css">

<script language="JavaScript">
function PrintForm() {
    newwin = window.open("<?php echo "http://".$_SERVER['SERVER_NAME'].$rootdir."/forms/".$form_folder."/print.php?id=" ?>" + <?php echo js_url($_GET["id"]); ?>,"mywin");
}
</script>
 <style type="text/css" title="mystyles" media="all">
            @media only screen and (max-width: 768px) {
                [class*="col-"] {
                width: 100%;
                text-align:left!Important;
            }
        </style>

</head>
<body class="body_top">



<form method=post action="<?php echo $rootdir;?>/forms/<?php echo $form_folder; ?>/save.php?mode=update&id=<?php echo attr_url($_GET["id"]);?>" name="my_form">
<input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
<div class="row">
                <div class="page-header">
                    <h2><?php echo xlt('Alergy Care Form'); ?></h2>
                </div>
            </div>



<div id="form_container">

<div id="general">
<table>
<tr>
<td>
	Alergyname:
</td><td>
 	<input name="alergyname" id="alergyname" type="text" size="15" maxlength="15" value="<?php echo attr($record['alergyname']);?>">
</td>
</tr>

<tr><td>

Alergytype :
</td><td>
<select name="alergytype" id="alergytype">
        <option <?php if($record['alergytype']=="penicillin") {echo "selected";}?> value="<?php echo attr('penicillin');?>">penicillin</option>
        <option <?php if($record['alergytype']=="sulfa") {echo "selected";}?> value="<?php echo attr('sulfa');?>">sulfa</option>
        <option <?php if($record['alergytype']=="iodine") {echo "selected";}?> value="<?php echo attr('iodine');?>">iodine</option>
</select>
</td></tr>

<tr><td>
Startdate:
</td><td>
   <input type='text' size='10' class='datepicker' name='startdate' id='startdate'
    value='<?php $startdate=date("d-m-Y", strtotime($record['startdate']));echo $startdate; attr('startdate');?>'
    title='<?php echo xla('dd-mm-yyyy'); ?>' />
</td></tr>

<tr><td>
Notes:</td><td>
	 <textarea name="notes" id="notes" rows="4" cols="50" maxlength="50"><?php echo attr($record['notes']);?></textarea>
</td></tr>
<tr><td>
	Active:
</td><td>
	<input type="checkbox" name="active" id="active" <?php if($record['active']=="on") {echo "checked";}?>>
</td>
<tr>
<td>Alergy:
</td><td>
	<input type="radio" name="normalalergyfood" id="normalalergy" <?php if($record['normalalergyfood']=="normalaler") {echo "checked";}?> value="<?php echo attr('normalaler');?>">NORMAL
	 <input type="radio" name="normalalergyfood" id="foodalergy"<?php if($record['normalalergyfood']=="foodalergy") {echo "checked";}?> value="<?php echo attr('foodalergy');?>">FOOD
 <input type="radio" name="normalalergyfood" id="drugalergy"<?php if($record['normalalergyfood']=="drugalergy") {echo "checked";}?> value="<?php echo attr('drugalergy');?>">DRUG

</td></tr>
</table>
</div>

</div>

<input type="button" class="save" value="<?php echo xla('Save Changes'); ?>"> &nbsp;
<input type="button" class="dontsave" value="<?php echo xla('Don\'t Save Changes'); ?>"> &nbsp;

</form>
</body>
<script language="javascript">
// jQuery stuff to make the page a little easier to use


$(function(){
    $(".save").click(function() { top.restoreSession(); document.my_form.submit(); });
    $(".dontsave").click(function() { parent.closeTab(window.name, false); });
    $(".printform").click(function() { PrintForm(); });

    $('.datepicker').datetimepicker({
        <?php $datetimepicker_timepicker = false; ?>
        <?php $datetimepicker_showseconds = false; ?>
        <?php $datetimepicker_formatInput = false; ?>
        <?php require($GLOBALS['srcdir'] . '/js/xl/jquery-datetimepicker-2-5-4.js.php'); ?>
        <?php // can add any additional javascript settings to datetimepicker here; need to prepend first setting with a comma ?>
    });
});

</script>


</html>


<?php
/**
 * The page shown when the user requests a new form
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2018 Brady Miller <brady.g.miller@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */


require_once("../../globals.php");
require_once("$srcdir/api.inc");

use OpenEMR\Common\Csrf\CsrfUtils;

/** CHANGE THIS name to the name of your form **/
$form_name = "Alergy";

/** CHANGE THIS to match the folder you created for this form **/
$form_folder = "priya";

formHeader("Form: ".$form_name);

$returnurl = 'encounter_top.php';
?>

<html><head>

<!-- other supporting javascript code -->
<script type="text/javascript" src="<?php echo $GLOBALS['assets_static_relative']; ?>/jquery/dist/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/textformat.js?v=<?php echo $v_js_includes; ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['assets_static_relative']; ?>/jquery-datetimepicker/build/jquery.datetimepicker.full.min.js"></script>

<!-- page styles -->
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" href="../../forms/<?php echo $form_folder; ?>/style.css?v=<?php echo $v_js_includes; ?>" type="text/css">
<link rel="stylesheet" href="<?php echo $GLOBALS['assets_static_relative']; ?>/jquery-datetimepicker/build/jquery.datetimepicker.min.css">
 <style type="text/css" title="mystyles" media="all">
            @media only screen and (max-width: 768px) {
                [class*="col-"] {
                width: 100%;
                text-align:left!Important;
	    }
	.general{
	width:60%;
	height:80%;
	background-color:gray;
	
	}
        </style>

</head>
<body class="body_top">


	<div class="row">
                <div class="page-header">
                    <h2><?php echo xlt('Alergy Care Form'); ?></h2>
                </div>
            </div>
<div class="general">  
<form method=post action="<?php echo $rootdir;?>/forms/<?php echo $form_folder; ?>/save.php?mode=new" name="my_form">
<input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
<div id="form_container">
<table>

<tr>
<td>
	Alergyname: 
</td>
<td>
	<input name="alergyname" id="alergyname" type="text" size="15" maxlength="15">
</td>
</tr>
<tr>
<td>
	Alergytype :
</td>
<td>
	<select name="alergytype" id="alergytype">
	<option value="penicillin">penicillin</option>
	<option value="sulfa">sulfa</option>
	<option value="iodine">iodine</option>


	</select>
</td>
</tr>

<tr>
<td>
	Start Date:
</td>
<td>   
	<input type='text' size='10' class='datepicker' name='startdate' id='startdate'
    	value='<?php echo date('d-m-Y', time()); ?>'
    	title='<?php echo xla('dd-mm-yyyy'); ?>' />
</td>
</tr>

<tr>
<td>
	Notes:
</td>
<td>
 	<textarea name="notes" id="notes" rows="4" cols="50" maxlength="50" ></textarea>
</td>
</tr>

<tr>
<td>
	Active:
</td>
<td>
 	<input name="active" id="active" type="checkbox" size="80" maxlength="250">
</td>
</tr>
<tr>
<td>Alergy:</td>
<td>
	<input name="normalalergyfood" id="normalalergy" type="radio" value="normalalergy">NORMAL
	<input name="normalalergyfood" id="foodalergy" type="radio" value="foodalergy">FOOD
	<input name="normalalergyfood" id="drugalergy" type="radio" value="drugalergy">DRUG
</td>
</tr>

</table>
</div>
<!-- Save/Cancel buttons -->
<input type="button" class="save" value="<?php echo xla('Save'); ?>"> &nbsp;
<input type="button" class="dontsave" value="<?php echo xla('Don\'t Save'); ?>"> &nbsp;

<!-- container for the main body of the form -->

</form>
</div>
</body>

<script language="javascript">

// jQuery stuff to make the page a little easier to use

$(function(){
    $(".save").click(function() { top.restoreSession(); document.my_form.submit(); });
    $(".dontsave").click(function() { parent.closeTab(window.name, false); });

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


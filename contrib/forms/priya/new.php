<?php

/**
* Review of Systems Checks form
*
* @package   OpenEMR
* @link      http://www.open-emr.org
* @author    sunsetsystems <sunsetsystems>
* @author    cfapress <cfapress>
* @author    Brady Miller <brady.g.miller@gmail.com>
* @copyright Copyright (c) 2009 sunsetsystems <sunsetsystems>
* @copyright Copyright (c) 2008 cfapress <cfapress>
* @copyright Copyright (c) 2016-2019 Brady Miller <brady.g.miller@gmail.com>
* @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
*/


require_once("../../globals.php");
require_once("$srcdir/api.inc");

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Core\Header;

$returnurl = 'encounter_top.php';
?>
<html>
 <title><?php echo xlt("Alergy care plan"); ?></title>
<div class="container">
    <div class="row">
        <div class="">
            <div class="page-header">
                <h2><?php echo xlt("Alergy care plan");?></h2>
            </div>
        </div>
    </div>
	 <div class="row">
		<form method=post action="<?php echo $rootdir;?>/forms/priya/save.php?mode=new" name="my_form" onsubmit="return top.restoreSession()">
		<div class="form-group">
			<label>
                                        <input type="textfield" name='alergyname'><?php echo xlt('Alergy Name');?>
			 </label>

		</div>
		<div class="form-group clearfix">
                    <div class="col-sm-12 col-sm-offset-1 position-override">
                        <div class="btn-group oe-opt-btn-group-pinch" role="group">
                        <button type="submit" onclick="top.restoreSession()" class="btn btn-default btn-save"><?php echo xlt('Save'); ?></button>
                        <button type="button" class="btn btn-link btn-cancel oe-opt-btn-separate-left" onclick="top.restoreSession(); parent.closeTab(window.name, false);"><?php echo xlt('Cancel');?></button>
                    </div>
                </div>

	</div>

</div>

</html>

<?php /* Smarty version 2.6.31, created on 2020-06-16 10:19:15
         compiled from default/views/month/ajax_template.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'config_load', 'default/views/month/ajax_template.html', 10, false),array('modifier', 'date_format', 'default/views/month/ajax_template.html', 351, false),array('modifier', 'string_format', 'default/views/month/ajax_template.html', 352, false),)), $this); ?>
<?php echo smarty_function_config_load(array('file' => "default.conf"), $this);?>

<?php echo smarty_function_config_load(array('file' => "lang.".($this->_tpl_vars['USER_LANG'])), $this);?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['TPL_NAME'])."/views/header.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php 
/* if you change these be sure to change their matching values in
 * the CSS for the calendar, found in interface/themes/ajax_calendar.css
 */
$timeslotHeightVal=20;
$timeslotHeightUnit="px";
 ?>

<script language='JavaScript'>
 // This is called from the event editor popup.
 function refreshme() {
  top.restoreSession();
  document.forms[0].submit();
 }

 function newEvt(startampm, starttimeh, starttimem, eventdate, providerid, catid) {
  dlgopen('add_edit_event.php?startampm=' + encodeURIComponent(startampm) +
   '&starttimeh=' + encodeURIComponent(starttimeh) + '&starttimem=' + encodeURIComponent(starttimem) +
   '&date=' + encodeURIComponent(eventdate) + '&userid=' + encodeURIComponent(providerid) + '&catid=' + encodeURIComponent(catid)<?php 
    if(isset($_SESSION['pid']))
        {
            if($_SESSION['pid']>0)
                {
                    echo "+'&patientid=" . attr_url($_SESSION['pid']) . "'";
                }
        }
     ?>
    ,'_blank', 775, 500);
 }

 function oldEvt(eventdate, eventid, pccattype) {
  dlgopen('add_edit_event.php?date=' + encodeURIComponent(eventdate) + '&eid=' + encodeURIComponent(eventid) + '&prov=' + encodeURIComponent(pccattype), '_blank', 775, 500);
 }

 function oldGroupEvt(eventdate, eventid, pccattype){
  top.restoreSession();
  dlgopen('add_edit_event.php?group=true&date=' + encodeURIComponent(eventdate) + '&eid=' + encodeURIComponent(eventid) + '&prov=' + encodeURIComponent(pccattype), '_blank', 775, 500);
 }

 function goPid(pid) {
  top.restoreSession();
<?php 
         echo "  top.RTop.location = '../../patient_file/summary/demographics.php' " .
           "+ '?set_pid=' + encodeURIComponent(pid);\n";
 ?>
 }

 function goGid(gid) {
  top.restoreSession();
<?php 
        echo "  top.RTop.location = '" . $GLOBALS['rootdir'] . "/therapy_groups/index.php' " .
        "+ '?method=groupDetails&group_id=' + encodeURIComponent(gid);\n ";
 ?>
 }

 function GoToToday(theForm){
  var todays_date = new Date();
  var theMonth = todays_date.getMonth() + 1;
  theMonth = theMonth < 10 ? "0" + theMonth : theMonth;
  theForm.jumpdate.value = todays_date.getFullYear() + "-" + theMonth + "-" + todays_date.getDate();
  top.restoreSession();
  theForm.submit();
 }
 function ShowImage(src)
 {
     var img = document.getElementById('popupImage');
     var div = document.getElementById('popup');
     img.src = src;
     div.style.display = "block";
 }
 function HideImage()
 {
     document.getElementById('popup').style.display = "none";
 }
</script>

<?php 

 // this is my proposed setting in the globals config file so we don't
 // need to mess with altering the pn database AND the config file
 //pnModSetVar(__POSTCALENDAR__, 'pcFirstDayOfWeek', $GLOBALS['schedule_dow_start']);

 // build a day-of-week (DOW) list so we may properly build the calendars later in this code
 $DOWlist = array();
 $tmpDOW = pnModGetVar(__POSTCALENDAR__, 'pcFirstDayOfWeek');
 // bound check and auto-correction
 if ($tmpDOW <0 || $tmpDOW >6) {
    pnModSetVar(__POSTCALENDAR__, 'pcFirstDayOfWeek', '0');
    $tmpDOW = 0;
 }
 while (count($DOWlist) < 7) {
    array_push($DOWlist, $tmpDOW);
    $tmpDOW++;
    if ($tmpDOW > 6) $tmpDOW = 0;
 }

 // A_CATEGORY is an ordered array of associative-array categories.
 // Keys of interest are: id, name, color, desc, event_duration.
 //
 // echo "<!-- A_CATEGORY = "; print_r($this->_tpl_vars['A_CATEGORY']); echo " -->\n"; // debugging
 // echo "<!-- A_EVENTS = "; print_r($this->_tpl_vars['A_EVENTS']); echo " -->\n"; // debugging

 $A_CATEGORY  =& $this->_tpl_vars['A_CATEGORY'];

 $A_EVENTS  =& $this->_tpl_vars['A_EVENTS'];
 $providers =& $this->_tpl_vars['providers'];
 $times     =& $this->_tpl_vars['times'];
 $interval  =  $this->_tpl_vars['interval'];
 $viewtype  =  $this->_tpl_vars['VIEW_TYPE'];
 $PREV_WEEK_URL = $this->_tpl_vars['PREV_WEEK_URL'];
 $NEXT_WEEK_URL = $this->_tpl_vars['NEXT_WEEK_URL'];
 $PREV_DAY_URL  = $this->_tpl_vars['PREV_DAY_URL'];
 $NEXT_DAY_URL  = $this->_tpl_vars['NEXT_DAY_URL'];
 $PREV_MONTH_URL = $this->_tpl_vars['PREV_MONTH_URL'];
 $NEXT_MONTH_URL = $this->_tpl_vars['NEXT_MONTH_URL'];

 $Date =  postcalendar_getDate();
 if (!isset($y)) $y = substr($Date, 0, 4);
 if (!isset($m)) $m = substr($Date, 4, 2);
 if (!isset($d)) $d = substr($Date, 6, 2);

 // echo "<!-- There are " . count($A_EVENTS) . " A_EVENTS days -->\n";

 //==================================
 //FACILITY FILTERING (CHEMED)
 $facilities = getUserFacilities($_SESSION['authId']); // from users_facility
if ( $_SESSION['pc_facility'] ) {
    $provinfo = getProviderInfo('%', true, $_SESSION['pc_facility']);
 } else {
    $provinfo = getProviderInfo();
 }
 //EOS FACILITY FILTERING (CHEMED)
 //==================================

$chevron_icon_left = $_SESSION['language_direction'] == 'ltr' ? 'fa-chevron-circle-left' : 'fa-chevron-circle-right';
$chevron_icon_right = $_SESSION['language_direction'] == 'ltr' ? 'fa-chevron-circle-right' : 'fa-chevron-circle-left';

 ?>
<div id="topToolbarRight" class="bgcolor2">  <!-- this wraps some of the top toolbar items -->
<div id="functions">
<!-- stuff form element here to avoid the margin padding it introduces into the page in some browsers -->
<form name='theform' id='theform' action='index.php?module=PostCalendar&func=view&tplview=default&pc_category=&pc_topic=' method='post' onsubmit='return top.restoreSession()'>
<input type="hidden" name="jumpdate" id="jumpdate" value="">
<input type="hidden" name="viewtype" id="viewtype" value="<?php echo attr($viewtype); ?>">
<?php 
echo "   <a href='#' title='" . xla("New Appointment") . "' onclick='newEvt(1, 9, 00," . attr_js($Date) . ", 0, 0)' class='css_button css_button_icon'>
            <i class='fa fa-plus' aria-hidden='true'></i></a>\n";
echo "   <a href='#' title='" . xla("Search Appointment") . "'
            onclick='top.restoreSession();location=\"index.php?module=PostCalendar&func=search\"' class='css_button css_button_icon'>
            <i class='fa fa-search' aria-hidden='true'></i></a>\n";
 ?>
</div>


<div id="dateNAV"">
<a href='#' name='bnsubmit' value='<?php echo xla("Today") ?>' onClick='GoToToday(theform);'  class='css_button'/><span><?php  echo xlt("Today") ?></span></a>
<?php 
echo "   <a id='prevmonth' href='$PREV_MONTH_URL' onclick='top.restoreSession()' title='" . xla("Previous Month") . "'>
            <i class='fa " . attr($chevron_icon_left) . " chevron_color' aria-hidden='true'></i></a>\n";;
echo xlt(date('F', strtotime($Date))) . " " . text(date('Y', strtotime($Date)));
echo "   <a id='nextmonth' href='$NEXT_MONTH_URL' onclick='top.restoreSession()' title='" . xla("Next Month") . "'>
           <i class= 'fa " . attr($chevron_icon_right) . " chevron_color' aria-hidden='true'></i></a>\n";
 ?>
</div>

<div id="viewPicker">
<?php 
echo "   <a href='#' id='printview' title='" . xla("View Printable Version") . "' class='css_button css_button_icon'>
            <i class='fa fa-print' aria-hidden='true'></i></a>\n";
echo "   <a href='#' title='" . xla("Refresh") . "' onclick='javascript:refreshme()' class='css_button css_button_icon'>
            <i class='fa fa-refresh' aria-hidden='true'></i></a>\n";
echo "   <a href='#' type='button' id='dayview' title='" . xla('Day View') . "' class='css_button'/><span>" . xlt('Day') . "</span></a>\n";
echo "   <a href='#' type='button' id='weekview' title='" . xla('Week View') . "' class='css_button'/><span>" . xlt('Week') . "</span></a>\n";
echo "   <a href='#' type='button' id='monthview' title='" . xla('Month View') . "' class='css_button'/><span>" . xlt('Month') . "</span></a>\n";
 ?>
</div>
</div> <!-- end topToolbarRight -->
<div id="bottom">
<div id="bottomLeft">
<div id="datePicker">
<?php 
    // caldate depends on what the user has clicked
    $caldate = strtotime($Date);
    $cMonth = date("m", $caldate);
    $cYear = date("Y", $caldate);
    $cDay = date("d", $caldate);

    include_once($GLOBALS['fileroot'].'/interface/main/calendar/modules/PostCalendar/pntemplates/default/views/monthSelector.php');
 ?>

<table border="0" cellpadding="0" cellspacing="0">
<tr>
<?php 

// compute the previous month date
// stay on the same day if possible
$pDay = $cDay;
$pMonth = $cMonth - 1;
$pYear = $cYear;
if ($pMonth < 1) { $pMonth = 12; $pYear = $cYear - 1; }
while (! checkdate($pMonth, $pDay, $pYear)) { $pDay = $pDay - 1; }
$prevMonth = sprintf("%d%02d%02d",$pYear,$pMonth,$pDay);

// compute the next month
// stay on the same day if possible
$nDay = $cDay;
$nMonth = $cMonth + 1;
$nYear = $cYear;
if ($nMonth > 12) { $nMonth = 1; $nYear = $cYear + 1; }
while (! checkdate($nMonth, $nDay, $nYear)) { $nDay = $nDay - 1; }
$nextMonth = sprintf("%d%02d%02d",$nYear,$nMonth,$nDay);
 ?>
<td class="tdDOW-small tdDatePicker tdNav" id="<?php echo attr($prevMonth) ?>" title="<?php echo xla(date("F", strtotime($prevMonth))); ?>">&lt;</td>
<td colspan="5" class="tdMonthName-small">
<?php 
echo xl(date('F', $caldate));
 ?>
</td>
<td class="tdDOW-small tdDatePicker tdNav" id="<?php echo attr($nextMonth) ?>" title="<?php echo xla(date("F", strtotime($nextMonth))); ?>">&gt;</td>
<tr>
<?php 
foreach ($DOWlist as $dow) {
    echo "<td class='tdDOW-small'>" . text($this->_tpl_vars['A_SHORT_DAY_NAMES'][$dow]) . "</td>";
}
 ?>
</tr>
<?php 
$atmp = array_keys($A_EVENTS);

foreach ($atmp as $currdate) {
    $currdate = strtotime($currdate);
    if (date('w', $currdate) == $DOWlist[0]) {
        // start of week row
        $tr = "<tr>";
        echo $tr;
    }

    // set the TD class
    $tdClass = "tdMonthDay-small";
    if (date('m', $currdate) != $month) {
        $tdClass = "tdOtherMonthDay-small";
    }
    if (is_weekend_day(date('w', $currdate))) {
        $tdClass = "tdWeekend-small";
    }
    if (is_holiday(date('Y-m-d', $currdate))) {
      $tdClass = "tdHoliday-small";
    }

    if (date('Ymd',$currdate) == $Date) {
        // $Date is defined near the top of this file
        // and is equal to whatever date the user has clicked
        $tdClass .= " currentDate";
    }

    // add a class so that jQuery can grab these days for the 'click' event
    $tdClass .= " tdDatePicker";

    // output the TD
    $td = "<td ";
    $td .= "class=\"" . attr($tdClass) . "\" ";
    //$td .= "id=\"" . attr(date("Ymd", $currdate)) . "\" ";
    $td .= "id=\"" . attr(date("Ymd", $currdate)) . "\" ";
    $td .= "title=\"" . xla('Go to') . " " . attr(date('F', $currdate)) . "\" ";
    $td .= "> " . text(date('d', $currdate)) . "</td>\n";
    echo $td;

    // end of week row
    if (date('w', $currdate) == $DOWlist[6]) echo "</tr>\n";
}
 ?>
</table>
</div>
<div id="bigCalHeader">
</div>

<div id="providerPicker">
<?php  echo xlt('Providers');  ?>
<div>
<?php 
// ==============================
// FACILITY FILTERING (lemonsoftware)
// $facilities = getFacilities();
if ($_SESSION['authorizeduser'] == 1) {
  $facilities = getFacilities();
} else {
  $facilities = getUserFacilities($_SESSION['authId']); // from users_facility
  if (count($facilities) == 1)
    $_SESSION['pc_facility'] = key($facilities);
}
if (count($facilities) > 1) {
    echo "   <select name='pc_facility' id='pc_facility'  class='view1' >\n";
    if ( !$_SESSION['pc_facility'] ) $selected = "selected='selected'";
    // echo "    <option value='0' $selected>"  . xlt('All Facilities'). "</option>\n";
    if (!$GLOBALS['restrict_user_facility']) echo "    <option value='0' $selected>" . xlt('All Facilities') . "</option>\n";

    foreach ($facilities as $fa) {
        $selected = ( $_SESSION['pc_facility'] == $fa['id']) ? "selected='selected'" : "" ;
        echo "    <option style=background-color:".htmlspecialchars($fa['color'],ENT_QUOTES)." value='" . attr($fa['id']). "' $selected>"  . text($fa['name']). "</option>\n";
    }
    echo "   </select>\n";
}
// EOS FF
// ==============================
 echo "</div>";
 echo "   <select multiple size='5' name='pc_username[]' id='pc_username' class='view2'>\n";
 echo "    <option value='__PC_ALL__'>" . xlt("All Users") . "</option>\n";
 foreach ($provinfo as $doc) {
  $username = $doc['username'];
  echo "    <option value='" . attr($username) . "'";
  foreach ($providers as $provider)
   if ($provider['username'] == $username) echo " selected";
  echo ">" . text($doc['lname']) . ", " . text($doc['fname']) . "</option>\n";
 }
 echo "   </select>\n";

 ?>
</div>
<?php 
if($_SESSION['pc_facility'] == 0){
 ?>
<div id="facilityColor">
 <table>
<?php 
foreach ($facilities as $f){
echo "   <tr><td><div class='view1' style=background-color:".$f['color'].";font-weight:bold>" . text($f['name']) . "</div></td></tr>";
}
 ?>
 </table>
</div>
<?php 
}
 ?>
</form>

<?php $this->assign('dayname', ((is_array($_tmp=$this->_tpl_vars['DATE'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%w") : smarty_modifier_date_format($_tmp, "%w"))); ?>
<?php $this->assign('day', ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['DATE'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d") : smarty_modifier_date_format($_tmp, "%d")))) ? $this->_run_mod_handler('string_format', true, $_tmp, "%1d") : smarty_modifier_string_format($_tmp, "%1d"))); ?>
<?php $this->assign('month', ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['DATE'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%m") : smarty_modifier_date_format($_tmp, "%m")))) ? $this->_run_mod_handler('string_format', true, $_tmp, "%1d") : smarty_modifier_string_format($_tmp, "%1d"))); ?>
<?php $this->assign('year', ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['DATE'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%Y") : smarty_modifier_date_format($_tmp, "%Y")))) ? $this->_run_mod_handler('string_format', true, $_tmp, "%4d") : smarty_modifier_string_format($_tmp, "%4d"))); ?>
    <div id="popup" class="pop-up">
        <img id="popupImage" alt=" " />
    </div>
</div> <!-- end bottomLeft -->

<div id="bigCal">
<?php 
/* used in debugging
foreach ($A_EVENTS as $date => $events) {
    echo $date." = ";
    foreach ($events as $oneE) {
        print_r($oneE);
        echo "<br><br>";
    }
    echo "<hr width=100%>";
}
*/


// This loops once for each provider to be displayed.
//
foreach ($providers as $provider) {
    $providerid = $provider['id'];

    echo "<table>\n";
    echo " <tr>\n";
    echo "  <td colspan='7' class='providerheader'>";
    echo text($provider['fname']) . " " . text($provider['lname']);
    echo "</td>\n";
    echo " </tr>\n";

    // output date headers
    echo " <tr>\n";
    $defaultDate = ""; // used when creating link for a 'new' event
    $in_cat_id = 0; // used when creating link for a 'new' event
    $dowCount = 0;
    foreach ($A_EVENTS as $date => $events) {
        if ($defaultDate == "") $defaultDate = date("Ymd", strtotime($date));
        echo "<td align='center' class='month_dateheader'>";
        echo xlt(date("D", strtotime($date)));
        echo "</td>";
        if ($dowCount++ == 6) { break; }
    }
    echo " </tr>\n";

    // For each day...
    // output a TD with an inner containing DIV positioned 'relative'

    foreach ($A_EVENTS as $date => $events) {
        $eventdate = substr($date, 0, 4) . substr($date, 5, 2) . substr($date, 8, 2);

        $gotoURL = pnModURL(__POSTCALENDAR__,'user','view',
                        array('tplview'=>$template_view,
                        'viewtype'=>'day',
                        'Date'=> date("Ymd", strtotime($date)),
                        'pc_username'=>$pc_username,
                        'pc_category'=>$category,
                        'pc_topic'=>$topic));

        if (date("w", strtotime($date)) == $DOWlist[0]) { echo "<tr>"; }
        $classForWeekend = is_weekend_day(date('w', strtotime($date))) ? 'weekend-day' : 'work-day';
        echo "<td class='schedule " . attr($classForWeekend) . "'>";

        echo "<div class='calendar_day'>\n";

        echo "<div class='month_daylink'>";
        echo "<a href='".$gotoURL."' alt='Go to " . attr(date("d M Y",strtotime($date))) . "' title='" . xla('Go to') . " " . attr(date("d M Y", strtotime($date))) . "'>";
        echo text(date("d", strtotime($date))) . "</a></div>";

        if (count($events) == 0) { echo "&nbsp;"; }
//=======================================================================================
//=======================================================================================
        foreach ($events as $event) {
            // skip events for other providers
            // yeah, we've got that sort of overhead here... it ain't perfect
            // $event['aid']!=0 :With the holidays we included clinic events, they have provider id =0
            // we dont want to exclude those events from being displayed
            if ($providerid != $event['aid']  && $event['aid']!=0) { continue; }

            // Omit IN and OUT events to reduce clutter in this month view
            if (($event['catid'] == 2) || ($event['catid'] == 3)) { continue; }

            // specially handle all-day events
            if ($event['alldayevent'] == 1) {
                $tmpTime = $times[0];
                if (strlen($tmpTime['hour']) < 2) { $tmpTime['hour'] = "0".$tmpTime['hour']; }
                if (strlen($tmpTime['minute']) < 2) { $tmpTime['minute'] = "0".$tmpTime['minute']; }
                $event['startTime'] = $tmpTime['hour'].":".$tmpTime['minute'].":00";
                $event['duration'] = ($calEndMin - $calStartMin) * 60;  // measured in seconds
            }

            // figure the start time and minutes (from midnight)
            $starth = substr($event['startTime'], 0, 2);
            $startm = substr($event['startTime'], 3, 2);
            $eStartMin = $starth * 60 + $startm;
            $startDateTime = strtotime($date." ".$event['startTime']);

            // determine the class for the event DIV based on the event category
            $evtClass = "event_appointment";
            switch ($event['catid']) {
                case 1:  // NO-SHOW appt
                    $evtClass = "event_noshow";
                    break;
                case 2:  // IN office
                    $evtClass = "event_in";
                    break;
                case 3:  // OUT of office
                    $evtClass = "event_out";
                    break;
                case 4:  // VACATION
                    $evtClass = "event_reserved";
                    break;
                case 6:  // HOLIDAY
                    $evtClass = "event_holiday hiddenevent";
                    break;
                case 8:  // LUNCH
                    $evtClass = "event_reserved";
                    break;
                case 11: // RESERVED
                    $evtClass = "event_reserved";
                    break;
                default: // some appointment
                    $evtClass = "event_appointment";
                    break;
            }

            // now, output the event DIV

            $eventid = $event['eid'];
            $eventtype = sqlQuery("SELECT pc_cattype FROM openemr_postcalendar_categories as oc LEFT OUTER JOIN openemr_postcalendar_events as oe ON oe.pc_catid=oc.pc_catid WHERE oe.pc_eid=?", [$eventid]);
            $pccattype = '';
            if($eventtype['pc_cattype']==1)
            $pccattype = 'true';
            $patientid = $event['pid'];
            $commapos = strpos($event['patient_name'], ",");
            $lname = substr($event['patient_name'], 0, $commapos);
	        $fname = substr($event['patient_name'], $commapos + 2);
            $patient_dob = $event['patient_dob'];
            $patient_age = $event['patient_age'];
            $catid = $event['catid'];
            $comment = $event['hometext'];
            $catname = $event['catname'];
            $title = "Age $patient_age ($patient_dob)";

            //Variables for therapy groups
            $groupid = $event['gid'];
            if($groupid) $patientid = '';
            $groupname = $event['group_name'];
            $grouptype = $event['group_type'];
            $groupstatus = $event['group_status'];
            $groupcounselors = '';
            foreach($event['group_counselors'] as $counselor){
                $groupcounselors .= getUserNameById($counselor) . " \n ";
            }

            // format the time specially
            $displayTime = date("g", $startDateTime);
            if (date("i", $startDateTime) == "00") {
                $displayTime .= (date("a", $startDateTime));
            }
            else {
                $displayTime .= (date(":ia", $startDateTime));
            }

            if ($comment && $GLOBALS['calendar_appt_style'] < 4) $title .= " " . $comment;

            // the divTitle is what appears when the user hovers the mouse over the DIV
                $divTitle = dateformat (strtotime($date),true);
            $result = sqlStatement("SELECT name,id,color FROM facility WHERE id=(SELECT pc_facility FROM openemr_postcalendar_events WHERE pc_eid=?)", [$eventid]);
            $row = sqlFetchArray($result);
            $color=$event["catcolor"];
            if($GLOBALS['event_color']==2)
            $color=$row['color'];
            $divTitle .= "\n" . $row['name'];
            $content = "";
            if ($catid == 4 || $catid == 8 || $catid == 11) {
                if ($catid ==  4) $catname = xl("VACATION");
                else if ($catid ==  8) $catname = xl("LUNCH");
                else if ($catid == 11) $catname = xl("RESERVED");

                $atitle = $catname;
                if ($comment) $atitle .= " $comment";
                $divTitle .= "\n[".$atitle ."]";
                $content .= text($displayTime);
                $content .= "&nbsp;" . text($catname);
            }
            else {
                // some sort of patient appointment
                if($groupid){
                    $divTitle .= "\n" . xl('Counselors') . ": \n" . $groupcounselors . " \n";
                    $divTitle .= "\r\n[" . $catname . ' ' . $comment . "]" . $groupname;
                }
                else
                    $divTitle .= "\r\n[" . $catname . ' ' . $comment . "]" . $fname . " " . $lname;
                $content .= create_event_time_anchor($displayTime);
                if ($patientid) {
                    // include patient name and link to their details
                    $link_title = $fname . " " . $lname . " \n";
                    $link_title .= xl('Age') . ": " . $patient_age . "\n" . xl('DOB') . ": " . $patient_dob . $comment . "\n";
                    $link_title .= "(" . xl('Click to view') . ")";
                    $content .= "<a href='javascript:goPid(" . attr_js($patientid) . ")' title='" . attr($link_title) . "'>";
                    $content .= "<img src='{$this->_tpl_vars['TPL_IMAGE_PATH']}/user-green.gif'
                                      onmouseover=\"javascript:ShowImage(" . attr_js($GLOBALS['webroot']."/controller.php?document&retrieve&patient_id=".$patientid."&document_id=-1&as_file=false&original_file=true&disable_exit=false&show_original=true&context=patient_picture") . ");\"
                                      onmouseout=\"javascript:HideImage();\"
                                      border='0' title='" . attr($link_title) . "' alt='View Patient' />";
                    if ($catid == 1) $content .= "<strike>";
                    $content .= text($lname);
                    if ($GLOBALS['calendar_appt_style'] != 1) {
                        $content .= "," . text($fname);
                        if ($event['title'] && $GLOBALS['calendar_appt_style'] >= 3) {
                            $content .= "(" . text($event['title']);
                            if ($event['hometext'] && $GLOBALS['calendar_appt_style'] >= 4)
                            $content .= ": <font color='green'>" . text(trim($event['hometext'])) . "</font>";
                            $content .= ")";
                        }
                    }
                    if ($catid == 1) $content .= "</strike>";
                    $content .= "</a>";
                }
                elseif($groupid){
                    $divTitle .= "\n" . getTypeName($grouptype) . "\n";
                    $link_title = '';
                    $link_title .= $divTitle ."\n";
                    $link_title .= "(" . xl('Click to view') . ")";
                    $content .= "<a href='javascript:goGid(" . attr_js($groupid) . ")' title='" . attr($link_title) . "'>";
                    $content .= "<img src='{$this->_tpl_vars['TPL_IMAGE_PATH']}/user-blue.gif' border='0' title='" . attr($link_title) . "' alt='View Patient' />";
                    if ($catid == 1) $content .= "<strike>";
                    $content .= text($groupname);
                    if ($GLOBALS['calendar_appt_style'] != 1) {
                        if ($event['title'] && $GLOBALS['calendar_appt_style'] >= 3) {
                            $content .= "(" . text($event['title']);
                            if ($event['hometext'] && $GLOBALS['calendar_appt_style'] >= 4)
                            $content .= ": <font color='green'>" . text(trim($event['hometext'])) . "</font>";
                            $content .= ")";
                        }
                    }
                    if ($catid == 1) $content .= "</strike>";
                    $content .= "</a>";

                    //Add class to wrapping div so EditEvent js function can differentiate between click on group and patient
                    $evtClass .= ' groups ';

                }

                else {
                //Category Clinic closaed or holiday take the event title
                     if ( $catid == 6 || $catid == 7){
                        $content  = xlt($event['title']);
                    }else{
                    // no patient id, just output the category name
                        $content .= text(xl_appt_category($catname));
                     }
                    }
            }
            if ( $catid != 6){
                     $divTitle .= "\n(" . xl('double click to edit') . ")";
                }
            if($_SESSION['pc_facility'] == 0){
                    echo "<div class='" . attr($evtClass) . " month_event' style='background-color:".$color.
                            "' title='" . attr($divTitle) . "'".
                            " id='" . attr($eventdate) . "-" . attr($eventid) . "-" . attr($pccattype) . "'".
                            ">";
                    echo $content;
                    echo "</div>\n";
            }
            elseif($_SESSION['pc_facility'] == $row['id']){
                    echo "<div class='" . attr($evtClass) . " month_event' style='background-color:".$event["catcolor"].
                        "' title='" . attr($divTitle) . "'".
                        " id='" . attr($eventdate) . "-" . attr($eventid) . "-" . attr($pccattype) . "'".
                        ">";
                    echo $content;
                    echo "</div>\n";
            }
            else{
                    echo "<div class='" . attr($evtClass) . " month_event' style='background-color:#DDDDDD".
                        "' title='" . attr($divTitle) . "'".
                        " id='" . attr($eventdate) . "-" . attr($eventid) . "-" . attr($pccattype) . "'".
                        ">";
                    echo "<span style='color:red;text-align:center;'>" . text($displayTime) . " " . text($row['name'])."</span>";
                    echo "</div>\n";
            }
        } // end EVENT loop
//=======================================================================================
//=======================================================================================
        echo "</div>";
        echo "</td>";
        if (date("w", strtotime($date)) == $DOWlist[6]) { echo "</tr>"; }
    } // end date

    echo "</table>\n";
    echo "<P>";
} // end provider

 ?>
</div>  <!-- end bigCal DIV -->
</div> <!-- end bottom DIV -->
</body>

<script language='JavaScript'>

    $(function (){
        $("#pc_username").change(function() { ChangeProviders(this); });
        $("#pc_facility").change(function() { ChangeProviders(this); });
        $("#dayview").click(function() { ChangeView(this); });
        $("#weekview").click(function() { ChangeView(this); });
        //$("#monthview").click(function() { ChangeView(this); });
        //$("#yearview").click(function() { ChangeView(this); });
        $(".tdDatePicker").click(function() { ChangeDate(this); });
        $("#datePicker .tdNav").mouseover(function() { $(this).toggleClass("tdDatePickerHighlight"); });
        $("#datePicker .tdNav").mouseout(function() { $(this).toggleClass("tdDatePickerHighlight"); });
        $("#printview").click(function() { PrintView(this); });
        $(".month_event").dblclick(function() { EditEvent(this); });
        $(".month_event").mouseover(function() { $(this).toggleClass("event_highlight"); });
        $(".month_event").mouseout(function() { $(this).toggleClass("event_highlight"); });

        $(".tdMonthName-small").click(function() {

            dpCal=$("#datePicker>table");
            mp = $("#monthPicker"); mp.width(dpCal.width()); mp.toggle();});

    });

    /* edit an existing event */
    var EditEvent = function(eObj) {
        // if have hiddenevent class do nothing
        if (eObj.classList.contains('hiddenevent'))
            return true;
        //alert ('editing '+eObj.id);
        // split the object ID into date and event ID
        objID = eObj.id;
        var parts = new Array();
        parts = objID.split("-");
        editing_group = $(eObj).hasClass("groups");
        if(editing_group){
            oldGroupEvt(parts[0], parts[1], parts[2]);
            return true;
        }
        // call the oldEvt function to bring up the event editor
        oldEvt(parts[0], parts[1], parts[2]);
        return true;
    }

    /* pop up a window to print the current view
     */
    var PrintView = function (eventObject) {
        printURL = "<?php echo pnModURL(__POSTCALENDAR__,'user','view',
                        array('tplview'=>$template_view,
                        'viewtype'=>$viewtype,
                        'Date'=> $Date,
                        'print'=> 1,
                        'pc_username'=>$pc_username,
                        'pc_category'=>$category,
                        'pc_topic'=>$topic)); ?>";
        window.open(printURL,'printwindow','width=740,height=480,toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,copyhistory=no,resizable=yes');
        return false;
    }

    /* change the current date based upon what the user clicked in
     * the datepicker DIV
     */
    var ChangeDate = function(eObj) {
        baseURL = "<?php echo pnModURL(__POSTCALENDAR__,'user','view',
                        array('tplview'=>$template_view,
                        'viewtype'=>$viewtype,
                        'Date'=> '~REPLACEME~',
                        'pc_username'=>$pc_username,
                        'pc_category'=>$category,
                        'pc_topic'=>$topic)); ?>";
        newURL = baseURL.replace(/~REPLACEME~/, eObj.id);
        document.location.href=newURL;
    }

    /* change the provider(s)
     */
    var ChangeProviders = function (eventObject) {
        $('#theform').submit();
    }

    /* change the calendar view
     */
    var ChangeView = function (eventObject) {
        if (eventObject.id == "dayview") {
            $("#viewtype").val('day');
        }
        else if (eventObject.id == "weekview") {
            $("#viewtype").val('week');
        }
        else if (eventObject.id == "monthview") {
            $("#viewtype").val('month');
        }
        else if (eventObject.id == "yearview") {
            $("#viewtype").val('year');
        }
        $('#theform').submit();
    }

</script>


</html>
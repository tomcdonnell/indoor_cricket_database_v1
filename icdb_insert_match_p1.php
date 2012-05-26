<?php
 session_start();
 error_reporting(E_ALL);

 // These variables should be read from database on team selection from main page.
 $_SESSION['teamName']     = 'Two Dogs';
 $_SESSION['databaseName'] = 'AustraliaVictoriaCroydon3136TwoDogs';


 // Initial (displayed) selections for HTML form selectors.
 // These will be removed from the selector lists
 // when the pull-down menus are selected (JavaScript).
 $_SESSION['selectOppTeamNameString'] = 'Select Opp. Team Name';
 $_SESSION['enterOppTeamNameString' ] = 'Enter Opp. Team Name';
 $_SESSION['selectPlayerNameString' ] = 'Select Player Name';
 $_SESSION['enterPlayerNameString'  ] = 'Enter Player Name';

 /*
  *
  */
 function readPlayerNames()
 {
    // build query
    $MySQLquery = 'select first_name, last_name from players order by first_name, last_name';

    // execute query
    $qResult = mysql_query($MySQLquery);

    // copy player names from result of query to $_SESSION[]
    $_SESSION['n_players'] = mysql_num_rows($qResult);
    for ($i = 0; $i < $_SESSION['n_players']; $i++)
    {
       // get row
       $dataRowArray = mysql_fetch_array($qResult, MYSQL_NUM);
       
       $_SESSION['firstNamesArray'][$i] = $dataRowArray[0];
       $_SESSION['lastNamesArray' ][$i] = $dataRowArray[1];
    }
 }

 /*
  *
  */
 function readOppTeamNames()
 {
    // build query
    $MySQLquery = 'select opp_team_name from opp_teams order by opp_team_name';

    // execute query
    $qResult = mysql_query($MySQLquery);

    // copy team names from result of query to $_SESSION[]
    $_SESSION['n_oppTeamNames'] = mysql_num_rows($qResult);
    for ($i = 0; $i < $_SESSION['n_oppTeamNames']; $i++)
    {
       // get row
       $dataRowArray = mysql_fetch_array($qResult, MYSQL_NUM);
       
       $_SESSION['oppTeamNamesArray'][$i] = $dataRowArray[0];
    }
 }

 // connect to MySQL
 mysql_connect('localhost','Tom','igaiasma'); // must create robot user for program eg ICDBbot

 // select database
 mysql_select_db($_SESSION['databaseName']);

 readPlayerNames();
 readOppTeamNames();
?>
<!DOCTYPE html
 PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
 <head>
  <link rel=StyleSheet href="style.css" type="text/css" />
  <title>Indoor Cricket Database (Insert Match Part 1)</title>
  <script type="text/javascript" src="icdb_insert_match_p1.js"></script>
 </head>
 <body onLoad="init()">
  <h2>Step 1: Opp. team name, date, time, & players list.</h2>
  <form method="POST" action="icdb_insert_match_p2.php" onsubmit="return validate()">
   <table>
    <thead>
     <tr><th colspan="5">Team '<?php echo $_SESSION['teamName']; ?>' Match Players List</th></tr>
    </thead>
    <tbody>
     <tr><td colspan="5"></td></tr>
     <tr><th rowspan="2" colspan="2">Date</th><th>DD</th><th>MM</th><th>YY</th></tr>
     <tr>
      <td>
<?php
 $date = getdate();
?>
       <select id="day" name="day">
<?php
 for ($i = 1; $i <= 31; $i++)
 {
?>
        <option<?php if ($i == $date['mday']) echo ' selected'; ?>><?php echo $i; ?></option>
<?php
 }
?>
       </select>
      </td>
      <td>
       <select id="month" name="month" onchange="updateDateSelectors()">
<?php
 for ($i = 1; $i <= 12; $i++)
 {
?>
        <option<?php if ($i == $date['mon']) echo ' selected'; ?>><?php echo $i; ?></option>
<?php
 }
?>
       </select>
      </td>
      <td>
       <select id="year" name="year" onchange="updateDateSelectors()">
<?php
 for ($i = 2000; $i <= 2020; $i++)
 {
?>
        <option<?php if ($i == $date['year']) echo ' selected'; ?>><?php echo $i; ?></option>
<?php
 }
?>
       </select>
      </td>
     </tr>
     <tr><th rowspan="2" colspan="2">Time</th><th>HH</th><th>MM</th><th>AM/PM</th></tr>
     <tr>
      <td>
       <select id="hour" name="hour">
<?php
 for ($i = 1; $i <= 12; $i++)
 {
?>
        <option><?php echo $i; ?></option>
<?php
 }
?>
       </select>
      </td>
      <td>
       <select id="minute" name="minute">
<?php
 for ($i = 0; $i <= 59; $i++)
 {
?>
        <option><?php if ($i < 10) echo '0'; echo $i; ?></option>
<?php
 }
?>
       </select>
      </td>
      <td>
       <select id="AM_PM" name="AM_PM">
        <option         >AM</option>
        <option selected>PM</option>
       </select>
      </td>
     </tr>
     <tr><td colspan="5"></td></tr>
     <tr>
      <th>Opposition Team Name</th>
      <td>
       <input type="checkbox" value="checked"
        id="OppTeamCheckbox" onClick="toggleOppTeamNameSelectorORtext()">New
      </td>
      <td colspan="3" id="oppTeamNameTD">
       <select id="oppTeamName" name="oppTeamName" onFocus="removeSelectOppTeamNameInstruction()">
<?php
 for ($i = 0; $i < $_SESSION['n_oppTeamNames']; $i++)
 {
?>
        <option><?php echo $_SESSION['oppTeamNamesArray'][$i]; ?></option>
<?php
 }
?>
        <option selected><?php echo $_SESSION['selectOppTeamNameString']; ?></option>
       </select>
      </td>
     </tr>
     <tr><td colspan="5"></td></tr>
<?php
 for ($i = 1; $i <= 8; $i++)
 {
?>
     <tr>
      <th>Player <?php echo $i; ?></th>
      <td>
       <input type="checkbox" onClick="togglePlayerNameSelectorORtext(<?php echo $i; ?>)">New
      </td>
      <td colspan="3" id="playerName<?php echo $i; ?>TD">
<?php
    if ($i > 6)
    {
?>
       *
<?php
    }
?>
       <select id="playerName<?php echo $i; ?>" name="playerName<?php echo $i; ?>"
        onFocus="removeSelectPlayerNameInstruction(<?php echo $i; ?>)"
        onChange="updateSelectPlayerNameOptions(<?php echo $i; ?>)">
<?php
    for ($j = 0; $j < $_SESSION['n_players']; $j++)
    {
?>
        <option><?php echo        $_SESSION['firstNamesArray'][$j]
                          , ' ' , $_SESSION['lastNamesArray' ][$j]; ?></option>
<?php
    }
?>
        <option selected><?php echo $_SESSION['selectPlayerNameString']; ?></option>
       </select>
<?php
    if ($i > 6)
    {
?>
       *
<?php
    }
?>
      </td>
     </tr>
<?php
 }
?>
     <tr><td colspan="5"></td></tr>
     <tr><th colspan="5"><input value="Continue" type="submit"></th></tr>
    </tbody>
    <tcaption>Fields marked with * are optional.</tcaption>
   </table>
  </form>
 </body>
</html>

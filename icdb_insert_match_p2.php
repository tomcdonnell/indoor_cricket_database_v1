<?php
 session_start();
 require 'icdb_functions.php';

 // Initial (displayed) selections for HTML form selectors.
 // These will be removed from the selector lists
 // when the pull-down menus are selected (JavaScript).
 $_SESSION['selectBatsmanNameString'] = 'Select Batsman Name';
 $_SESSION['selectBowlerNameString' ] = 'Select Bowler Name';

 /*
  * Print a date as text eg. "the 21st of June 2006".
  */
 function printDateString($day, $month, $year)
 {
    echo 'the ' , $_POST['day'];
    switch ($_POST['day'] % 10)
    {
     case 1:  echo 'st'; break;
     case 2:  echo 'nd'; break;
     case 3:  echo 'rd'; break;
     default: echo 'th'; break;
    }
    echo ' of ';
    switch ($_POST['month'])
    {
     case  1: echo 'January ';   break;
     case  2: echo 'February ';  break;
     case  3: echo 'March ';     break;
     case  4: echo 'April ';     break;
     case  5: echo 'May ';       break;
     case  6: echo 'June ';      break;
     case  7: echo 'July ';      break;
     case  8: echo 'August ';    break;
     case  9: echo 'September '; break;
     case 10: echo 'October ';   break;
     case 11: echo 'November ';  break;
     case 12: echo 'December ';  break;
     default: break;
    }
    echo $_POST['year'];
 }

 /*
  * Print a time as text eg. "7:30 pm".
  */
 function printTimeString($hour, $minute, $am_pm)
 {
    echo $hour , ':', $minute , ' ';

    switch ($am_pm)
    {
     case 'AM': echo 'am'; break;
     case 'PM': echo 'pm'; break;
     default: errMsg('AM_PM variable was other than AM or PM.\n');
    }
 }


 // Check that new match is not at same date & time as an existing match ///////////////////////////

 // connect to MySQL
 if (!mysql_connect('localhost', 'Tom', 'igaiasma'))
   MySQLerror('Could not connect to MySQL.', mysql_errno(), mysql_error());
 else
   debugMsg('Connection to MySQL OK.');

 // select database
 if (!mysql_select_db($_SESSION['databaseName']))
   MySQLerror('Could not select database.', mysql_errno(), mysql_error());
 else
   debugMsg('Selection of database OK.');

 // build query substrings
 if ($_POST['AM_PM'] == 'AM') $hour24 = $_POST['hour'];
 else                         $hour24 = $_POST['hour'] + 12;
 $timeString = "'{$hour24}:{$_POST['minute']}:00'";
 $dateString = "'{$_POST['year']}-{$_POST['month']}-{$_POST['day']}'";

 // build query
 $qTestForDuplicateMatch =  "select @matchID := match_id\n"
                          . "from matches\n"
                          . "where match_date = $dateString\n"
                          . "and   match_time = $timeString";
 // execute query
 $r = mysql_query($qTestForDuplicateMatch);

 // test result
 if (mysql_error() != '')
   MySQLerror('Could not test for duplicate match.'
              , mysql_errno(), mysql_error()       );
 else
 {
    switch (mysql_num_rows($r))
    {
     case 0: // Zero is the expected result.  Do nothing and continue.
       break;

     case 1: // One match record exists whose date and time are the same as the new match record.
?>
<!DOCTYPE html
 PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
 <head></head>
 <body>
  <p>
   The database for team '<?php echo $_SESSION['teamName']; ?>' already contains a match on
   <?php printDateString($_POST['day'], $_POST['month'], $_POST['year']); ?> at
   <?php printTimeString($_POST['hour'], $_POST['minute'], $_POST['AM_PM']); ?>.
   <br /><br />
   Here is a summary of that match:
<?php

?>
   <br /><br />
   Since team '<?php echo $_SESSION['teamName']; ?>' could not have played two different matches
   at the same time, the database will not allow the creation of a new match record whose date and
   time are the same as an existing match record.
   <br /><br />
   You can either modify the existing match record, or delete the existing match
   record (enabling you to create a new match record at that date and time).
   <br /><br />
   <input type="button" value="Modify Existing Match Record">
   <input type="button" value="Delete Existing Match Record">
  </p>
 </body>
</html>
<?php
       exit(0); // successful exit
       break;

     default:
       $errMsg =  'Expected 0 or 1 row in result from mysql query '
                . '(select match_id (2)), '
                . 'received ' . mysql_num_rows($qCreateMatchIDvar) . ' row(s).';
       error($errMsg);
       break;
    }
 }


 // Save $_POST[] variables to $_SESSION[]. ////////////////////////////////////////////////////////

 $_SESSION['oppTeamName'] = $_POST['oppTeamName'];
 $_SESSION['day'        ] = $_POST['day'        ];
 $_SESSION['month'      ] = $_POST['month'      ];
 $_SESSION['year'       ] = $_POST['year'       ];
 $_SESSION['hour'       ] = $_POST['hour'       ];
 $_SESSION['minute'     ] = $_POST['minute'     ];
 $_SESSION['AM_PM'      ] = $_POST['AM_PM'      ];

 $_SESSION['playerNamesArray']
   = array(1 => $_POST['playerName1'],
           2 => $_POST['playerName2'],
           3 => $_POST['playerName3'],
           4 => $_POST['playerName4'],
           5 => $_POST['playerName5'],
           6 => $_POST['playerName6'],
           7 => $_POST['playerName7'],
           8 => $_POST['playerName8'] );
?>
<!DOCTYPE html
 PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
 <head>
  <link rel=StyleSheet href="style.css" type="text/css" />
  <title>Indoor Cricket Database (Insert Match Part 2)</title>
  <script type="text/javascript" src="icdb_insert_match_p2.js"></script>
 </head>
 <body onLoad="init()">
  <h2>Step 2: Scores.</h2>
  <form method="POST" action="icdb_insert_match_p3.php" onsubmit="return validate()">
   <table>
    <thead>
     <tr>
      <th colspan="10">
<?php
 $indent = '       ';

 echo  $indent , "Team '{$_SESSION['teamName']}' Match Scores Sheet<br />\n"
     , $indent , "(Vs. '{$_SESSION['oppTeamName']}' \n"
     , $indent , "{$_SESSION['day' ]}/{$_SESSION['month' ]}/{$_SESSION['year' ]} "
               , "{$_SESSION['hour']}:{$_SESSION['minute'] }{$_SESSION['AM_PM']})\n";
?>
      </th>
     </tr>
    </thead>
    <tbody>
     <tr><td colspan="10"></td>
     <tr>
      <th colspan="4">Batting Scores</th><td rowspan="21"></td>
      <th colspan="5">Bowling Scores</th>
     </tr>
      <th colspan="3">Batted</th>
      <td>
       <select id="inningsOrderBatt" name="inningsOrderBatt" 
        onChange="updateInningsOrderSelectors('true')">
        <option selected>1st</option>
        <option         >2nd</option>
       </select>
      </td>
      <th colspan="4">Bowled</th>
      <td>
       <select id="inningsOrderBowl" name="inningsOrderBowl"
        onchange="updateInningsOrderSelectors('false')">
        <option         >1st</option>
        <option selected>2nd</option>
       </select>
      </td>
     <tr>
      <th>Batting<br />Position</th>
      <th>Name                 </th>
      <th>Runs   <br />Scored  </th>
      <th>P'ship               </th>
      <th>Over   <br />Number  </th>
      <th>Name                 </th>
      <th>Wickets<br />Taken   </th>
      <th>Runs   <br />Conceded</th>
      <th>Opp.   <br />P'ship  </tr>
     </tr>
<?php
 $pos = 0; // batting position
 for ($i = 1; $i <= 16; $i++)
 {
?>
     <tr>
<?php
    if ($i % 2 === 1)
    {
       // $i is odd
       $pos++;
?>
      <th rowspan="2"><?php echo $pos; ?></th>
      <td rowspan="2">
       <select id="batsmanName<?php echo $pos; ?>" name="batsmanName<?php echo $pos; ?>"
        onFocus="removeSelectBatsmanNameInstruction(<?php echo $pos; ?>)"
        onChange="updateSelectBatsmanNameOptions(<?php echo $pos; ?>)">
<?php
       $selected = false;
       for ($j = 1; $j <= 8; $j++)
       {
          if (   $_SESSION['playerNamesArray'][$j] != ''
              && $_SESSION['playerNamesArray'][$j] != $_SESSION['enterPlayerNameString' ]
              && $_SESSION['playerNamesArray'][$j] != $_SESSION['selectPlayerNameString'])
          {
?>
        <option<?php if ($pos == $j) {echo ' selected'; $selected = true;} ?>>
         <?php echo $_SESSION['playerNamesArray'][$j] , "\n"; ?>
        </option>
<?php
          }
       }
       if ($selected === false)
       {
          $indent = '        ';
          echo $indent , "<option selected>" , $_SESSION['selectBatsmanNameString'] , "</option>\n";
       }
?>
       </select>
      </td>
      <td rowspan="2">
       <input type="text" size="2" maxLength="3"
        id="runsScored<?php echo $pos; ?>" name="runsScored<?php echo $pos; ?>"
        onKeyUp="updateTeamScoreAndTeamPship(<?php echo ceil($i / 4); ?>)">
      </td>
<?php
    }

    if ($i % 4 === 1)
    {
?>
      <th rowspan="4" id="teamPship<?php echo ceil($i / 4); ?>TH">0</th>
<?php
    }
?>
      <th><?php echo $i; ?></th>
      <td>
       <select id="bowlerName<?php echo $i; ?>" name="bowlerName<?php echo $i; ?>"
        onFocus="removeSelectBowlerNameInstruction(<?php echo $i; ?>)">
<?php
    for ($j = 1; $j <= 8; $j++)
    {
       if (   $_SESSION['playerNamesArray'][$j] != ''
           && $_SESSION['playerNamesArray'][$j] != $_SESSION['enterPlayerNameString']
           && $_SESSION['playerNamesArray'][$j] != $_SESSION['selectPlayerNameString'])
       {
?>
        <option>
         <?php echo $_SESSION['playerNamesArray'][$j] , "\n"; ?>
        </option>
<?php
       }
    }
?>
        <option selected><?php echo $_SESSION['selectBowlerNameString']; ?></option>
       </select>
      </td>
      <td>
       <input type="text" size="2" maxLength="2" value="0"
        id="wicketsTaken<?php echo $i; ?>" name="wicketsTaken<?php echo $i; ?>"
        onFocus="clearWicketsTaken(<?php echo $i; ?>)">
      </td>
      <td>
       <input type="text" size="2" maxLength="3"
        id="runsConceded<?php echo $i; ?>" name="runsConceded<?php echo $i; ?>"
        onKeyUp="updateOppTeamScoreAndOppTeamPship(<?php echo ceil($i / 4); ?>)">
      </td>
<?php
    if ($i % 4 === 1)
    {
?>
      <th rowspan="4" id="oppTeamPship<?php echo ceil($i / 4); ?>TH">0</th>
<?php
    }
?>
     </tr>
<?php
 }
?>
     <tr>
      <th colspan="2">Penalty Runs<br />Against '<?php echo $_SESSION['teamName']; ?>'</th>
      <td colspan="2">(
       <input type="text" size="2" maxLength="3" value="0"
        id="teamPenaltyRuns" name="teamPenaltyRuns"
        onFocus="clearTeamPenaltyRuns()" onKeyUp="updateTeamScore()"> )
      </td>
      <th colspan="3">Penalty Runs<br />Against '<?php echo $_SESSION['oppTeamName']; ?>'</th>
      <td colspan="2">(
       <input type="text" size="2" maxLength="3" value="0"
        id="oppTeamPenaltyRuns" name="oppTeamPenaltyRuns"
        onFocus="clearOppTeamPenaltyRuns()" onKeyUp="updateOppTeamScore()"> )
      </td>
     </tr>
     <tr>
      <th colspan="2">'<?php echo $_SESSION['teamName']; ?>'<br />Total Score</th>
      <th colspan="2" id="teamScoreTH">0</th>
      <th colspan="3">'<?php echo $_SESSION['oppTeamName']; ?>'<br />Total Score</th>
      <th colspan="2" id="oppTeamScoreTH">0</th>
     </tr>
     <tr><td colspan="10"></td></tr>
     <tr><th colspan="10"><input value="Continue" type="submit"></th></tr>
    </tbody>
   </table>
  </form>
 </body>
</html>

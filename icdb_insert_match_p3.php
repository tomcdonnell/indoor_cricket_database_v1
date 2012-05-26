<?php
 session_start();
 require 'icdb_functions.php';

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

 // start transaction
 if (!mysql_query('start transaction'))
   MySQLerror('Could not start transaction.', mysql_errno(), mysql_error());
 else
   debugMsg('Transaction started OK.');


 // Extract firstNames and lastNames from $_POSTed playerNames. ////////////////////////////////////

 $batsmanNamesArray
   = array(1 => explode(" ", $_POST['batsmanName1']),
           2 => explode(" ", $_POST['batsmanName2']),
           3 => explode(" ", $_POST['batsmanName3']),
           4 => explode(" ", $_POST['batsmanName4']),
           5 => explode(" ", $_POST['batsmanName5']),
           6 => explode(" ", $_POST['batsmanName6']),
           7 => explode(" ", $_POST['batsmanName7']),
           8 => explode(" ", $_POST['batsmanName8']) );

 $bowlerNamesArray
   = array( 1 => explode(" ", $_POST['bowlerName1' ]),
            2 => explode(" ", $_POST['bowlerName2' ]),
            3 => explode(" ", $_POST['bowlerName3' ]),
            4 => explode(" ", $_POST['bowlerName4' ]),
            5 => explode(" ", $_POST['bowlerName5' ]),
            6 => explode(" ", $_POST['bowlerName6' ]),
            7 => explode(" ", $_POST['bowlerName7' ]),
            8 => explode(" ", $_POST['bowlerName8' ]),
            9 => explode(" ", $_POST['bowlerName9' ]),
           10 => explode(" ", $_POST['bowlerName10']),
           11 => explode(" ", $_POST['bowlerName11']),
           12 => explode(" ", $_POST['bowlerName12']),
           13 => explode(" ", $_POST['bowlerName13']),
           14 => explode(" ", $_POST['bowlerName14']),
           15 => explode(" ", $_POST['bowlerName15']),
           16 => explode(" ", $_POST['bowlerName16']) );

 $_SESSION['runsScoredArray']
   = array (1 => $_POST['runsScored1'],
            2 => $_POST['runsScored2'],
            3 => $_POST['runsScored3'],
            4 => $_POST['runsScored4'],
            5 => $_POST['runsScored5'],
            6 => $_POST['runsScored6'],
            7 => $_POST['runsScored7'],
            8 => $_POST['runsScored8'] );

 $_SESSION['wicketsTakenArray']
   = array ( 1 => $_POST['wicketsTaken1' ],
             2 => $_POST['wicketsTaken2' ],
             3 => $_POST['wicketsTaken3' ],
             4 => $_POST['wicketsTaken4' ],
             5 => $_POST['wicketsTaken5' ],
             6 => $_POST['wicketsTaken6' ],
             7 => $_POST['wicketsTaken7' ],
             8 => $_POST['wicketsTaken8' ],
             9 => $_POST['wicketsTaken9' ],
            10 => $_POST['wicketsTaken10'],
            11 => $_POST['wicketsTaken11'],
            12 => $_POST['wicketsTaken12'],
            13 => $_POST['wicketsTaken13'],
            14 => $_POST['wicketsTaken14'],
            15 => $_POST['wicketsTaken15'],
            16 => $_POST['wicketsTaken16'] );

 $_SESSION['runsConcededArray']
   = array ( 1 => $_POST['runsConceded1' ],
             2 => $_POST['runsConceded2' ],
             3 => $_POST['runsConceded3' ],
             4 => $_POST['runsConceded4' ],
             5 => $_POST['runsConceded5' ],
             6 => $_POST['runsConceded6' ],
             7 => $_POST['runsConceded7' ],
             8 => $_POST['runsConceded8' ],
             9 => $_POST['runsConceded9' ],
            10 => $_POST['runsConceded10'],
            11 => $_POST['runsConceded11'],
            12 => $_POST['runsConceded12'],
            13 => $_POST['runsConceded13'],
            14 => $_POST['runsConceded14'],
            15 => $_POST['runsConceded15'],
            16 => $_POST['runsConceded16'] );

 // Insert oppTeamName into database, //////////////////////////////////////////////////////////////
 // and create MySQL variable for oppTeam_ID. //////////////////////////////////////////////////////

 $qCreateOppTeamIDvar =  "select @oppTeamID := opp_team_id\n"
                       . "from opp_teams\n"
                       . "where opp_team_name = \"{$_SESSION['oppTeamName']}\"\n";
 // execute query
 $r = mysql_query($qCreateOppTeamIDvar);

 // test result
 if (mysql_error() != '')
   MySQLerror("Could not create opp. team name variable (1).", mysql_errno(), mysql_error());
 else
 {
    switch (mysql_num_rows($r))
    {
     case 1:
       debugMsg("Opp. team name already in database.");
       debugMsg("MySQL opp. team ID variable created.");
       break;

     case 0:
       debugMsg("Opp. team name not in database (must be inserted).");


       // Insert team name into database. ////////////////////////////////////////////////////

       // build query
       $q =  'insert into opp_teams values '
           .  "(null, \"{$_SESSION['oppTeamName']}\")";

       // execute query
       mysql_query($q);

       // test result
       if (mysql_error() != '')
         MySQLerror('Could not insert opp. team name.', mysql_errno(), mysql_error());
       else
         debugMsg("Opp. team name inserted into database.");


       // Create mysql variables for oppTeamID. //////////////////////////////////////////////

       // execute query (built earlier)
       $r = mysql_query($qCreateOppTeamIDvar);

       // test result of query
       if (mysql_error() != '')
         MySQLerror("Could not create opp team name variable (2).", mysql_errno(), mysql_error());
       else
       {
          switch (mysql_num_rows($r))
          {
           case 1:
             debugMsg("MySQL opp. team ID variable created.");
             break;

           default:
             $errMsg =  'Expected 1 row in result from mysql query '
                      . "(create oppTeamName variable (1), received "
                      . mysql_num_rows($r) . ' row(s).';
             error($errMsg);
             break;
          }
       }
       break;

     default:
       $errMsg =  'Expected 0 or 1 row in result from mysql query '
                . "(create oppTeamName variable (2), received "
                . mysql_num_rows($r) . ' row(s).';
       error($errMsg);
       break;
    }
 }


 // Insert match title into database, //////////////////////////////////////////////////////////////
 // and create MySQL variable for matchID. /////////////////////////////////////////////////////////

 // build query substrings
 if ($_SESSION['AM_PM'] == 'AM') $hour24 = $_SESSION['hour'];
 else                            $hour24 = $_SESSION['hour'] + 12;
 $timeString = "'{$hour24}:{$_SESSION['minute']}:00'";
 $dateString = "'{$_SESSION['year']}-{$_SESSION['month']}-{$_SESSION['day']}'";

 // build query
 $qCreateMatchIDvar =  "select @matchID := match_id\n"
                     . "from matches\n"
                     . "where opp_team_id = @oppTeamID \n"
                     . "and   match_date  = $dateString\n"
                     . "and   match_time  = $timeString";

 // execute query
 $r = mysql_query($qCreateMatchIDvar);

 // test result
 if (mysql_error() != '')
   MySQLerror('Could not select match_id (1).', mysql_errno(), mysql_error());
 else
 {
    switch (mysql_num_rows($r))
    {
     case 1:
       debugMsg("Match title already in database.");
       debugMsg("MySQL match ID variable created.");
       break;

     case 0:
       debugMsg('Match title not in database, so must be inserted.');

       // Insert match title into database. /////////////////////////////////////////////////////

       // build query substring
       if ($_POST['inningsOrderBatt'] == '1st') $teamBatted1stString = 'true' ;
       else                                     $teamBatted1stString = 'false';

       // build query
       $q =   'insert into matches values '
            . "(null, @oppTeamID, $dateString, $timeString, $teamBatted1stString)";

       // execute query
       mysql_query($q);

       // test result
       if (mysql_error() != '')
         MySQLerror('Could not insert match title.', mysql_errno(), mysql_error());
       else
         debugMsg('Match title inserted into database.');


       // Create MySQL variable for matchID. ////////////////////////////////////////////////////

       // execute query (built earlier)
       $r = mysql_query($qCreateMatchIDvar);

       // test result
       if (mysql_error() != '')
         MySQLerror('Could not select match_id (2).', mysql_errno(), mysql_error());
       else
       {
          switch (mysql_num_rows($r))
          {
           case 1:
             debugMsg('MySQL match ID variable created.');
             break;

           default:
             $errMsg =  'Expected 1 row in result from mysql query '
                      . '(select match_id (2)), '
                      . 'received ' . mysql_num_rows($qCreateMatchIDvar) . ' row(s).';
             error($errMsg);
             break;
          }
       }
       break;

     default:
       $errMsg =  'Expected 0 or 1 row in result from mysql query '
                . '(select match_id (1)), received ' . mysql_num_rows($r) . ' row(s).';
       error($errMsg);
       break;
    }
 }


 // Insert players, player-team connections, ///////////////////////////////////////////////////////
 // and batting scores into database, //////////////////////////////////////////////////////////////

 for ($i = 1; $i <= 8; $i++)
 {
    debugMsg("Player ($i) start."); // separator line in debug output for easier reading


    // Insert player into database. /////////////////////////////////////////////////////////////

    // build query
    $qCreatePlayerIDvar =  "select @playerID := player_id\n"
                         . "from players\n"
                         . "where first_name = \"{$batsmanNamesArray[$i][0]}\"\n";
    if (count($batsmanNamesArray[$i]) == 2)
      $qCreatePlayerIDvar .= "and    last_name = \"{$batsmanNamesArray[$i][1]}\"";
    else
      $qCreatePlayerIDvar .= "and    last_name = \"\"";

    // execute query
    $r = mysql_query($qCreatePlayerIDvar);

    // test result
    if (mysql_error() != '')
      MySQLerror('Could not create MySQL player ($i) ID variable (1).'
                 , mysql_errno(), mysql_error()                       );
    else
    {
       switch (mysql_num_rows($r))
       {
        case 1:
          debugMsg("Player ($i) already in database.");
          debugMsg("MySQL player ($i) ID variable created.");
          break;

        case 0:
          debugMsg("Player ($i) not in database (must be inserted).");

          // build query
          $q =   'insert into players values '
               . "(null, \"{$batsmanNamesArray[$i][0]}\", ";
          if (count($batsmanNamesArray[$i]) == 2)
            $q .= "\"{$batsmanNamesArray[$i][1]}\")";
          else
            $q .= "\"\")";

          // execute query
          mysql_query($q);

          // test result
          if (mysql_error() != '')
             MySQLerror('Could not insert player.', mysql_errno(), mysql_error());
          else
            debugMsg("Player ($i) inserted into database.");


          // Create MySQL playerID variable. ////////////////////////////////////////////////////

          // execute query (built earlier)
          $r = mysql_query($qCreatePlayerIDvar);

          // test result
          if (mysql_error() != '')
            MySQLerror('Could not create MySQL player ($i) ID variable (2).'
                       , mysql_errno(), mysql_error()                       );
          else
          {
             switch (mysql_num_rows($r))
             {
              case 1:
                debugMsg("MySQL player ($i) ID variable created.");
                break;

              default:
                $errMsg =  'Expected 1 row in result from mysql query '
                         . '(select player_id (2)), received ' . mysql_num_rows($r) . ' row(s).';
                error($errMsg);
                break;
             }
          }
          break;

        default:
          $errMsg =  'Expected 0 or 1 row in result from mysql query '
                   . '(select player_id (1)), received ' . mysql_num_rows($r) . ' row(s).';
          error($errMsg);
          break;
       }
    }


    // Insert batting innings into database. ////////////////////////////////////////////////////

    // build query (to test whether batting innings is already in database)
    $q =  "select * from innings     \n"
        . "where  match_id = @matchID\n"
        . "and batting_pos = $i"        ;

    // execute query
    $r = mysql_query($q);

    // test result
    if (mysql_error() != '')
      MySQLerror('Could not select * from innings.', mysql_errno(), mysql_error());
    else
    {
       switch (mysql_num_rows($r))
       {
        case 1:
          debugMsg("Batting innings ($i) already in database.");
          break;

        case 0:
          debugMsg("Batting innings ($i) not in database (must be inserted).");

          // build query
          $q =  'insert into innings values '
              . "(@matchID, @playerID, $i , {$_SESSION['runsScoredArray'][$i]})";

          // execute query
          mysql_query($q);

          // test result
          if (mysql_error() != '')
            MySQLerror('Could not insert into innings.', mysql_errno(), mysql_error());
          else
            debugMsg("Batting innings ($i) inserted into database.");
          break;

        default:
          $errMsg =  'Expected 0 or 1 row in result from mysql query '
                   . '(select * from innings ...), received ' . mysql_num_rows($r) . ' row(s).';
          error($errMsg);
       }
    }
 }


 // Insert overs into database. /////////////////////////////////////////////////////////////////

 for ($i = 1; $i <= 16; $i++)
 {
    debugMsg("Player ($i) start."); // separator line in debug output for easier reading

    // build query (player already in database (tested earlier), so create MySQL variable)
    $qCreatePlayerIDvar =  "select @playerID := player_id\n"
                         . "from players\n"
                         . "where first_name = \"{$bowlerNamesArray[$i][0]}\"\n";
    if (count($bowlerNamesArray[$i]) == 2)
      $qCreatePlayerIDvar .= "and    last_name = \"{$bowlerNamesArray[$i][1]}\"";
    else
      $qCreatePlayerIDvar .= "and    last_name = \"\"";

    // execute query
    $r = mysql_query($qCreatePlayerIDvar);

    // test result
    if (mysql_error() != '')
      MySQLerror('Could not create player ID ($i) variable (1).', mysql_errno(), mysql_error());
    else
    {
       switch (mysql_num_rows($r))
       {
        case 1:
          debugMsg("Player ID ($i) variable created.");
          break;

        default:
          $errMsg =  'Expected 1 row in result from mysql query (select player_id (1)), received '
                   . mysql_num_rows($r) . ' row(s).';
          error($errMsg);
          break;
       }

       // build query (to test whether over is already in database)
       $q =  "select * from overs       \n"
           . "where match_id = @matchID \n"
           . "and  player_id = @playerID\n"
           . "and    over_no = $i";

       // execute query
       $r = mysql_query($q);

       // test result of select query
       if (mysql_error() != '')
         MySQLerror('Could not select * from overs (where over_no = $i).',
                    mysql_errno(), mysql_error()                          );
       else
       {
          switch (mysql_num_rows($r))
          {
           case 1:
             debugMsg("Over ($i) already in database.");
             break;

           case 0:
             debugMsg("Over ($i) not in database (must be inserted).");

             // build query
             $q =  'insert into overs values '
                 . "(@matchID, @playerID, $i, "
                 .   "{$_SESSION['wicketsTakenArray'][$i]}, "
                 .   "{$_SESSION['runsConcededArray'][$i]}) ";

             // execute query
             mysql_query($q);

             // test result
             if (mysql_error() != '')
               MySQLerror('Could not insert into overs.', mysql_errno(), mysql_error());
             else
               debugMsg("Over ($i) inserted into database.");
             break;

           default:
             $errMsg =  'Expected 0 or 1 row in result from mysql query '
                      . '(select * from overs ...), received ' . mysql_num_rows($r) . ' row(s).';
             error($errMsg);
             break;
          }
       }
    }
 }

 // commit transaction
 if (!mysql_query('commit'))
 {
    MySQLerror('Could not commit transaction.', mysql_errno(), mysql_error());

    // rollback transaction
    if (!mysql_query('rollback'))
      MySQLerror('Could not rollback transaction.', mysql_errno(), mysql_error());
    else
      debugMsg('Transaction rolled back OK.');
 }
 else
   debugMsg('Transaction committed OK.');
?>
<!DOCTYPE html
 PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
 <head>
  <title>Indoor Cricket Database (Insert Match Part 3)</title>
 </head>
 <body>
 </body>
</html>

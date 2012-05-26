<?php
 session_start();
 error_reporting(E_ALL);

 function pageHeading()
 {
?>
<!doctype html public "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
 <head>
  <link rel=StyleSheet href="style.css" type="text/css">
  <title>Indoor Cricket Database</title>
 </head>
 <body>
<?php
 }
 function displayMySQLtable($MySQLquery, $heading, $colHeadingsArray, $indent)
 {
    $qResult = mysql_query($MySQLquery);

    $nCols = count($colHeadingsArray);

    echo $indent, "<table>\n";
    echo $indent, " <tr><th colspan=\"{$nCols}\">{$heading}</th></tr>\n";
    echo $indent, " <tr>\n";
    for ($i = 0; $i < $nCols; $i++)
      echo $indent, "  <th>{$colHeadingsArray[$i]}</th>\n";
    echo $indent, " </tr>\n";

    // display table
    $n_rows = mysql_num_rows($qResult);
    for ($i = 0; $i <= $n_rows; $i++)
    {
       // get row
       $dataRowArray = mysql_fetch_array($qResult, MYSQL_NUM);

       // display row
       echo $indent, " <tr>\n";
       for ($j = 0; $j < $nCols; $j++)
         echo $indent, "  <td>{$dataRowArray[$j]}</td>\n";
       echo $indent, " </tr>\n";
    }
 }

 pageHeading();

 mysql_connect('localhost','Tom','igaiasma');
 mysql_select_db('AustraliaVictoriaCroydon3136TwoDogs');
?>
  <form method = "post" action = "<?php echo $_SERVER['PHP_SELF']; ?>">
   Select the table you wish to view.<br>
   <table>
    <tr>
     <th>players  </th>
     <th>opp_teams</th>
     <th>matches  </th>
     <th>innings  </th>
     <th>overs    </th>
    </tr>
    <tr>
     <td><input type=radio name="tableName" value="players"  ></td>
     <td><input type=radio name="tableName" value="opp_teams"></td>
     <td><input type=radio name="tableName" value="matches"  ></td>
     <td><input type=radio name="tableName" value="innings"  ></td>
     <td><input type=radio name="tableName" value="overs"    ></td>
    </tr>
    <tr><td colspan="6"></td></tr>
    <tr>
     <th>              </th>
     <th>              </th>
     <th>matches_no_ids</th>
     <th>innings_no_ids</th>
     <th>overs_no_ids  </th>
    </tr>
    <tr>
     <td></td>
     <td></td>
     <td><input type=radio name="tableName" value="matches_no_ids"></td>
     <td><input type=radio name="tableName" value="innings_no_ids"></td>
     <td><input type=radio name="tableName" value="overs_no_ids"  ></td>
    </tr>
    <tr><td colspan="6"></td></tr>
    <tr><th colspan="6"><input type = "submit" value = "Submit"></th></tr>
   </table>
  </form>
 </body>
</html>
<?php
 if (isset($_POST['tableName'])) ///////////////////////////////////////////////////////////////////
 {
    $indent = '  ';

    switch ($_POST['tableName'])
    {
     case 'players':
       $MySQLquery       =  "select * from players\n"
                          . "order by player_id asc";
       $heading          = 'players';
       $colHeadingsArray = array(0 => 'player_id', 1 => 'first_name', 2 => 'last_name');
       displayMySQLtable($MySQLquery, $heading, $colHeadingsArray, $indent);
       break;

     case 'opp_teams':
       $MySQLquery       =  "select * from opp_teams\n"
                          . "order by opp_team_id asc";
       $heading          = 'opp_teams';
       $colHeadingsArray = array(0 => 'opp_team_id', 1 => 'opp_team_name');
       displayMySQLtable($MySQLquery, $heading, $colHeadingsArray, $indent);
       break;

     case 'matches':
       $MySQLquery       =  "select * from matches\n"
                          . "order by match_id asc";
       $heading          = 'matches';
       $colHeadingsArray = array(0 => 'match_id'  , 1 => 'opp_team_id',
                                 2 => 'match_date', 3 => 'match_time' , 4 => 'team_batted_1st');
       displayMySQLtable($MySQLquery, $heading, $colHeadingsArray, $indent);
       break;

     case 'innings':
       $MySQLquery       =  "select * from innings\n"
                          . "order by match_id asc, batting_pos asc";
       $heading          = 'innings';
       $colHeadingsArray = array(0 => 'match_id'   , 1 => 'player_id',
                                 2 => 'batting_pos', 3 => 'runs_scored');
       displayMySQLtable($MySQLquery, $heading, $colHeadingsArray, $indent);
       break;

     case 'overs':
       $MySQLquery       =  "select * from overs\n"
                          . "order by match_id asc, over_no asc";
       $heading          = 'overs';
       $colHeadingsArray = array(0 => 'match_id', 1 => 'player_id',
                                 2 => 'over_no' , 3 => 'wickets_taken', 4 => 'runs_conceded');
       displayMySQLtable($MySQLquery, $heading, $colHeadingsArray, $indent);
       break;

     case 'matches_no_ids':
       $MySQLquery       =  "select match_date, match_time,                   \n"
                          . "       opp_team_name, team_batted_1st            \n"
                          . "from matches, opp_teams                          \n"
                          . "where opp_teams.opp_team_id = matches.opp_team_id\n"
                          . "order by match_date desc, match_time desc";
       $heading          = "Matches";
       $colHeadingsArray = array(0 => 'Date'          , 1 => 'Time',
                                 2 => 'Opp. Team Name', 3 => 'Team Batted 1st');
       displayMySQLtable($MySQLquery, $heading, $colHeadingsArray, $indent);
       break;

     case 'innings_no_ids':
       $MySQLquery       =  "select first_name, last_name, match_date,         \n"
                          . "       opp_team_name, batting_pos, runs_scored    \n"
                          . "from   players, opp_teams, matches, innings       \n"
                          . "where  players.player_id     = innings.player_id  \n"
                          . "and    matches.match_id      = innings.match_id   \n"
                          . "and    opp_teams.opp_team_id = matches.opp_team_id\n"
                          . "order by runs_scored desc, match_date asc, batting_pos asc";
       $heading          = 'Innings';
       $colHeadingsArray = array(0 => 'First Name'      , 1 => 'Last Name'     ,
                                 2 => 'Match Date'      , 3 => 'Opp. Team Name',
                                 4 => 'Batting Position', 5 => 'Runs Scored'    );
       displayMySQLtable($MySQLquery, $heading, $colHeadingsArray, $indent);
       break;

     case 'overs_no_ids':
       $MySQLquery       =  "select first_name, last_name, match_date,         \n"
                          . "       opp_team_name, over_no, wickets_taken,     \n"
                          . "       runs_conceded                              \n"
                          . "from   players, opp_teams, matches, overs         \n"
                          . "where  players.player_id     = overs.player_id    \n"
                          . "and    matches.match_id      = overs.match_id     \n"
                          . "and    opp_teams.opp_team_id = matches.opp_team_id\n"
                          . "order by runs_conceded asc, match_date asc, over_no asc";
       $heading          = 'Overs';
       $colHeadingsArray = array(0 => 'First Name'    ,
                                 1 => 'Last Name'     , 2 => 'Date'         ,
                                 3 => 'Opp. Team Name', 4 => 'Over Number'  ,
                                 5 => 'Wickets Taken' , 6 => 'Runs Conceded' );
       displayMySQLtable($MySQLquery, $heading, $colHeadingsArray, $indent);
       break;
    }
?>
 </body>
</html>
<?php
 }
?>

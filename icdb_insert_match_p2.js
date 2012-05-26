/**************************************************************************************************\
*                                                                                                  *
* FILENAME: "icdb_insert_match_p2.js"                                                              *
*                                                                                                  *
* PURPOSE: Javascripts for web page "icdb_insert_match_p2.php".                                    *
*                                                                                                  *
* AUTHOR: Tom McDonnell 2006                                                                       *
*                                                                                                  *
\**************************************************************************************************/

// GLOBAL VARIABLES ////////////////////////////////////////////////////////////////////////////////

var onerror = handleErr; // set error handling function

var selectBatsmanNameString = 'Select Batsman Name'; // must match value in PHP file
var selectBowlerNameString  = 'Select Bowler Name';  // must match value in PHP file

var oldBatsmanNameSelectedIndicesArray = new Array(8);

var n_playersThisMatch; // 8 players in a full team, but a team can consist of as few as 6 players.

// FUNCTIONS ///////////////////////////////////////////////////////////////////////////////////////

/*
 *
 */
function handleErr(msg, url, l)
{
   var txt = "There was an error on this page.\n\n";
   txt += "Error: " + msg + "\n";
   txt += "URL: " + url + "\n";
   txt += "Line: " + l + "\n\n";
   txt += "Click OK to continue.\n\n";
   alert(txt);

   return true;
}

/*
 *
 */
function init()
{
   // count team players participating in this match
   n_playersThisMatch = document.getElementById("batsmanName1").length;

   for (var i = 1; i <= 8; ++i)
     updateSelectBatsmanNameOptions(i);
}

/*
 * NOTE: this function must depend on whether the team is short players.
 */
function updateSelectBatsmanNameOptions(battingPos)
{
   var i;     // counter
   var limit; // upper limit for 'for' loop

   var element = document.getElementById("batsmanName" + battingPos);

   if (   element.selectedIndex >= 0             // Required since after removal of instruction
       && element.selectedIndex < element.length // option, index will be out of range.
       && element.options[element.selectedIndex].text != selectBatsmanNameString)
   {
      var newSelectedIndex = element.selectedIndex;
      var oldSelectedIndex = oldBatsmanNameSelectedIndicesArray[battingPos - 1];

      if (n_playersThisMatch < 8 && battingPos > 6)
      {
         // The team is short one or two players, so one or two batsmen must bat twice.
         // In this situation, a selection in batting position 7 or 8 will only effect the options
         // of the other of the last two batting positions.
         // What to do depends on whether there are 6 or 7 players.

         switch (n_playersThisMatch)
         {
          case 6:
            // Enable other (batting position)'s select options
            // for (batsman at battingPos)'s old selected batsman.
            i = (battingPos === 8)? 7: 8;
            if (oldSelectedIndex != undefined)
              document.getElementById("batsmanName" + i).options[oldSelectedIndex].disabled = false;

            // Disable other (batting position)'s select options
            // for (batsman at battingPos)'s new selected batsman.
            document.getElementById("batsmanName" + i).options[newSelectedIndex].disabled = true;
            break;

          case 7:
            break;

          default: // error: less than 6 players in this match
            break;
         }
      }
      else
      {
         // Enable other (batting position)'s select options
         // for (batsman at battingPos)'s old selected batsman.
         if (oldSelectedIndex != undefined)
           for (i = 1; i <= 8; ++i)
             if (i != battingPos)
               document.getElementById("batsmanName" + i).options[oldSelectedIndex].disabled
                 = false;

         // Disable other (batting position)'s select options
         // for (batsman at battingPos)'s new selected batsman.
         // Purpose of 'limit': If team is short one or two players, any player may bat again to
         //                     fill the empty position.  Hence no option should be disabled from
         //                     the last two positions if the team is short players. 
         limit = (document.getElementById("batsmanName1").length < 8)? 6: 8;
         for (i = 1; i <= limit; ++i)
           if (i != battingPos)
             document.getElementById("batsmanName" + i).options[newSelectedIndex].disabled = true;
      }

      // new becomes old
      oldBatsmanNameSelectedIndicesArray[battingPos - 1] = newSelectedIndex;
   }
}

/*
 *
 */
function removeSelectBatsmanNameInstruction(battingPos)                           
{
   var x = document.getElementById("batsmanName" + battingPos);

   if (x.options[x.selectedIndex].text === selectBatsmanNameString)
     x.remove(x.selectedIndex);
}

/*
 *
 */
function removeSelectBowlerNameInstruction(overNo)                           
{
   var x = document.getElementById("bowlerName" + overNo);

   if (x.options[x.selectedIndex].text === selectBowlerNameString)
     x.remove(x.selectedIndex);
}

/*
 *
 */
function updateTeamScoreAndTeamPship(pShipNo)
{
   var i; // counter

   // update team score
   var teamScore = 0;
   for (i = 1; i <= 8; ++i)
     teamScore += Number(document.getElementById("runsScored" + i).value);
   teamScore -= Number(document.getElementById("teamPenaltyRuns").value);
   document.getElementById("teamScoreTH").innerHTML = teamScore;

   // update partnersip
   var pShip = 0;
   pShip  = Number(document.getElementById("runsScored" + ((pShipNo - 1) * 2 + 1)).value);
   pShip += Number(document.getElementById("runsScored" + ((pShipNo - 1) * 2 + 2)).value);
   document.getElementById("teamPship" + pShipNo + "TH").innerHTML = pShip;
}

/*
 *
 */
function updateOppTeamScoreAndOppTeamPship(pShipNo)
{
   // update opposition team score
   var oppTeamScore = 0;
   for (i = 1; i <= 16; ++i)
     oppTeamScore += Number(document.getElementById("runsConceded" + i).value);
   oppTeamScore -= Number(document.getElementById("oppTeamPenaltyRuns").value);
   document.getElementById("oppTeamScoreTH").innerHTML = oppTeamScore;

   // update opposition partnership
   var pShip = 0;
   pShip  = Number(document.getElementById("runsConceded" + ((pShipNo - 1) * 4 + 1)).value);
   pShip += Number(document.getElementById("runsConceded" + ((pShipNo - 1) * 4 + 2)).value);
   pShip += Number(document.getElementById("runsConceded" + ((pShipNo - 1) * 4 + 3)).value);
   pShip += Number(document.getElementById("runsConceded" + ((pShipNo - 1) * 4 + 4)).value);
   document.getElementById("oppTeamPship" + pShipNo + "TH").innerHTML = pShip;
}

/*
 *
 */
function clearWicketsTaken(overNo)
{
   document.getElementById("wicketsTaken" + overNo).value = "";
}

/*
 *
 */
function clearTeamPenaltyRuns()
{
   var x = document.getElementById("teamPenaltyRuns");

   if (x.value === "0")
     x.value = ""; // PROBLEM: causes exception (see "Tools->JavaScript Console" on firefox browser)
}

/*
 *
 */
function clearOppTeamPenaltyRuns()
{
   var x = document.getElementById("oppTeamPenaltyRuns");

   if (x.value === "0")
     x.value = ""; // PROBLEM: causes exception (see "Tools->JavaScript Console" on firefox browser)
}

/*
 *
 */
function updateInningsOrderSelectors(battOrderChanged)
{
   switch (battOrderChanged)
   {
      case 'true':
        if (document.getElementById("inningsOrderBatt").selectedIndex === 0)
          document.getElementById("inningsOrderBowl").selectedIndex = 1;
        else
          document.getElementById("inningsOrderBowl").selectedIndex = 0;
        break;
      case 'false':
        if (document.getElementById("inningsOrderBowl").selectedIndex === 0)
          document.getElementById("inningsOrderBatt").selectedIndex = 1;
        else
          document.getElementById("inningsOrderBatt").selectedIndex = 0;
        break;
   }
}

/*
 * Test whether the form has been filled in correctly.
 * Need to ensure that: Each integer field contains an integer in the expected range.
 *                      No player has batted more than once or bowled more than twice.
 *                      No bowler has bowled two overs in a row.
 */
function validate()
{
   var faultFound = false;
   var msg = "The form has been completed incorrectly.\n\n";

   // test runsScored[1-8] for NaN and ""
   for (i = 1; i <= 8 && !faultFound; ++i)
   {
      var runsScored = document.getElementById("runsScored" + i).value;

      if (runsScored === "" || isNaN(runsScored))
      {
         faultFound = true;
         msg += "An integer must be entered in the 'Runs Scored' field for each batsman.\n";
      }
   }

   // test runsConceded[1-16] and wicketsTaken[1-16] for NaN and ""
   for (i = 1; i <= 16 && !faultFound; ++i)
   {
      var wicketsTaken = document.getElementById("wicketsTaken" + i).value;
      var runsConceded = document.getElementById("runsConceded" + i).value;

      if (   wicketsTaken === "" || isNaN(wicketsTaken)
          || runsConceded === "" || isNaN(runsConceded))
      {
         faultFound = true;
         msg += "An integer must be entered in the 'Wickets Taken'";
         msg += " and 'Runs Conceded' field for each bowler.\n";
      }
   }

   if (faultFound)
   {
      alert(msg);
      return false;
   }
   else
     return true;
}

/*******************************************END*OF*FILE********************************************/

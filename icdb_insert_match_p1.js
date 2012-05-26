/**************************************************************************************************\
*                                                                                                  *
* FILENAME: "icdb_insert_match_p1.js"                                                              *
*                                                                                                  *
* PURPOSE: Javascripts for web page "icdb_insert_match_p1.php".                                    *
*                                                                                                  *
* AUTHOR: Tom McDonnell 2006                                                                       *
*                                                                                                  *
\**************************************************************************************************/

// GLOBAL VARIABLES ////////////////////////////////////////////////////////////////////////////////

onerror = handleErr; // set error handling function

var selectOppTeamNameString = 'Select Opp. Team Name'; // must match value in PHP file
var enterOppTeamNameString  = 'Enter Opp. Team Name';
var selectPlayerNameString  = 'Select Player Name';    // must match value in PHP file
var enterPlayerNameString   = 'Enter Player Name';

var oppTeamNameTDisSelector;        // boolean
var oppTeamNameTDselectorInnerHTML; // string
var oppTeamNameTDtextInnerHTML;     // string

var playerNameTDisSelector        = new Array(8); // array of boolean
var playerNameTDselectorInnerHTML = new Array(8); // array of string
var playerNameTDtextInnerHTML     = new Array(8); // array of string

var existingOppTeamNamesArray;      // to be defined in init()
var existingPlayerNamesArray;       // to be defined in init()

var oldPlayerNameSelectedIndicesArray = new Array(8);

// FUNCTIONS ///////////////////////////////////////////////////////////////////////////////////////

/*
 * Print error message if error in script.
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
   var i;   // counter
   var str; // temporary string variable

   updateDateSelectors(); // required since on load, today's date is selected

   // initialise oppTeamNameTD arrays
   oppTeamNameTDisSelector = true;
   oppTeamNameTDselectorInnerHTML = document.getElementById("oppTeamNameTD").innerHTML;
   str  = "<input type=\"text\" size=\"25\" maxLength=\"64\"";
   str += " value=\"" + enterOppTeamNameString + "\" id=\"oppTeamName\" name=\"oppTeamName\"";
   str += " onClick=\"clearOppTeamNameText()\" />";
   oppTeamNameTDtextInnerHTML = str;

   // initialise playerNameTD arrays
   for (i = 1; i <= 8; ++i)
   {
      playerNameTDisSelector[i - 1] = true;
      playerNameTDselectorInnerHTML[i - 1]
        = document.getElementById("playerName" + i + "TD").innerHTML;
      str  = "<input type=\"text\"" + i + "\" size=\"25\" maxLength=\"64\"";
      str += " value=\"" + enterPlayerNameString + "\" id=\"playerName" + i + "\"";
      str += " name=\"playerName" + i + "\"";
      str += " onClick=\"clearPlayerNameText(" + i + ")\" />";
      playerNameTDtextInnerHTML[i - 1] = str;
   }

   // initialise existingOppTeamNamesArray
   existingOppTeamNamesArray = new Array(document.getElementById("oppTeamName").length);
   for (i = 0; i < existingOppTeamNamesArray.length; ++i)
     existingOppTeamNamesArray[i] = document.getElementById("oppTeamName").options[i].text;

   // initialise existingPlayerNamesArray
   existingPlayerNamesArray = new Array(document.getElementById("playerName1").length);
   for (i = 0; i < existingPlayerNamesArray.length; ++i)
     existingPlayerNamesArray[i] = document.getElementById("playerName1").options[i].text;
}

/*
 * Return true if year is leap year, false otherwise.
 */
function isLeapYear(y)
{
   if (y % 4 === 0 && !((y % 100 === 0) && (y % 400 != 0))) return true;
   else                                                     return false;
}

/*
 * Disable/enable day options in day selector depending on month and year selected.
 * NOTE: option indices start at 0, option values start at 1.
 *       so eg. to change day 30, must use index 29.
 */
function updateDateSelectors()
{
   var day = document.getElementById("day");
   var mon = document.getElementById("month");

   switch (mon.selectedIndex + 1)
   {
    // february (28 days, except in leapyears in which case 29) 
    case 2:
      if (isLeapYear(document.getElementById("year").selectedIndex))
      {
         day.options[28].disabled = false;

         if (day.selectedIndex + 1 > 29)
           day.selectedIndex = 28;
      }
      else
      {
         day.options[28].disabled = true;

         if (day.selectedIndex + 1 > 28)
           day.selectedIndex = 27;
      }

      day.options[29].disabled = true;
      day.options[30].disabled = true;

      break;

    // months with 30 days
    case 4: case 6: case 9: case 11:
      day.options[28].disabled = false;
      day.options[29].disabled = false;
      day.options[30].disabled = true;

      if (day.selectedIndex + 1 > 30)
        day.selectedIndex = 29;

      break;

    // months with 31 days
    case 1: case 3: case 5: case 7: case 8: case 10: case 12:
      day.options[28].disabled = false;
      day.options[29].disabled = false;
      day.options[30].disabled = false;
      break;
   }
}

/*
 *
 */
function removeSelectOppTeamNameInstruction()
{
   var x = document.getElementById("oppTeamName");

   if (x.options[x.selectedIndex].text === selectOppTeamNameString)
     x.remove(x.selectedIndex);
}

/*
 *
 */
function removeSelectPlayerNameInstruction(playerNo)                           
{
   var x = document.getElementById("playerName" + playerNo);

   if (x.options[x.selectedIndex].text === selectPlayerNameString)
     x.remove(x.selectedIndex);
}

/*
 *
 */
function updateSelectPlayerNameOptions(playerNo)
{
   var i; // counter
   var newSelectedIndex;

   newSelectedIndex = document.getElementById("playerName" + playerNo).selectedIndex
   oldSelectedIndex = oldPlayerNameSelectedIndicesArray[playerNo - 1];

   // enable other players select options for playerNo's old selected player
   if (oldSelectedIndex != undefined)
     for (i = 1; i <= 8; ++i)
       if (i != playerNo && playerNameTDisSelector[i - 1])
         document.getElementById("playerName" + i).options[oldSelectedIndex].disabled = false;

   // disable other players select options for playerNo's new selected player
   for (i = 1; i <= 8; ++i)
     if (i != playerNo && playerNameTDisSelector[i - 1])
       document.getElementById("playerName" + i).options[newSelectedIndex].disabled = true;
     
   // new becomes old
   oldPlayerNameSelectedIndicesArray[playerNo - 1] = newSelectedIndex;
}

/*
 *
 */
function clearOppTeamNameText()
{
   var x = document.getElementById("oppTeamName");

   if (x.value === enterOppTeamNameString)
     x.value = ""; // PROBLEM: causes exception (see "Tools->JavaScript Console" on firefox browser)
}

/*
 *
 */
function clearPlayerNameText(playerNo)
{
   var x = document.getElementById("playerName" + playerNo);

   if (x.value === enterPlayerNameString)
     x.value = ""; // PROBLEM: causes exception (see "Tools->JavaScript Console" on firefox browser)
}

/*
 *
 */
function toggleOppTeamNameSelectorORtext()
{
   var x = document.getElementById("oppTeamNameTD");

   // swap oppTeamNameTDselectorInnerHTML with oppTeamNameTDtextInnerHTML
   if (oppTeamNameTDisSelector)
   {
      x.innerHTML = oppTeamNameTDtextInnerHTML;
      oppTeamNameTDisSelector = false;
   }
   else
   {
      x.innerHTML = oppTeamNameTDselectorInnerHTML;
      oppTeamNameTDisSelector = true;
   }
}

/*
 *
 */
function togglePlayerNameSelectorORtext(playerNo)
{
   var x  = document.getElementById("playerName" + playerNo + "TD");

   // swap playerNameTDselectorInnerHTML[playerNo - 1] with playerNameTDtextInnerHTML[playerNo - 1]
   if (playerNameTDisSelector[playerNo - 1])
   {
      x.innerHTML = playerNameTDtextInnerHTML[playerNo - 1];
      playerNameTDisSelector[playerNo - 1] = false;


      oldSelectedIndex = oldPlayerNameSelectedIndicesArray[playerNo - 1];

      // enable other players select options for playerNo's old selected player
      if (oldSelectedIndex != undefined)
        for (var i = 1; i <= 8; ++i)
          if (i != playerNo && playerNameTDisSelector[i - 1])
            document.getElementById("playerName" + i).options[oldSelectedIndex].disabled = false;

      // reset old selected index
      oldPlayerNameSelectedIndicesArray[playerNo] = undefined;
   }
   else
   {
      x.innerHTML = playerNameTDselectorInnerHTML[playerNo - 1];
      playerNameTDisSelector[playerNo - 1] = true;

      // No action needed regarding enabling/disabling options because
      // the selected option for playerNo is now 'selectPlayerNameString'.
   }
}

/*
 * Remove leading spaces, trailing spaces, and extra spaces between words from string str.
 * All whitespace characters are considered spaces for the purposes of this function.
 */
function removeExcessSpaces(str)
{
   // 1. Make a copy of 'str' called 'copy', and clear 'str'.
   // 2. Go through 'copy' character by character doing the following:
   //    If encounter characters other than whitespace, append to 'str'.
   //    If encounter space character (' ') following a non space character, append to 'str'.
   // 3. If last character of 'str' is now a space, remove this last character.

   // step 1
   var copy = str;
   str = "";

   // step 2
   var prevCharWasSpace = true;
   for (var i = 0; i < copy.length; ++i)
   {
      switch (copy[i] === ' ')
      {
       case false:
         str += copy[i];
         prevCharWasSpace = false;
         break;
       case true:
         if (!prevCharWasSpace)
           str += ' ';
         prevCharWasSpace = true;
         break;
      }
   }

   // step 3
   if (str[str.length - 1] === ' ')
     str = str.substring(0, str.length - 1);

   return str;
}

/*
 * Return the number of spaces in the string 'str'.
 */
function countSpaces(str)
{
   var n = 0;
   for (var i = 0; i < str.length; ++i)
     if (str[i] === ' ')
       ++n;

   return n;
}

/*
 * Test whether the form has been filled in correctly.
 * NOTE: Team can play if short 1 or 2 players.  Therefore player names 7 and 8 may be left blank.
 * Date and time are guaranteed to be correct (impossible to select invalid date or time).
 * Need to ensure that:
 *   (0) All strings have no leading or trailing spaces,
         and consist of words separated by at most one space.
 *   (1) Opp. team name has been entered.
 *   (2) If opp. team name is entered as 'New', it is not in existing opp. team names list.
 * Need to ensure that first six player name strings:
 *   (3) Are not 'selectPlayerNameString' or 'enterPlayerNameString' or blank.
 *   (4) Are not repeated in any other player name slot.
 *   (5) Consist of at most two words each.
 *   (6) If entered as 'New', are not in existing players names list.
 * Need to ensure that last two player name strings are:
 *   (7) Either ('selectPlayerNameString' or 'enterPlayerNameString' or blank)
 *           or (not repeated in any other name slot,
 *               and consist of at most two words each,
 *               and if entered as 'New', are not in existing players list).
 */
function validate()
{
   var i;                  // counter
   var j;                  // counter
   var playerNameA;        // temp variable
   var playerNameB;        // temp variable
   var faultFound = false; // boolean

   var msg = "The form has been completed incorrectly.\n\n";

   // (0) Remove excess spaces for oppTeamName and playerName[1-8]
   // (excess spaces are leading, trailing, and extras between words)
   if (!oppTeamNameTDisSelector)
     document.getElementById("oppTeamName").value
       = removeExcessSpaces(document.getElementById("oppTeamName").value);
   for (i = 1; i <= 8; ++i)
     if (!playerNameTDisSelector[i - 1])
       document.getElementById("playerName" + i).value
         = removeExcessSpaces(document.getElementById("playerName" + i).value);

   // (1) Test oppTeamName for default and blank values.
   var oppTeamName = document.getElementById("oppTeamName").value;
   if (   oppTeamName === ""
       || oppTeamName === selectOppTeamNameString
       || oppTeamName === enterOppTeamNameString )
   {
      faultFound = true;
      msg += "An opposition team name must be selected or entered.\n";
   }

   // (2) Test that new oppTeamName is not in the existing oppTeamNames list.
   var oppTeamNameA;
   var oppTeamNameB;
   if (!oppTeamNameTDisSelector && !faultFound)
   {
      oppTeamNameA = document.getElementById("oppTeamName").value;
      for (i = 0; i < existingOppTeamNamesArray.length && !faultFound; ++i)
      {
         oppTeamNameB = existingOppTeamNamesArray[i];
         if (oppTeamNameA === oppTeamNameB)
         {
            faultFound = true;
            msg += "Opposition Team's names entered as new must not be in the existing opposition";
            msg += " teams list.\n";
            msg += "If a new opposition team happens to have the same name as an existing";
            msg += " opposition team, the new team's name must be altered in order to keep all";
            msg += " team's names unique."
         }
      }
   }

   // (3) Test playerName[1-6] for default and blank values
   for (i = 1; i <= 6 && !faultFound; ++i)
   {
      playerNameA = document.getElementById("playerName" + i).value;

      if (   playerNameA === ""
          || playerNameA === enterPlayerNameString
          || playerNameA === selectPlayerNameString)
      {
         faultFound = true;
         msg += "A player name must be selected or entered in each of the first six positions.\n";
      }
   }

   // (4 & 7) Test playerName[1-8] for duplicate values.
   for (i = 1; i <= 8 && !faultFound; ++i)
   {
      playerNameA = document.getElementById("playerName" + i).value;

      if (i <= 6 || (   playerNameA != ""
                     && playerNameA != enterPlayerNameString
                     && playerNameA != selectPlayerNameString))
      {
         for (j = i; j <= 8 && !faultFound; ++j)
         {
            playerNameB = document.getElementById("playerName" + j).value;
            if (i != j && playerNameA === playerNameB)
            {
               faultFound = true;
               msg += "Player's names must be unique.\n";
               msg += "Player's names " + i + " & " + j + " are equal.\n";
            }
         }
      }
   }

   // (5 & 7) Test playerName[1-8] for more than 2 words.
   for (i = 1; i <= 8 && !faultFound; ++i)
   {
      playerNameA = document.getElementById("playerName" + i).value;

      if (i <= 6 || (   playerNameA != ""
                     && playerNameA != enterPlayerNameString
                     && playerNameA != selectPlayerNameString))
      {
         if (countSpaces(playerNameA) > 1)
         {
            faultFound = true;
            msg += "Player's names must consist of one or two words only.\n";
            msg += "Player " + i + " currently has more than two words in his/her name field.\n";
         }
      }
   }

   // (6 & 7) Test that new player names are not in existing player names list.
   for (i = 1; i <= 8 && !faultFound; ++i)
   {
      if (!playerNameTDisSelector[i - 1])
      {
         playerNameA = document.getElementById("playerName" + i).value;

         for (j = 0; j < existingPlayerNamesArray.length && !faultFound; ++j)
         {
            playerNameB = existingPlayerNamesArray[j];
            if (playerNameA === playerNameB)
            {
               faultFound = true;
               msg += "Player's names entered as new must not be in the existing players list.\n";
               msg += "Player's name " + i + " is in the existing players list.\n\n";
               msg += "If a new player happens to have the same first and last names as an";
               msg += " existing player, the new player's name must be altered in order to keep";
               msg += " all player's names unique."
            }
         }
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

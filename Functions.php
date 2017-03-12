<?php
/*
The MIT License (MIT)

Copyright © 2017 ALEXANDER BIVZYUK

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and
associated documentation files (the “Software”), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following
conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions
of the Software.

THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT
OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.
*/

function GetLeaderboard($data)
{
  if ($data == NULL) return NULL;

  $leaderboard = array();
  foreach ($data['tournaments'] as $tournament)
  {
    foreach ($tournament['phases'] as $phase)
    {
      if ($phase['type'] != 'D')
        continue;

      foreach ($phase['ranks'] as $rank)
      {
        $team         = GetTeamById($data, $rank['team_id']);
        $teamPlayers  = $team['players'];
        $leaderboard[] = array(GetPlayerById($data, $teamPlayers[0]), GetPlayerById($data, $teamPlayers[1]));
      }
    }
  }

  return $leaderboard;
}

function GetTeamById($data, $teamId)
{
  if ($data == NULL) return NULL;

  foreach ($data['tournaments'] as $tournament)
    foreach ($tournament['teams'] as $team)
      if ($team['id'] == $teamId)
        return $team;

  return NULL;
}

function GetPlayerById($data, $playerId)
{
  if ($data == NULL) return NULL;

  foreach ($data['players'] as $player)
    if ($player['id'] == $playerId)
      return $player;

  return NULL;
}

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

function Parse($xml)
{
  $data = XmlToObject($xml);
  if ($data == NULL) return NULL;

  //DEBUG: View xml tree.
  //echo '<pre>' . var_export($data, true) . '</pre><br><br>';

  //Date when file created.
  $date     = date_parse_from_format("Ymdsih", $data->creationDate);
  //Fast version.
  $version  = (string)$data->fastVersion;
  //Fast build.
  $build    = (int)$data->fastBuild;
  //Get players list.
  $playerInfos = $data->registeredPlayers->playerInfos;
  if ($playerInfos)
  {
    $players  = array();
    foreach ($playerInfos as $playerInfo)
    {
      $license = (int)$playerInfo->noLicense;
      if ($license)
      {
        $playerId   = (int)$playerInfo->playerId;
        $players[]  = array('id' => $playerId, 'license' => $license);
      }
      else
      {
        $playerId   = (int)$playerInfo->player->id;
        $firstName  = (string)$playerInfo->player->person->firstName;
        $lastName   = (string)$playerInfo->player->person->lastName;
        $players[]  = array('id' => $playerId, 'first_name' => $firstName, 'last_name' => $lastName);
      }
    }
  }
  //Get tournaments list.
  $tournaments = array();
  if ($data->tournaments->tournament)
  {
    foreach ($data->tournaments->tournament as $tournament)
    {
      $name       = (string)$tournament->name;
      $type       = (string)$tournament->type;
      $beginDate  = date_parse_from_format("d/m/Y", (string)$tournament->beginDate);
      $endDate    = date_parse_from_format("d/m/Y", (string)$tournament->endDate);
      $country    = (string)$tournament->country;
      //Get teams list.
      $teams = array();
      if ($tournament->competition->competitionTeam)
      {
        foreach ($tournament->competition->competitionTeam as $competitionTeam)
        {
          $teamId     = (int)$competitionTeam->id;
          $player1Id  = (int)$competitionTeam->team->player1Id;
          $player2Id  = (int)$competitionTeam->team->player2Id;
          $teams[]    = array('id' => $teamId, 'players' => array($player1Id, $player2Id));
        }
      }
      //Get phases list.
      $phases = array();
      if ($tournament->competition->phase)
      {
        foreach ($tournament->competition->phase as $phase)
        {
          $phaseType = (string)$phase->phaseType;
          if ($phase->phaseRanking->ranking)
          {
            $ranks = array();
            foreach ($phase->phaseRanking->ranking as $ranking)
            {
              $teamId = (int)$ranking->definitivePhaseOpponentRanking->teamId;
              $ranks[] = array('team_id' => $teamId);
            }
          }
          $phases[] = array('type' => $phaseType, 'ranks' => $ranks);
        }
      }

      $tournaments[] = array('name' => $name,
                             'type' => $type,
                             'begin_date' => array('day' => $beginDate['day'],
                                                   'month' => $beginDate['month'],
                                                   'year' => $beginDate['year']),
                             'end_date' => array('day' => $endDate['day'],
                                                 'month' => $endDate['month'],
                                                 'year' => $endDate['year']),
                             'country' => $country,
                             'teams' => $teams,
                             'phases' => $phases);
    }
  }

  $output = array('date'    => array('hour'   => $date['hour'],
                                     'minute' => $date['minute'],
                                     'second' => $date['second'],
                                     'day'    => $date['day'],
                                     'month'  => $date['month'],
                                     'year'   => $date['year']),
                  'version' => $version,
                  'build'   => $build,
                  'players' => $players,
                  'tournaments' => $tournaments);

  return $output;
}


function XmlToObject($xml)
{
  libxml_use_internal_errors(true);
  try {
     return new SimpleXMLElement($xml);
  } catch (Exception $e) {
    return NULL;
  }
}

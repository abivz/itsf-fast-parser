<?php
class FASTParser
{
    public $_DATA = NULL;

    //  Parse FAST xml content and store result in $_DATA
    //  Return TRUE or FALSE
    public function Parse($xml_content)
    {
        $data = $this->XMLContentToObject($xml_content);
        if ($data === NULL)
            return FALSE;

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
        else
        {
            return FALSE;
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
                else
                {
                    return FALSE;
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
                else
                {
                    return FALSE;
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
        else
        {
            return FALSE;
        }

        $this->_DATA = array('date'    => array('hour'   => $date['hour'],
                                            'minute' => $date['minute'],
                                            'second' => $date['second'],
                                            'day'    => $date['day'],
                                            'month'  => $date['month'],
                                            'year'   => $date['year']),
                        'version' => $version,
                        'build'   => $build,
                        'players' => $players,
                        'tournaments' => $tournaments);

        return TRUE;
    }

    public function GetLeaderboard()
    {
        if ($this->_DATA === NULL) return NULL;

        $leaderboard = array();
        foreach ($this->_DATA['tournaments'] as $tournament)
        {
            foreach ($tournament['phases'] as $phase)
            {
                if ($phase['type'] == 'D' || $phase['type'] == 'S')
                {
                    foreach ($phase['ranks'] as $rank)
                    {
                        $team         = $this->GetTeamById($rank['team_id']);
                        $teamPlayers  = $team['players'];
                        $leaderboard[] = array($this->GetPlayerById($teamPlayers[0]), $this->GetPlayerById($teamPlayers[1]));
                    }
                }
            }
        }

        return $leaderboard;
    }

    public function GetTeamById($teamId)
    {
        if ($this->_DATA === NULL) return NULL;

        foreach ($this->_DATA['tournaments'] as $tournament)
            foreach ($tournament['teams'] as $team)
            if ($team['id'] == $teamId)
                return $team;

        return NULL;
    }

    public function GetPlayerById($playerId)
    {
        if ($this->_DATA === NULL) return NULL;

        foreach ($this->_DATA['players'] as $player)
            if ($player['id'] == $playerId)
            return $player;

        return NULL;
    }

    private function XMLContentToObject($xml_content)
    {
        libxml_use_internal_errors(TRUE);
        try
        {
            return new SimpleXMLElement($xml_content);
        }
        catch (Exception $e)
        {
            return NULL;
        }
    }
}

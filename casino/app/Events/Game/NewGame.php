<?php

namespace VanguardLTE\Events\Game;

use VanguardLTE\Game;

class NewGame
{
    /**
     * @var Returns
     */
    protected $NewGame;

    public function __construct(Game $NewGame)
    {
        $this->NewGame = $NewGame;
    }

    /**
     * @Game Games
     */
    public function getNewGame()
    {
        return $this->NewGame;
    }
}

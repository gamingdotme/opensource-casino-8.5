<?php

namespace VanguardLTE\Events\Game;

use VanguardLTE\Game;

class GameEdited
{
    /**
     * @var Games
     */
    protected $editedGame;
    protected $editedCategory;
    protected $editedMatch;

    public function __construct(Game $editedGame, $editedCategory=0, $editedMatch=0)
    {
        $this->editedGame = $editedGame;
        $this->editedCategory = $editedCategory;
        $this->editedMatch = $editedMatch;
    }


    public function getEditedGame()
    {
        return $this->editedGame;
    }

    public function getEditedCategory()
    {
        return $this->editedCategory;
    }

    public function getEditedMatch()
    {
        return $this->editedMatch;
    }
}

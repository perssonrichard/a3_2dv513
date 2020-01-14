<?php

class Controller
{
    private $view;
    private $db;

    public function __construct()
    {
        $this->view = new MainView();
        $this->db = new SteamGamesDB();
    }

    public function run()
    {
        if ($this->view->userWantsToSearch()) {
            $search = $this->view->getSearch();

            if ($this->db->gameExists($search)) {
                $this->view->setGameFound(true);
            } else {
                $this->view->setGameFound(false);
            }
        } else if ($this->view->userWantsToViewGameList()) {
            $this->view->setViewGameList();
        } else if ($this->view->userWantsToViewPriceRange()) {
            $this->view->setViewPriceRange();
        } else if ($this->view->userWantsToViewHardWareReq()) {
            $this->view->setViewHardwareReq();
        } else if ($this->view->userWantsToViewGenreAndCategory()){
            $this->view->setGenreAndCategory();
        } else if ($this->view->userWantsToViewScreenshots()) {
            $this->view->setScreenshot();
        }
    }
}

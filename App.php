<?php
session_start();

require_once('view/MainView.php');
require_once('controller/controller.php');
require_once('model/SteamGamesDB.php');

class App
{
    private $mainView;
    private $controller;

    public function __construct ()
    {
        $this->mainView = new MainView();
        $this->controller = new Controller();
    }

    public function run ()
    {
        $this->controller->run();
        $this->mainView->render();
    }
}

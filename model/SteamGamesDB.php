<?php

class SteamGamesDB
{
    /**
     * Server connection
     */
    private const SERVER_NAME = 'localhost';
    private const USERNAME = 'root';
    private const PASSWORD = '';
    private const DATABASE_NAME = 'steam_games';

    private $mysqli;

    public function __construct()
    {
        $this->mysqli = new mysqli(self::SERVER_NAME, self::USERNAME, self::PASSWORD, self::DATABASE_NAME);

        if ($this->mysqli->connect_error) {
            die('Connect Error (' . $this->mysqli->connect_errno . ') '
                . $this->mysqli->connect_error);
        }
    }

    public function gameExists(string $input): bool
    {
        $prepare = $this->mysqli->prepare('SELECT * FROM game WHERE name = ?');
        $prepare->bind_param('s', $input);
        $prepare->execute();

        $result = $prepare->get_result();

        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getAllGamesStartingWithCharacter(string $input): array
    {
        $prepare = $this->mysqli->prepare('SELECT name FROM game WHERE name LIKE ? ORDER BY name');
        $char = "$input%";

        $prepare->bind_param('s', $char);
        $prepare->execute();

        $result = $prepare->get_result()->fetch_all();

        $names = [];
        foreach ($result as $value) {
            array_push($names, $value[0]);
        }

        return $names;
    }

    public function getAllGamesInPriceRange(array $arr): array
    {
        $view = 'v_range';

        $viewQuery = 'CREATE VIEW ' . $view . ' AS 
        SELECT G.*, P.SEK 
        FROM game G, price P WHERE G.id = P.id 
        AND P.SEK BETWEEN ' . $arr[0] . ' AND ' . $arr[1] . ';';

        $this->mysqli->query($viewQuery);

        $gameQuery = 'SELECT name, SEK FROM ' . $view . ' ORDER BY SEK desc;';

        $result = $this->mysqli->query($gameQuery);

        $games = [];
        foreach ($result as $value) {
            array_push($games, $value);
        }

        $dropView = 'DROP VIEW ' . $view . '';
        $this->mysqli->query($dropView);

        return $games;
    }

    public function getAllGamesWithGenreAndCategory(string $genre, string $category): array
    {
        $prepare = $this->mysqli->prepare(
            'SELECT name, total FROM game G 
            INNER JOIN rating R ON G.id = R.id 
            WHERE genre LIKE ? AND category LIKE ? 
            ORDER BY total DESC'
        );
        $gnr = "%$genre%";
        $cat = "%$category%";

        $prepare->bind_param('ss', $gnr, $cat);
        $prepare->execute();

        $result = $prepare->get_result();

        $games = [];

        while ($row = $result->fetch_row()) {
            array_push($games, $row);
        }

        return $games;
    }

    public function getHardwareReq(string $game): array
    {
        $query = "SELECT windows, linux, mac FROM hardware_requirement H INNER JOIN game G ON H.id = G.id WHERE name = '$game';";

        $result = $this->mysqli->query($query);

        foreach ($result as $value) {
            return $value;
        }
    }

    public function getScreenshots(string $game): string
    {
        $query = "SELECT screenshot FROM media M
        INNER JOIN game G ON M.id = G.id 
        WHERE name = '$game'";

        $result = $this->mysqli->query($query)->fetch_row();


        return $result[0];
    }

    public function getNumberOfGames(): int
    {
        $query = "SELECT COUNT(name) FROM game;";
        $result = $this->mysqli->query($query)->fetch_row();

        return (int) $result[0];

    }
}

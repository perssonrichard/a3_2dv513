<?php


class MainView
{
  // Html IDs
  private const SEARCH_FORM = "MainView::SearchGameForm";
  private const SUBMIT_SEARCH = "MainView::SubmitSearch";
  private const WINDOWS = "MainView::Windows";
  private const LINUX = "MainView::Linux";
  private const MAC = "MainView::Mac";
  private const SELECT_GENRE = "MainView::SelectGenre";
  private const SELECT_CATEGORY = "MainView::SelectCategory";
  private const SCREENSHOTS = "MainView::Screenshots";


  private const SEARCH_VALUE = 'SearchValue';

  private const GAME_FOUND = 'GAME_FOUND';
  private const VIEW_GAME_LIST = 'VIEW_GAME_LIST';
  private const VIEW_PRICE_RANGE = 'VIEW_PRICE_RANGE';
  private const VIEW_SCREENSHOTS = 'VIEW_SCREENSHOTS';

  private const PRICE_FREE = 'Free';
  private const PRICE_1_99 = '1-99';
  private const PRICE_100_299 = '100-299';
  private const PRICE_300_499 = '300-499';
  private const PRICE_500_699 = '500-699';
  private const PRICE_700_999 = '700-999';
  private const PRICE_1000 = '1000';

  private const PRICE_LIST = 'Price';
  private const CHARACTER_LIST = 'CharacterList';
  private const GENRE_CATEGORY_LIST = 'GenreCategoryList';

  private const VIEW_HARDWARE_REQ = 'ViewHardWareReq';

  private const GENRE_CATEGORY = "GenreCategory";
  private const GENRE = 'Genre';
  private const CATEGORY = "Category";

  private $db;

  public function __construct()
  {
    $this->db = new SteamGamesDB();
  }

  public function render()
  {
    echo '<!DOCTYPE html>
        <html lang="en">
          <head>
          <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
          <link rel="stylesheet" href="css/style.css">
          <meta charset="utf-8">
          <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
          <title>Steam Database</title>
          </head>

          <body>
            <div class="container">
              ' . $this->renderSearchForGameForm() . '
              <br>  
              ' . $this->renderGenreAndCategory() . '
              <br>
              ' . $this->renderCharacterList() . '
              ' . $this->renderPriceRange() . '
              <br>
              <br>
              ' . $this->renderContent() . '
            </div>

            <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

          </body>
        </html>
      ';
  }

  public function userWantsToSearch(): bool
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST[self::SUBMIT_SEARCH])) {
      $_SESSION[self::SEARCH_VALUE] = $_POST[self::SEARCH_FORM];

      return true;
    }
    return false;
  }

  public function getSearch(): string
  {
    return $_SESSION[self::SEARCH_VALUE];
  }

  public function setGameFound(bool $isFound): void
  {
    $_SESSION[self::GAME_FOUND] = $isFound;
  }

  public function userWantsToViewGameList(): bool
  {
    $queryString = $_SERVER['QUERY_STRING'];

    // If one letter or number
    if (ctype_alnum($queryString) && strlen($queryString) == 1) {
      return true;
    } else {
      return false;
    }
  }

  public function setViewGameList(): void
  {
    $_SESSION[self::VIEW_GAME_LIST] = true;
  }

  public function userWantsToViewPriceRange(): bool
  {
    $queryString = $_SERVER['QUERY_STRING'];

    if (
      $queryString == self::PRICE_1_99 ||
      $queryString == self::PRICE_100_299 ||
      $queryString == self::PRICE_300_499 ||
      $queryString == self::PRICE_500_699 ||
      $queryString == self::PRICE_700_999 ||
      $queryString == self::PRICE_1000 ||
      $queryString == self::PRICE_FREE
    ) {
      return true;
    } else {
      return false;
    }
  }

  public function setViewPriceRange(): void
  {
    $_SESSION[self::VIEW_PRICE_RANGE] = true;
  }

  public function getPriceRange(): array
  {
    $queryString = $_SERVER['QUERY_STRING'];

    switch ($queryString) {
      case self::PRICE_FREE:
        return [0, 0];
      case self::PRICE_1_99:
        return [1, 99];
      case self::PRICE_100_299:
        return [100, 299];
      case self::PRICE_300_499:
        return [300, 499];
      case self::PRICE_500_699:
        return [500, 699];
      case self::PRICE_700_999:
        return [700, 999];
      case self::PRICE_1000:
        return [1000, 9999];
    }
  }

  public function userWantsToViewHardWareReq(): bool
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && (isset($_POST[self::WINDOWS])) || isset($_POST[self::LINUX]) || isset($_POST[self::MAC])) {
      return true;
    }
    return false;
  }

  public function setViewHardwareReq(): void
  {
    if (isset($_POST[self::WINDOWS])) {
      $_SESSION[self::VIEW_HARDWARE_REQ] = self::WINDOWS;
    } else if (isset($_POST[self::LINUX])) {
      $_SESSION[self::VIEW_HARDWARE_REQ] = self::LINUX;
    } else if (isset($_POST[self::MAC])) {
      $_SESSION[self::VIEW_HARDWARE_REQ] = self::MAC;
    }
  }

  public function userWantsToViewGenreAndCategory(): bool
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST[self::SELECT_GENRE]) && isset($_POST[self::SELECT_CATEGORY])) {
      $_SESSION[self::GENRE] = $_POST[self::SELECT_GENRE];
      $_SESSION[self::CATEGORY] = $_POST[self::SELECT_CATEGORY];

      return true;
    }
    return false;
  }

  public function setGenreAndCategory(): void
  {
    $_SESSION[self::GENRE_CATEGORY] = true;
  }

  private function getGenre(): string
  {
    return $_SESSION[self::GENRE];
  }

  private function getCategory(): string
  {
    return $_SESSION[self::CATEGORY];
  }

  public function userWantsToViewScreenshots(): bool
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST[self::SCREENSHOTS])) {
      return true;
    }
    return false;
  }

  public function setScreenshot(): void
  {
    $_SESSION[self::VIEW_SCREENSHOTS] = true;
  }

  private function renderContent(): string
  {
    if (isset($_SESSION[self::GAME_FOUND]) && $_SESSION[self::GAME_FOUND]) {
      unset($_SESSION[self::GAME_FOUND]);
      return $this->renderGameFound();
    }

    if (isset($_SESSION[self::GAME_FOUND]) && $_SESSION[self::GAME_FOUND] == false) {
      unset($_SESSION[self::GAME_FOUND]);
      return '<b>Game Not Found</b>';
    }

    if (isset($_SESSION[self::VIEW_GAME_LIST]) && $_SESSION[self::VIEW_GAME_LIST]) {
      unset($_SESSION[self::VIEW_GAME_LIST]);
      return $this->renderGameList(self::CHARACTER_LIST);
    }

    if (isset($_SESSION[self::VIEW_PRICE_RANGE]) && $_SESSION[self::VIEW_PRICE_RANGE]) {
      unset($_SESSION[self::VIEW_PRICE_RANGE]);
      return $this->renderGameList(self::PRICE_LIST);
    }

    if (isset($_SESSION[self::VIEW_HARDWARE_REQ])) {
      $game = $this->getSearch();
      $reqArr = $this->db->getHardwareReq($game);

      if ($_SESSION[self::VIEW_HARDWARE_REQ] == self::WINDOWS) {
        unset($_SESSION[self::VIEW_HARDWARE_REQ]);

        if ($reqArr['windows'] == null) {
          return 'No hardware requirements for Windows found';
        }

        return $reqArr['windows'];
      } else if ($_SESSION[self::VIEW_HARDWARE_REQ] == self::LINUX) {
        unset($_SESSION[self::VIEW_HARDWARE_REQ]);

        if ($reqArr['linux'] == null) {
          return 'No hardware requirements for Linux found';
        }

        return $reqArr['linux'];
      } else if ($_SESSION[self::VIEW_HARDWARE_REQ] == self::MAC) {
        unset($_SESSION[self::VIEW_HARDWARE_REQ]);

        if ($reqArr['mac'] == null) {
          return 'No hardware requirements for Mac found';
        }

        return $reqArr['mac'];
      }
    }

    if (isset($_SESSION[self::GENRE_CATEGORY]) && $_SESSION[self::GENRE_CATEGORY]) {
      unset($_SESSION[self::GENRE_CATEGORY]);

      return  $this->renderGameList(self::GENRE_CATEGORY_LIST);
    }

    if (isset($_SESSION[self::VIEW_SCREENSHOTS]) && $_SESSION[self::VIEW_SCREENSHOTS]) {
      unset($_SESSION[self::VIEW_SCREENSHOTS]);

      return $this->renderScreenshots();
    }

    return '';
  }

  private function renderGameFound(): string
  {
    $game = $this->getSearch();

    return '
        <p>Game <i>' . $game . '</i> Found!</p>

        <form method="POST">
        <p>Select an OS to view hardware requirements</p>
        <input type="submit" class="btn btn-outline-primary" name="' . self::WINDOWS . '" value="Windows" />
        <input type="submit" class="btn btn-outline-primary" name="' . self::LINUX . '" value="Linux" />
        <input type="submit" class="btn btn-outline-primary" name="' . self::MAC . '" value="Mac" />
        </form>
        
        <br>

        <form method="POST">
        <input type="submit" class="btn btn-outline-primary" name="' . self::SCREENSHOTS . '" value="View Screenshots" />
        </form>
    ';
  }

  private function renderSearchForGameForm(): string
  {
    return '
      <form method="POST" action="index.php" class="border border-primary p-2">
        <label for="searchGame">Search for a game</label>
        <input type="text" class="form-control" id="' . self::SEARCH_FORM . '" name="' . self::SEARCH_FORM . '" placeholder="Portal">
        <small id="searchInfo" class="form-text text-muted">Total number of games: '.$this->db->getNumberOfGames().'</small>
        <br>
        <input type="submit" class="btn btn-outline-primary" name="' . self::SUBMIT_SEARCH . '" value="Search" />
      </form>
      ';
  }

  private function renderCharacterList(): string
  {
    return '            
      <div class="btn-group">
      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">A-Z <span class="caret"></span></button>
        <ul class="dropdown-menu scrollable-menu" role="menu">
        <li class="list-group-item"><a href="?A">A</a></li>
        <li class="list-group-item"><a href="?B">B</a></li>
        <li class="list-group-item"><a href="?C">C</a></li>
        <li class="list-group-item"><a href="?D">D</a></li>
        <li class="list-group-item"><a href="?E">E</a></li>
        <li class="list-group-item"><a href="?F">F</a></li>
        <li class="list-group-item"><a href="?G">G</a></li>
        <li class="list-group-item"><a href="?H">H</a></li>
        <li class="list-group-item"><a href="?I">I</a></li>
        <li class="list-group-item"><a href="?J">J</a></li>
        <li class="list-group-item"><a href="?K">K</a></li>
        <li class="list-group-item"><a href="?L">L</a></li>
        <li class="list-group-item"><a href="?M">M</a></li>
        <li class="list-group-item"><a href="?N">N</a></li>
        <li class="list-group-item"><a href="?O">O</a></li>
        <li class="list-group-item"><a href="?P">P</a></li>
        <li class="list-group-item"><a href="?Q">Q</a></li>
        <li class="list-group-item"><a href="?R">R</a></li>
        <li class="list-group-item"><a href="?S">S</a></li>
        <li class="list-group-item"><a href="?T">T</a></li>
        <li class="list-group-item"><a href="?U">U</a></li>
        <li class="list-group-item"><a href="?V">V</a></li>
        <li class="list-group-item"><a href="?W">W</a></li>
        <li class="list-group-item"><a href="?X">X</a></li>
        <li class="list-group-item"><a href="?Y">Y</a></li>
        <li class="list-group-item"><a href="?Z">Z</a></li>
        </ul>
      </div>

      <div class="btn-group">
      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">0-9 <span class="caret"></span></button>
        <ul class="dropdown-menu scrollable-menu" role="menu">
        <li class="list-group-item"><a href="?0">0</a></li>
        <li class="list-group-item"><a href="?1">1</a></li>
        <li class="list-group-item"><a href="?2">2</a></li>
        <li class="list-group-item"><a href="?3">3</a></li>
        <li class="list-group-item"><a href="?4">4</a></li>
        <li class="list-group-item"><a href="?5">5</a></li>
        <li class="list-group-item"><a href="?6">6</a></li>
        <li class="list-group-item"><a href="?7">7</a></li>
        <li class="list-group-item"><a href="?8">8</a></li>
        <li class="list-group-item"><a href="?9">9</a></li>
        </ul>
      </div>
		';
  }

  private function renderPriceRange(): string
  {
    return '
        <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Price Range <span class="caret"></span></button>
          <ul class="dropdown-menu scrollable-menu" role="menu">
          <li class="list-group-item"><a href="?' . self::PRICE_FREE . '">' . self::PRICE_FREE . '</a></li>
          <li class="list-group-item"><a href="?' . self::PRICE_1_99 . '">' . self::PRICE_1_99 . ' SEK</a></li>
          <li class="list-group-item"><a href="?' . self::PRICE_100_299 . '">' . self::PRICE_100_299 . ' SEK</a></li>
          <li class="list-group-item"><a href="?' . self::PRICE_300_499 . '">' . self::PRICE_300_499 . ' SEK</a></li>
          <li class="list-group-item"><a href="?' . self::PRICE_500_699 . '">' . self::PRICE_500_699 . ' SEK</a></li>
          <li class="list-group-item"><a href="?' . self::PRICE_700_999 . '">' . self::PRICE_700_999 . ' SEK</a></li>
          <li class="list-group-item"><a href="?' . self::PRICE_1000 . '">' . self::PRICE_1000 . '+ SEK</a></li>
          </ul>
        </div>
      ';
  }

  private function renderGenreAndCategory(): string
  {
    return '
        <form method="POST" action="index.php" class="border border-primary p-2">
        <div class="form-group">
        <label for="' . self::SELECT_GENRE . '">Select Genre</label>
        <select class="form-control" id="' . self::SELECT_GENRE . '" name="' . self::SELECT_GENRE . '">
        <option>Action</option>
        <option>Casual</option>
        <option>Free to Play</option>
        <option>RPG</option>
        <option>Sports</option>
        </select>
        </div>

        <div class="form-group">
        <label for="' . self::SELECT_CATEGORY . '">Select Category</label>
        <select class="form-control" id="' . self::SELECT_CATEGORY . '" name="' . self::SELECT_CATEGORY . '">
        <option>Single-player</option>
        <option>Multi-player</option>
        </select>
        </div>

        <button type="submit" class="btn btn-outline-primary">Search</button>
        </form>
      ';
  }

  private function renderScreenshots(): string
  {
    $game = $this->getSearch();

    $screenshots = $this->db->getScreenshots($game);

    $screenshotArr = explode(';', $screenshots);
    // Remove last value of arr because empty string
    array_pop($screenshotArr);

    $list = '';
    foreach ($screenshotArr as $screenshot) {
      $list .= '<img src="' . $screenshot . '" alt="screenshot">';
    }

    return $list;
  }

  private function renderGameList(string $typeOfList): string
  {
    if ($typeOfList == self::CHARACTER_LIST) {
      $letter = $_SERVER['QUERY_STRING'];
      $list = '';

      $gameArr = $this->db->getAllGamesStartingWithCharacter($letter);

      foreach ($gameArr as $value) {
        $list .= '<li class="list-group-item"> ' . $value . ' </li>';
      }

      return '<ul class="list-group">' . $list . '</ul>';
    } else if ($typeOfList == self::PRICE_LIST) {
      $priceRange = $this->getPriceRange();

      $gameArr = $this->db->getAllGamesInPriceRange($priceRange);
      $rows = '';

      foreach ($gameArr as $value) {
        $name = $value['name'];
        $price = $value['SEK'];

        $rows .= '<tr>';
        $rows .= '<td>' . $name . '</td>';
        $rows .= '<td>' . $price . '</td>';
        $rows .= '</tr>';
      }

      return '
      <table class="table">
      <thead>
        <tr>
          <th scope="col">Name</th>
          <th scope="col">Price</th>
        </tr>
      </thead>
      <tbody>
          ' . $rows . '
      </tbody>
    </table>    
      ';
    } else if ($typeOfList == self::GENRE_CATEGORY_LIST) {
      $genre = $this->getGenre();
      $category = $this->getCategory();

      $games = $this->db->getAllGamesWithGenreAndCategory($genre, $category);

      $rows = '';
      foreach ($games as $value) {
        $name = $value[0];
        $rating = $value[1];

        $rows .= '<tr>';
        $rows .= '<td>' . $name . '</td>';
        $rows .= '<td>' . $rating . '</td>';
        $rows .= '</tr>';
      }

      return '
      <table class="table">
      <thead>
        <tr>
          <th scope="col">Name</th>
          <th scope="col">Rating</th>
        </tr>
      </thead>
      <tbody>
          ' . $rows . '
      </tbody>
    </table>    
      ';
    }
  }
}

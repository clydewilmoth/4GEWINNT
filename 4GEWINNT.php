<?php
error_reporting(E_ALL ^ E_WARNING);

session_start();
global $_SESSION;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['choice'])) {
    gamelogic();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['restart'])) {
    $_SESSION["currentplayer"] = "red";
    $_SESSION["playcounter"] = 0;
    $_SESSION["restart"] = '<button id="restart" type="submit" name="restart" hidden>&#8635</button>';
    $_SESSION["message"] = "";
    unset($_SESSION["winningchip"]);
    fillboard();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selection'])) {
    $_SESSION["gamemode"] = (int) $_POST['selection'];
    $_SESSION["currentplayer"] = "red";
    $_SESSION["playcounter"] = 0;
    $_SESSION["restart"] = '<button id="restart" type="submit" name="restart" hidden>&#8635</button>';
    $_SESSION["message"] = "";
    fillboard();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['menu'])) {
    unset($_SESSION["gamemode"]);
    unset($_SESSION["currentplayer"]);
    unset($_SESSION["playcounter"]);
    unset($_SESSION["restart"]);
    unset($_SESSION["message"]);
    unset($_SESSION["board"]);
    unset($_SESSION["winningchip"]);
}

function fillboard(){
    for ($row = 0; $row < 6; $row++) {
        for ($col = 0; $col < 7; $col++) {
            $_SESSION["board"][$row][$col] = "white";
        }
    }
}

function gamelogic(){
    if ($_SESSION["restart"] === '<button id="restart" type="submit" name="restart" hidden>&#8635</button>') {
        if ($_SESSION["gamemode"] === 1) {
            for ($i = 0; $i <= 1; $i++) {
                if ($i === 0) {
                    $playerchoice = (int) $_POST['choice'];
                } else {
                    $playerchoice = botturn();
                }

                $placemem = placechip($playerchoice);
                $currentplayermem = currentplayerger();
                $_SESSION["message"] = $placemem[0] ? "" : "Spalte ist schon voll!";

                if (!($_SESSION["message"] === "")) {
                    break;
                }

                $_SESSION["playcounter"]++;

                if ($currentplayermem === "Gelb") {
                    $_SESSION["message"] = checkwin($placemem[1], $playerchoice) ? "Roboter "
                    . $currentplayermem
                    . " hat gewonnen!" : "";
                } else {
                    $_SESSION["message"] = checkwin($placemem[1], $playerchoice) ? "Spieler "
                    . $currentplayermem
                    . " hat gewonnen!" : "";
                }

                swapplayer();

                if (
                    $_SESSION["message"] === "Spieler "
                    . $currentplayermem
                    . " hat gewonnen!" ||
                    $_SESSION["message"] === "Roboter "
                    . $currentplayermem
                    . " hat gewonnen!"
                ) {
                    $_SESSION["restart"] = '<button id="restart" type="submit" name="restart">&#8635</button>';
                    $_SESSION["winningchip"] = [$placemem[1], $playerchoice];
                    break;
                }

                $_SESSION["message"] = $_SESSION["playcounter"] === 42 ? "Unentschieden!" : "";

                if ($_SESSION["message"] === "Unentschieden!") {
                    $_SESSION["restart"] = '<button id="restart" type="submit" name="restart">&#8635</button>';
                    break;
                }
            }

        } else {

            $playerchoice = (int) $_POST['choice'];
            $placemem = placechip($playerchoice);
            $currentplayermem = currentplayerger();
            $_SESSION["message"] = $placemem[0] ? "" : "Spalte ist schon voll!";

            if (!($_SESSION["message"] === "")) {
                return;
            }

            $_SESSION["playcounter"]++;

            $_SESSION["message"] = checkwin($placemem[1], $playerchoice) ? "Spieler "
            . $currentplayermem
            . " hat gewonnen!" : "";

            swapplayer();

            if (
                $_SESSION["message"] === "Spieler "
                . $currentplayermem
                . " hat gewonnen!"
            ) {
                $_SESSION["restart"] = '<button id="restart" type="submit" name="restart">&#8635</button>';
                $_SESSION["winningchip"] = [$placemem[1], $playerchoice];
                return;
            }

            $_SESSION["message"] = $_SESSION["playcounter"] === 42 ? "Unentschieden!" : "";

            if ($_SESSION["message"] === "Unentschieden!") {
                $_SESSION["restart"] = '<button id="restart" type="submit" name="restart">&#8635</button>';
            }

        }
    }
}

function placechip($col){
    for ($row = 6; $row >= 0; $row--) {
        if ($_SESSION["board"][$row][$col] === "white") {
            $_SESSION["board"][$row][$col] = $_SESSION["currentplayer"];
            return [true, $row];
        }
    }
    return false;
}

function checkwin($row, $col){
    for ($row = 0; $row <= 5; $row++) {
        for ($col = 0; $col <= 6; $col++) {
          if (
            $row <= 2 &&
            $_SESSION["board"][$row][$col] === $_SESSION["currentplayer"] &&
            $_SESSION["board"][$row + 1][$col] === $_SESSION["currentplayer"] &&
            $_SESSION["board"][$row + 2][$col] === $_SESSION["currentplayer"] &&
            $_SESSION["board"][$row + 3][$col] === $_SESSION["currentplayer"]
          ) {
            return true;
          } else if (
            $col <= 3 &&
            $_SESSION["board"][$row][$col] === $_SESSION["currentplayer"] &&
            $_SESSION["board"][$row][$col + 1] === $_SESSION["currentplayer"] &&
            $_SESSION["board"][$row][$col + 2] === $_SESSION["currentplayer"] &&
            $_SESSION["board"][$row][$col + 3] === $_SESSION["currentplayer"]
          ) {
            return true;
          } else if (
            $row <= 2 &&
            $col <= 3 &&
            $_SESSION["board"][$row][$col] === $_SESSION["currentplayer"] &&
            $_SESSION["board"][$row + 1][$col + 1] === $_SESSION["currentplayer"] &&
            $_SESSION["board"][$row + 2][$col + 2] === $_SESSION["currentplayer"] &&
            $_SESSION["board"][$row + 3][$col + 3] === $_SESSION["currentplayer"]
          ) {
            return true;
          } else if (
            $row <= 2 &&
            $col <= 3 &&
            $_SESSION["board"][$row][$col + 3] === $_SESSION["currentplayer"] &&
            $_SESSION["board"][$row + 1][$col + 2] === $_SESSION["currentplayer"] &&
            $_SESSION["board"][$row + 2][$col + 1] === $_SESSION["currentplayer"] &&
            $_SESSION["board"][$row + 3][$col] === $_SESSION["currentplayer"]
          ) {
            return true;
          }
        }
      }
    
      return false;
}

function swapplayer(){
    $_SESSION["currentplayer"] = $_SESSION["currentplayer"] === "red" ? "yellow" : "red";
}

function currentplayerger(){
    return $_SESSION["currentplayer"] === "red" ? "Rot" : "Gelb";
}

function botturn(){
    while (true) {
        $mem = random_int(0, 6);
        if ($_SESSION["board"][0][$mem] === "white") {
            return $mem;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4GEWINNT</title>
    <style>
    body {
        all: initial;
    }

    h1,
    p,
    button {
        text-align: center;
        font-family: "Helvetica", sans-serif;
    }

    #board {
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: white;
    }

    form {
        min-width: 350px;
        min-height: 100px;
        background-color: blue;
        border: solid black 5px;
    }

    .chips {
        margin: 10px;
        height: 50px;
        width: 50px;
        border: solid black 3px;
        border-radius: 100%;
    }

    #restart {
        width: 20%;
        height: 50px;
        margin-left: 40%;
        margin-bottom: 2.5%;
        background-color: white;
        border: solid black 3px;
        font-size: 30px;
    }

    .selection {
        width: 100px;
        height: 50px;
        font-size: 20px;
        border: solid black 3px;
        background-color: white;
        margin-top: 25px;
        margin-bottom: 20px;
        margin-left: 50px;
    }

    #menu {
        width: 30%;
        margin-top: 10px;
        margin-left: 35%;
    }
    </style>
</head>

<body>
    <h1>4GEWINNT</h1>
    <div id="board">
        <form method="post">
            <?php
                if (!isset($_SESSION["gamemode"])) {
                    echo '<button class="selection" type="submit" name="selection" value="1">1 Spieler</button>'
                        . '<button class="selection" type="submit" name="selection" value="2">2 Spieler</button>';
                }

                if (isset($_SESSION["gamemode"])) {
                    for ($row = 0; $row < 6; $row++) {
                        for ($col = 0; $col < 7; $col++) {
                            if ($row === 0 && !(isset($_SESSION["winningchip"]))) {
                                echo '<button class="chips" type="submit" name="choice" value="'
                                    . $col
                                    . '" style="background-color:'
                                    . $_SESSION['board'][$row][$col]
                                    . '">'
                                    . '&#8203</button>';
                            } else if (isset($_SESSION["winningchip"]) && $_SESSION["winningchip"][0] === $row && $_SESSION["winningchip"][1] === $col) {
                                echo '<button class="chips" disabled="true" style="background-color:'
                                    . $_SESSION['board'][$row][$col]
                                    . '; border: outset black 7px">'
                                    . '&#8203</button>';
                            } else {
                                echo '<button class="chips" disabled="true" style="background-color:'
                                    . $_SESSION['board'][$row][$col]
                                    . '">'
                                    . '&#8203</button>';
                            }

                        }
                        echo "<br/>";
                    }
                    echo "<br/>"
                        . $_SESSION["restart"];
                    echo '<button class="selection" id="menu" type="submit" name="menu">Men&uuml</button>';
                }
            ?>
        </form>
    </div>
    <p><?=$_SESSION["message"]?></p>
</body>

</html>
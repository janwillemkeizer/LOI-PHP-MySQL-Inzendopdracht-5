<?php
session_start();

include 'connection.php';

function compare($a, $b) {  
    $c = $b['points'] - $a['points'];
    $c .= $b['goal_difference'] - $a['goal_difference'];
    return $c;
}

if (isset($_SESSION['username'])) {
    $user_login = TRUE;
    echo 'Ingelogd als ' . $_SESSION['username'] . 
        ' - <a href="logout.php">Uitloggen</a>' . 
        ' - <a href="formProgram.php">Schema opvoeren</a>';   
} else {
    echo '<p><a href="login.php">Inloggen</a></p>';
    $user_login = FALSE;
}

$scores_query = "SELECT Date, t1.Name, t2.Name, Home_Team_Score, "
            . "Away_Team_Score "
            . "FROM Games JOIN Teams t1 "
            . "ON Games.Home_Team = t1.TeamID "
            ." JOIN Teams t2 "
            . "ON Games.Away_Team = t2.TeamID "
            . "WHERE Games.Home_Team_Score IS NOT NULL "
            . "ORDER BY Date ASC";

$fixtures_query = "SELECT Date, t1.Name, t2.Name, GameID "
            . "FROM Games JOIN Teams t1 "
            . "ON Games.Home_Team = t1.TeamID "
            ." JOIN Teams t2 "
            . "ON Games.Away_Team = t2.TeamID "
            . "WHERE Games.Home_Team_Score IS NULL "
            . "ORDER BY Date ASC";

$team_query = "SELECT Name FROM Teams";
$team_result = mysqli_query($connection, $team_query) or die
(mysqli_error($connection)); 

$teams = mysqli_fetch_all($team_result);

$scores_result = mysqli_query($connection, $scores_query) or die
(mysqli_error($connection));

$fixtures_result = mysqli_query($connection, $fixtures_query) or die
(mysqli_error($connection));

$scores_array = mysqli_fetch_all($scores_result);
$fixtures_array = mysqli_fetch_all($fixtures_result);

mysqli_close($connection);

$ranking = [];

// Add teams to ranking
foreach ($teams as $team) {
    $ranking["$team[0]"] = array("played" => 0, "won" => 0, 
      "drawn" => 0, "lost" => 0, "points" => 0, "goals_for" => 0, 
      "goals_against" => 0, "goal_difference" => 0);
}

// Add results to ranking
foreach ($scores_array as $scores_games) {

// calculate points    
if ($scores_games[3] > $scores_games[4]) {
    $ranking["$scores_games[1]"]["won"]++;
    $ranking["$scores_games[2]"]["lost"]++;
    $ranking["$scores_games[1]"]["points"] = 
            $ranking["$scores_games[1]"]["points"] + 3;
}
elseif ($scores_games[3] === $scores_games[4]) {
$ranking["$scores_games[1]"]["drawn"]++;
$ranking["$scores_games[2]"]["drawn"]++;
$ranking["$scores_games[1]"]["points"]++;
$ranking["$scores_games[2]"]["points"]++;
} else {
    $ranking["$scores_games[2]"]["won"]++;
    $ranking["$scores_games[1]"]["lost"]++;
    $ranking["$scores_games[2]"]["points"] = 
        $ranking["$scores_games[2]"]["points"] + 3;
}
// add other statistics
$ranking["$scores_games[1]"]["goals_for"] = 
        $ranking["$scores_games[1]"]["goals_for"] + 
        $scores_games[3];
$ranking["$scores_games[1]"]["goals_against"] = 
        $ranking["$scores_games[1]"]["goals_against"] + 
        $scores_games[4];
$ranking["$scores_games[2]"]["goals_for"] = 
        $ranking["$scores_games[2]"]["goals_for"] + 
        $scores_games[4];
$ranking["$scores_games[2]"]["goals_against"] = 
        $ranking["$scores_games[2]"]["goals_against"] + 
        $scores_games[3];
$ranking["$scores_games[1]"]["goal_difference"] = 
        $ranking["$scores_games[1]"]["goal_difference"] + 
        ($scores_games[3] - $scores_games[4]);
$ranking["$scores_games[2]"]["goal_difference"] = 
        $ranking["$scores_games[2]"]["goal_difference"] + 
        ($scores_games[4] - $scores_games[3]);
$ranking["$scores_games[1]"]["played"]++;
$ranking["$scores_games[2]"]["played"]++;
}
uasort($ranking, 'compare');
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Inzendopdracht 051R5</title>
        <style type='text/css'>
            table, td, th {
                border: 1px solid black;
                padding: 5px;
            }
            table {
                border-collapse: collapse;
            }
            body {
            background-image: url("football.jpg");
            }
        </style>
    </head>
    <body>

        <h1>Voetbalpoule</h1>

        <h2>Actuele stand</h2>
        
        <?php
        $position = 1;
        if ($ranking) {
        echo '<table>';
        
            echo "<th>#</th>";
            echo "<th>Team</th>"; 
            echo "<th>Gespeeld</th>";
            echo "<th>Gewonnen</th>";
            echo "<th>Gelijk</th>";
            echo "<th>Verloren</th>"; 
            echo "<th>Punten</th>";
            echo "<th>Goals voor</th>";
            echo "<th>Goals tegen</th>";
            echo "<th>Doelsaldo</th>"; 
            echo '<tr>';
            foreach ($ranking as $clubs => $results) {
            echo "<td>$position</td>";
            echo "<td>$clubs</td>";
            foreach ($results as $value) {
                echo "<td>$value</td>";
            }
            echo '</tr>';
            $position++;
        }
            } else {
                echo 'Er is nog geen stand bekend. ';
            }        
        echo '</table>';
        ?>
            
        <h2>Uitslagen</h2>
        
        <?php
        if ($scores_array) {
        foreach ($scores_array as $scores_games) {
         echo date("d-m-Y", strtotime($scores_games[0])) . ': ' 
        . $scores_games[1] . ' - ' . $scores_games[2] . ' ' . $scores_games[3] .
                 ' - ' . $scores_games[4];
         echo '<br>';
         }
        } else {
            echo 'Er zijn nog geen uitslagen bekend.';
        }
        ?>
        
        <h2>Programma</h2>
 
        <?php
        if ($fixtures_array) {
        foreach ($fixtures_array as $scores_games) {
            if($user_login) {
             echo '<a href="formScore.php?' . 'GameID=' . $scores_games[3] . '">' . 
                 date("d-m-Y", strtotime($scores_games[0])) 
              . '</a>: '. $scores_games[1] . ' - ' . $scores_games[2];
            } else {
                echo date("d-m-Y", strtotime($scores_games[0])) . ': ' . 
                        $scores_games[1] . ' - ' . $scores_games[2];
            }
         echo '<br>';
         }
        } else {
            echo 'Er is geen programma.';
        }
        ?>
    </body>
</html>

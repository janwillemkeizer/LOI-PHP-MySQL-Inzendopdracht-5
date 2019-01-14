<?php
include 'connection.php';
session_start();

$submit = filter_input(INPUT_POST, 'Verzend');

// Access $_POST input
$post = filter_input_array(INPUT_POST);

// Clean array up
if (isset($post)) {
    array_pop($post);
}

$query = "SELECT TeamID, Name FROM Teams";
$result = mysqli_query($connection, $query) or die
(mysqli_error($connection)); 

// Create an array with all team names and ID's
$teams = mysqli_fetch_all($result);

function dropdownBox() {
global $teams;

    foreach ($teams as $team)
    {
        echo "<option value = '{$team[0]}'";
        echo ">{$team[1]}</option>";
    }

}

// Count teams
$number_teams_rows = mysqli_num_rows($result);

// Total number games in competition
$number_games = $number_teams_rows * ($number_teams_rows - 1);

// Calculate number rounds in whole competition
$number_rounds = ($number_teams_rows - 1) * 2; 

// Number of games per round
$number_games_round = $number_teams_rows / 2;

// Collect game dates in array
$game_dates = [];
for ($round = 1, $game_date = "2019-01-06"; $round <= $number_rounds; $round++,
        $game_date = date("Y-m-d", strtotime("+7 day", 
        strtotime($game_date)))) {  
    $game_dates[] = $game_date;    
}

$sql_values = '';
$round_counter_sql = 0;
$games_counter_sql = 1;

// If form is submitted add games to database and redirect to homepage
if (isset($submit) && $submit === 'Verzend') {
    
// Build SQL query
foreach($post as $rounds) {
    foreach($rounds as $games) {
        $sql_values .= '(';
        $sql_values .= '\'' . $game_dates[$round_counter_sql] . '\', ';
            foreach($games as $key => $value) {
                    $sql_values .= '\'' . $value . '\'';
                    
                    if (next($games) == true) { 
                        $sql_values .= ", ";
                        } 
                }
        $sql_values .= ')';
            if ($games_counter_sql < $number_games) { 
                $sql_values .= ", ";
                }
        $games_counter_sql++;
            }
        $round_counter_sql++;
    }
    
$sql_insert = "INSERT INTO Games (Date, Home_Team, Away_Team)
VALUES ";
$sql_insert .= $sql_values;

if (mysqli_query($connection, $sql_insert)) { 
    header('Location: index.php');
    } else {
        echo "Error: " . $sql_insert . "<br>" . mysqli_error($connection);
    }
    mysqli_close($connection);

}

if (isset($_SESSION['username'])) {
    echo '<div class="box">';
    echo 'Ingelogd als ' . $_SESSION['username'] . 
        ' - <a href="logout.php">Uitloggen</a>' .
            ' - <a href="index.php">Ga terug naar de homepage</a>.';
    echo '</div>';
} else {
    header('Location: index.php');
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Inzendopdracht 051R5</title>
        <style type='text/css'>
            body {
            background-image: url("pitch.jpg");
            }
            .box {
                background-color: white;
                width: 30%;
                padding-left: 30px;
                padding-top: 1px;
                padding-bottom: 10px;
            }
        </style>
    </head>
    <body>
        <div class="box">
        <h1>Wedstrijdschema</h1>
        <form action="formProgram.php" method="POST">
    
        <?php
        if ($number_teams_rows % 2 === 0) {
       
        $game_counter = 1;
        
        for ($round = 1; $round <= $number_rounds; 
        $round++) {
                
            echo "<h1>Ronde $round</h1>";
                
            for ($for_number_games = 1; 
            $for_number_games <= $number_games_round; 
            $for_number_games++) {

            echo date("d-m-Y", strtotime($game_dates[$round - 1])) . " ";

            echo '<select name="' . $round . '[' . $game_counter . ']'
                    . '[home_team]">';
            dropdownBox();
            echo '</select>';
            echo ' - ';
            echo '<select name="' . $round . '[' . $game_counter . ']'
                    . '[away_team]">';
            dropdownBox();
            echo '</select>';
            echo '<br>';
            $game_counter++;
            }
        }
        ?>
        <br /><input type="submit" name="Verzend" value="Verzend">   
        </form>
        </div>
    
        <?php
        } else {
            echo '<h2>Niet beschikbaar</h2>';
            echo '<p>Deze competitie kan alleen bestaan met een even aantal teams. ' . 
        'Zorg er alsjeblieft voor dat dit wordt ingevoerd in de database.</p>';
        }
        ?> 
        
    </body>
</html>
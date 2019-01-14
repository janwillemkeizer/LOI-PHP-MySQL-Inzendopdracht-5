<?php
session_start();

include 'connection.php';

$game_id = filter_input(INPUT_GET, 'GameID');

$home_score = filter_input(INPUT_POST, 'home_score');
$away_score = filter_input(INPUT_POST, 'away_score');
$game_id_post = filter_input(INPUT_POST, 'game_id_post');
$submit = filter_input(INPUT_POST, 'Verzend');

if (isset($submit) && $submit === 'Verzend') {
    $update_query = "UPDATE Games SET Home_Team_Score = '$home_score', "
            . "Away_Team_Score = '$away_score' "
            . "WHERE GameID = '$game_id_post'";
    mysqli_query($connection, $update_query)
    or die(mysqli_error($connection));
    mysqli_close($connection);
    header('Location: index.php');
   
}

if (isset($_SESSION['username'])) {
      
    $select_query = "SELECT Date, t1.Name, t2.Name FROM Games "
            . "JOIN Teams t1 "
            . "ON Games.Home_Team = t1.TeamID "
            . "JOIN Teams t2 "
            . "ON Games.Away_Team = t2.TeamID "
            . "WHERE GameID = '$game_id'";
    
    $result = mysqli_query($connection, $select_query) 
        or die(mysqli_error($connection));
        mysqli_close($connection);
    
    $selected_game = mysqli_fetch_row($result);
       
} else {
    echo '<h1>Invullen uitslag</h1>';
    echo '<p>Om een stand in te kunnen vullen moet je ingelogd zijn.</p>';
    echo '<a href="login.php">Log in</a>.';
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Inzendopdracht 051R5></title>
    <style>
        #element {
            display: inline-block;
        } 
        input[type="number"] {
           width:50px;
           font-size: large;
        }
        body {
           background-image: url("football.jpg");
        }
</style>
    </head>
    <?php if (isset($_SESSION['username'])) { ?>
    <body>
        <h1>Invullen uitslag</h1>
       
        <form method="post" action="formScore.php">
        
            <div id="element"><h3><?php echo $selected_game[1]?></h3></div>
            <div id="element"><input type="number" min="0" max="10" value="0" name="home_score"></div>
            <div id="element"><input type="number" min="0"max="10" value="0" name="away_score"></div>
            <div id="element"><h3><?php echo $selected_game[2]?></h3></div>
            <input type="hidden" name="game_id_post" value="<?php echo $game_id; ?>">
            <input type="submit" name="Verzend" value="Verzend">
        </form>
        <?php } echo "<p>Gespeeld op " . date("d-m-Y", strtotime($selected_game[0])) . "</p>"?>
        <a href="index.php">Ga terug naar de homepage</a>.
    </body>
</html>


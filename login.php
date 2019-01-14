<?php
include 'config/connection.php';

session_start();

$username = filter_input(INPUT_POST, 'username');
$password = filter_input(INPUT_POST, 'password');

if (isset($username) && isset($password)) {
$passwordmd5 = md5($password);

$query = "SELECT * FROM `Users` WHERE username='$username' "
        . "AND password='$passwordmd5'";

$result = mysqli_query($connection, $query) 
        or die(mysqli_error($connection));
        mysqli_close($connection);

$count = mysqli_num_rows($result);

if ($count === 1){
$_SESSION['username'] = $username;
header('Location: index.php');
} else {
echo "Deze inloggegevens zijn ongeldig. Probeer het alsjeblieft opnieuw.";
}
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Inzendopdracht 051R5</title>
         <style type='text/css'>
            body {
            background-image: url("football.jpg");
            }
        </style>
    </head>
    <body>
        <h1>Inloggen</h1>
        
        <form method="post" action="login.php">

            Gebruikersnaam: <input type="text" size="30" name="username" 
             value="<?php if(isset($username)) echo $username; ?>" > <br>

            Wachtwoord: <input type="password" size="30" name="password"
            value=""> <br>
            <input type="submit" name="submit" value="Verzend">
            
        </form>
        
        <p>Nog geen account?</p>
        <a href="register.php">Registreer</a> of  
        <a href="index.php">ga terug naar de homepage</a>. 

    </body>
</html>
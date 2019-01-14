<?php

include 'connection.php';

function emailcheck($email) {
    if (preg_match('#[a-zA-Z]{2,}@[a-zA-Z]{2,}\.nl#', $email))
  {
    return TRUE;
  } else {
    return FALSE;
  }  
}

$message = '';
$username_message = '';
$password_message = '';
$email_message = '';

$submit = filter_input(INPUT_POST, 'submit');

$error = TRUE;

if (isset($submit) && $submit === 'Verzend')
{
$username = filter_input(INPUT_POST, 'username');
$password = filter_input(INPUT_POST, 'password');
$email = filter_input(INPUT_POST, 'email');

$error = FALSE;

if(!isset($username) || strlen(trim($username)) < 3) {
$username_message = "De gebruikersnaam moet minimaal uit drie tekens bestaan.";    
$error = TRUE;    
}
if(!isset($password) || strlen(trim($password)) < 8) {
$password_message = "Het wachtwoord moet minimaal 8 tekens bevatten.";    
$error = TRUE;    
}
if(!isset($email) || !emailcheck($email)) {
$email_message = "Vul alsjeblieft een correct e-mailadres in dat eindigt op '.nl'.";    
$error = TRUE;    
} 
}

if(!$error) {
$query = "INSERT INTO Users (Username, Password, Email)
          VALUES ('$username',md5('$password'),'$email')";
$result = mysqli_query($connection, $query);

    if (mysqli_affected_rows($connection) === 1) {
    $message = '<h3>Succes! Je bent toegevoegd als gebruiker.</h3>' . 
       '<a href="login.php">Ga naar inloggen</a>';
    $username = "";
    $password = "";
    $email = "";
    } else {
    error_log(mysqli_error($connection));
    $message = '<p>Het is niet gelukt om je als gebruiker te registreren.'
            . ' Probeer een andere gebruikersnaam in te voeren. </p>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inzendopdracht 051R5</title>
     <style type='text/css'>
            body {
            background-image: url("football.jpg");
            }
        </style>
</head>

<body>
    <h1>Registreren</h1>
    <p>Vul hieronder je gewenste gebruikersnaam, wachtwoord en e-mailadres in.</p>
    
    <form method="post" action="register.php">
        
        Gebruikersnaam: <input type="text" size="30" name="username" 
        value="<?php if(isset($username)) echo $username; ?>" ><?php echo 
        $username_message;?><br>

        Wachtwoord: <input type="password" size="30" name="password" 
        value="<?php if(isset($password)) echo $password; ?>" ><?php echo 
        $password_message;?> <br>

        E-mail: <input type="text" size="30" name="email" 
        value="<?php if(isset($email)) echo $email; ?>" > <?php echo 
        $email_message;?><br><br>
        <input type="submit" name="submit" value="Verzend">
        
    </form>
    <?php echo $message; ?>
    <p><a href="index.php">ga terug naar de homepage</a>.</p>
</body>
</html> 
<?php
    session_start();

    $email = $_POST["email"];
    $password = $_POST["password"];

    $connection = new PDO('pgsql:host=localhost port=5432 dbname=bonus user=adilet password=');

    $statement = $connection->prepare("SELECT * FROM people WHERE email = :email AND password = :password;");
    $statement->bindParam(':email', $email, PDO::PARAM_STR, 50);
    $statement->bindParam(':password', hash('sha256', $password), PDO::PARAM_STR, 64);
    $statement->execute();

    if ($statement->rowCount() > 0) {
        $_SESSION['email'] = $email;
        $_SESSION['type'] = 'person';

        header('Location: /account.php');
    } else {
        header('Location: /sign_in.php?tab=person&title=Error&content=Wrong email or password.');
    }
?>
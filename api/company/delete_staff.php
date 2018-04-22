<?php
    session_start();

    $email = $_POST["email"];

    $connection = new PDO('pgsql:host=localhost port=5432 dbname=bonus');

    $statement = $connection->prepare("UPDATE people SET company_id = null WHERE email = :email;");
    $statement->bindParam(':email', $email, PDO::PARAM_STR, 50);

    if ($statement->execute()) {
        header('Location: /account.php');
    } else {
        header('Location: /account.php?title=Error&content=Something went horribly wrong while trying to delete staff. Try again.');
    }
?>
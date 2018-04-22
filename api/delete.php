<?php
    session_start();

    $connection = new PDO('pgsql:host=localhost port=5432 dbname=bonus');

    if ($_SESSION['type'] == 'person') {
        $statement = $connection->prepare("DELETE FROM people WHERE email = :email;");
    } else if ($_SESSION['type'] == 'company') {
        $statement = $connection->prepare("DELETE FROM companies WHERE email = :email;");
    }
    $statement->bindParam(':email', $_SESSION['email'], PDO::PARAM_STR, 50);

    if ($statement->execute()) {
        header('Location: /api/sign_out.php');
    } else {
        header("Location: /account.php?title=Error&content=For some reason we can't delete your account. Try again.");
    }
?>
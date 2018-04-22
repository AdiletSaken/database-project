<?php
    session_start();

    $full = $_POST['full'];
    $mixed = $_POST['mixed'];

    $connection = new PDO('pgsql:host=localhost port=5432 dbname=bonus');

    $statement = $connection->prepare("SELECT * FROM companies WHERE email = :email;");
    $statement->bindParam(':email', $_SESSION['email'], PDO::PARAM_STR, 50);
    $statement->execute();

    if ($statement->rowCount() == 1) {
        $statement = $connection->prepare("UPDATE companies SET percentage_full = :percentage_full, percentage_mixed = :percentage_mixed WHERE email = :email;");
        $statement->bindParam(':percentage_full', $full, PDO::PARAM_STR, 6);
        $statement->bindParam(':percentage_mixed', $mixed, PDO::PARAM_STR, 6);
        $statement->bindParam(':email', $_SESSION['email'], PDO::PARAM_STR, 50);
        
        if ($statement->execute()) {
            header('Location: /account.php');
        } else {
            header('Location: /account.php?title=Error&content=Something went horribly wrong. Try again.');
        }
    } else {
        header('Location: /api/sign_out.php');
    }
?>
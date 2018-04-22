<?php
    session_start();
    
    $email = $_POST["email"];

    $connection = new PDO('pgsql:host=localhost port=5432 dbname=bonus');

    $statement = $connection->prepare("SELECT * FROM people WHERE email = :email;");
    $statement->bindParam(':email', $email, PDO::PARAM_STR, 50);
    $statement->execute();

    if ($statement->rowCount() == 1) {
        $statement = $connection->prepare("UPDATE people SET company_id = :company_id WHERE email = :email;");
        $statement->bindParam(':email', $email, PDO::PARAM_STR, 50);
        $statement->bindParam(':company_id', $_SESSION['company_id'], PDO::PARAM_STR);
        
        if ($statement->execute()) {
            header('Location: /account.php');
        } else {
            header("Location: /account.php?title=Error&content=Something went horribly wrong while adding staff. Try again.");
        }
    } else {
        header("Location: /account.php?title=Error&content=There is no such person with $email.");
    }
?>
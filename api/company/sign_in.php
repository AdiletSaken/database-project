<?php
    session_start();
    
    $email = $_POST["email"];
    $password = $_POST["password"];

    $connection = new PDO('pgsql:host=localhost port=5432 dbname=bonus');

    $statement = $connection->prepare("SELECT * FROM companies WHERE email = :email AND password = :password;");
    $statement->bindParam(':email', $email, PDO::PARAM_STR, 50);
    $statement->bindParam(':password', hash('sha256', $password), PDO::PARAM_STR, 64);
    $statement->execute();

    if ($statement->rowCount() == 1) {
        $_SESSION['email'] = $email;
        $_SESSION['type'] = 'company';

        $row = $statement->fetch();
        $_SESSION['company_id'] = $row['id'];

        header('Location: /account.php');
    } else {
        header('Location: /sign_in.php?tab=company&title=Error&content=Wrong email or password.');
    }
?>
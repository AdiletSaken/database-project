<?php
    session_start();

    function editAccountWithoutPassword($connection, $name, $email) {
        $statement = $connection->prepare("UPDATE companies SET name = :name, email = :new_email WHERE email = :email;");
        $statement->bindParam(':name', $name, PDO::PARAM_STR, 50);
        $statement->bindParam(':new_email', $email, PDO::PARAM_STR, 50);
        $statement->bindParam(':email', $_SESSION['email'], PDO::PARAM_STR, 50);

        if ($statement->execute()) {
            $_SESSION['email'] = $email;
            $_SESSION['type'] = 'company';

            header('Location: /account.php');
        } else {
            header('Location: /account.php?title=Error&content=Something went horribly wrong. Try again.');
        }
    }

    function editAccount($connection, $name, $email, $password) {
        if ($password != '') {
            $statement = $connection->prepare("UPDATE companies SET password = :password WHERE email = :email;");
            $statement->bindParam(':password', hash('sha256', $password), PDO::PARAM_STR, 64);
            $statement->bindParam(':email', $_SESSION['email'], PDO::PARAM_STR, 50);

            if ($statement->execute()) {
                editAccountWithoutPassword($connection, $name, $email);
            } else {
                header('Location: /account.php?title=Error&content=Something went horribly wrong when changing your password. Try again.');
            }
        } else {
            editAccountWithoutPassword($connection, $name, $email);
        }
    }

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $connection = new PDO('pgsql:host=localhost port=5432 dbname=bonus');

    if ($_SESSION['email'] != $email) {
        $statement = $connection->prepare("SELECT * FROM companies WHERE email = :email;");
        $statement->bindParam(':email', $email, PDO::PARAM_STR, 50);
        $statement->execute();

        if ($statement->rowCount() == 0) {
            editAccount($connection, $name, $email, $password);
        } else {
            header("Location: /account.php?title=Error&content=There is already a company registered with $email.");
        }
    } else {
        editAccount($connection, $name, $email, $password);
    }
?>
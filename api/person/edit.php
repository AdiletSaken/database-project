<?php
    session_start();

    function editAccountWithoutPassword($connection, $first_name, $last_name, $phone, $birthday, $email) {
        $statement = $connection->prepare("UPDATE people SET first_name = :first_name, last_name = :last_name, phone = :phone, birthday = :birthday, email = :new_email WHERE email = :email;");
        $statement->bindParam(':first_name', $first_name, PDO::PARAM_STR, 50);
        $statement->bindParam(':last_name', $last_name, PDO::PARAM_STR, 50);
        $statement->bindParam(':phone', $phone, PDO::PARAM_STR, 13);
        $statement->bindParam(':birthday', $birthday, PDO::PARAM_STR);
        $statement->bindParam(':new_email', $email, PDO::PARAM_STR, 50);
        $statement->bindParam(':email', $_SESSION['email'], PDO::PARAM_STR, 50);

        if ($statement->execute()) {
            $_SESSION['email'] = $email;
            $_SESSION['type'] = 'person';

            header('Location: /account.php');
        } else {
            header('Location: /account.php?title=Error&content=Something went horribly wrong. Try again.');
        }
    }

    function editAccount($connection, $first_name, $last_name, $phone, $birthday, $email, $password) {
        if ($password != '') {
            $statement = $connection->prepare("UPDATE people SET password = :password WHERE email = :email;");
            $statement->bindParam(':password', hash('sha256', $password), PDO::PARAM_STR, 64);
            $statement->bindParam(':email', $_SESSION['email'], PDO::PARAM_STR, 50);

            if ($statement->execute()) {
                editAccountWithoutPassword($connection, $first_name, $last_name, $phone, $birthday, $email);
            } else {
                header('Location: /account.php?title=Error&content=Something went horribly wrong when changing your password. Try again.');
            }
        } else {
            editAccountWithoutPassword($connection, $first_name, $last_name, $phone, $birthday, $email);
        }
    }

    $first_name = $_POST['first-name'];
    $last_name = $_POST['last-name'];
    $phone = $_POST['phone'];
    $birthday = $_POST['birthday'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $connection = new PDO('pgsql:host=localhost port=5432 dbname=bonus');

    if ($_SESSION['email'] != $email) {
        $statement = $connection->prepare("SELECT * FROM people WHERE email = :email;");
        $statement->bindParam(':email', $email, PDO::PARAM_STR, 50);
        $statement->execute();

        if ($statement->rowCount() == 0) {
            editAccount($connection, $first_name, $last_name, $phone, $birthday, $email, $password);
        } else {
            header("Location: /account.php?title=Error&content=There is already a user registered with $email.");
        }
    } else {
        editAccount($connection, $first_name, $last_name, $phone, $birthday, $email, $password);
    }
?>
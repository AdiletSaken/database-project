<?php
    session_start();

    function generateRandomCode() { 
        $chars = 'abcdefghijkmnopqrstuvwxyz0123456789';
        $code = '';

        while (strlen($code) < 8) {
            $num = rand() % 36;
            $char = substr($chars, $num, 1);
            $code = $code . $char;
        }

        return strtoupper($code);
    }
    
    $first_name = $_POST['first-name'];
    $last_name = $_POST['last-name'];
    $phone = $_POST['phone'];
    $birthday = $_POST['birthday'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $connection = new PDO('pgsql:host=localhost port=5432 dbname=bonus');

    $statement = $connection->prepare("SELECT * FROM people WHERE email = :email;");
    $statement->bindParam(':email', $email, PDO::PARAM_STR, 50);
    $statement->execute();

    if ($statement->rowCount() == 0) {
        $code = generateRandomCode();

        $statement = $connection->prepare("SELECT * FROM people WHERE code = '$code';");
        $statement->execute();

        while ($statement->rowCount() != 0) {
            $code = generateRandomCode();

            $statement = $connection->prepare("SELECT * FROM people WHERE code = '$code';");
            $statement->execute();
        }

        $statement = $connection->prepare("INSERT INTO people VALUES (NEXTVAL('people_id_seq'), :first_name, :last_name, :phone, :birthday, '$code', :email, :password);");
        $statement->bindParam(':first_name', $first_name, PDO::PARAM_STR, 50);
        $statement->bindParam(':last_name', $last_name, PDO::PARAM_STR, 50);
        $statement->bindParam(':phone', $phone, PDO::PARAM_STR, 13);
        $statement->bindParam(':birthday', $birthday, PDO::PARAM_STR);
        $statement->bindParam(':email', $email, PDO::PARAM_STR, 50);
        $statement->bindParam(':password', hash('sha256', $password), PDO::PARAM_STR, 64);

        if ($statement->execute()) {
            $_SESSION['email'] = $email;
            $_SESSION['type'] = 'person';

            header('Location: /account.php');
        } else {
            header('Location: /sign_up.php?tab=person&title=Error&content=Something went horribly wrong. Try again.');
        }
    } else {
        header("Location: /sign_up.php?tab=person&title=Error&content=There is already a user registered with $email.");
    }
?>
<?php
    session_start();

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $connection = new PDO('pgsql:host=localhost port=5432 dbname=bonus');

    $statement = $connection->prepare("SELECT * FROM companies WHERE email = :email;");
    $statement->bindParam(':email', $email, PDO::PARAM_STR, 50);
    $statement->execute();

    if ($statement->rowCount() == 0) {
        $statement = $connection->prepare("INSERT INTO companies VALUES (NEXTVAL('companies_id_seq'), :name, 10.00, 5.00, :email, :password);");
        $statement->bindParam(':name', $name, PDO::PARAM_STR, 50);
        $statement->bindParam(':email', $email, PDO::PARAM_STR, 50);
        $statement->bindParam(':password', hash('sha256', $password), PDO::PARAM_STR, 64);

        if ($statement->execute()) {
            $_SESSION['email'] = $email;
            $_SESSION['type'] = 'company';

            $statement = $connection->prepare("SELECT * FROM companies WHERE email = :email;");
            $statement->bindParam(':email', $email, PDO::PARAM_STR, 50);
            $statement->execute();

            $row = $statement->fetch();
            $_SESSION['company_id'] = $row['id'];

            header('Location: /account.php');
        } else {
            header('Location: /sign_up.php?tab=company&title=Error&content=Something went horribly wrong. Try again.');
        }
    } else {
        header("Location: /sign_up.php?tab=company&title=Error&content=There is already a company registered with $email.");
    }
?>
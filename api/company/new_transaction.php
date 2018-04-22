<?php
    session_start();

    function update_balance($connection, $user, $new_balance) {
        $statement = $connection->prepare("UPDATE company_users SET balance = :balance WHERE user_id = :user_id AND company_id = :company_id;");
        $statement->bindParam(':balance', $new_balance, PDO::PARAM_STR);
        $statement->bindParam(':user_id', $user['user_id'], PDO::PARAM_STR);
        $statement->bindParam(':company_id', $user['company_id'], PDO::PARAM_STR);
        $statement->execute();
    }


    function add_transaction($connection, $user_id, $company_id, $amount, $used, $added) {
        $statement = $connection->prepare("INSERT INTO transactions VALUES (NEXTVAL('transactions_id_seq'), :user_id, :company_id, :amount, :used, :added);");
        $statement->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        $statement->bindParam(':company_id', $company_id, PDO::PARAM_STR);
        $statement->bindParam(':amount', $amount, PDO::PARAM_STR);
        $statement->bindParam(':used', $used, PDO::PARAM_STR);
        $statement->bindParam(':added', $added, PDO::PARAM_STR);
        $statement->execute();
    }
    
    $promo_code = $_POST['promo-code'];
    $price = $_POST['price'];
    $bonus = $_POST['bonus'];

    $connection = new PDO('pgsql:host=localhost port=5432 dbname=bonus');

    $statement = $connection->prepare("SELECT * FROM people WHERE code = :code;");
    $statement->bindParam(':code', $promo_code, PDO::PARAM_STR, 8);
    $statement->execute();

    if ($statement->rowCount() == 1) {
        $person = $statement->fetch();

        $statement = $connection->prepare("SELECT * FROM companies WHERE id = :company_id;");
        $statement->bindParam(':company_id', $_SESSION['company_id'], PDO::PARAM_STR);
        $statement->execute();

        if ($statement->rowCount() == 1) {
            $company = $statement->fetch();

            $statement = $connection->prepare("SELECT * FROM company_users WHERE user_id = :user_id AND company_id = :company_id;");
            $statement->bindParam(':user_id', $person['id'], PDO::PARAM_STR);
            $statement->bindParam(':company_id', $company['id'], PDO::PARAM_STR);
            $statement->execute();

            if ($statement->rowCount() == 1) {
                $user = $statement->fetch();

                if ($bonus == '') {
                    $new_bonus = round($company['percentage_full'] / 100 * $price, 2);

                    update_balance($connection, $user, $user['balance'] + $new_bonus);

                    add_transaction($connection, $person['id'], $company['id'], $price, 0, $new_bonus);

                    header('Location: /transaction.php');
                } else {
                    if ($user['balance'] >= $bonus) {
                        echo 'test';
                        $new_bonus = round($company['percentage_mixed'] / 100 * $price, 2);

                        update_balance($connection, $user, $user['balance'] - $bonus + $new_bonus);

                        add_transaction($connection, $person['id'], $company['id'], $price, $bonus, $new_bonus);

                        header('Location: /transaction.php');
                    } else {
                        header("Location: /transaction.php?title=Error&content=User doesn't have enough funds.");
                    }
                }
            } else {
                if ($bonus == '') {
                    $new_bonus = round($company['percentage_full'] / 100 * $price, 2);

                    $statement = $connection->prepare("INSERT INTO company_users VALUES (NEXTVAL('company_users_id_seq'), :user_id, :company_id, :balance);");
                    $statement->bindParam(':user_id', $person['id'], PDO::PARAM_STR);
                    $statement->bindParam(':company_id', $company['id'], PDO::PARAM_STR);
                    $statement->bindParam(':balance', $new_bonus, PDO::PARAM_STR);
                    $statement->execute();

                    add_transaction($connection, $person['id'], $company['id'], $price, 0, $new_bonus);

                    header('Location: /transaction.php');
                } else {
                    header("Location: /transaction.php?title=Error&content=User doesn't have enough funds.");
                }
            }
        } else {
            header('Location: /api/sign_out.php');
        }
    } else {
        header("Location: /transaction.php?title=Error&content=There is no such person with $promo_code promo code.");
    }
?>
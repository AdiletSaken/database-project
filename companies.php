<?php
    session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Companies</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
    </head>
    <body>
        <?php
            $page = "companies";
            include "navbar.php";

            $connection = new PDO('pgsql:host=localhost port=5432 dbname=bonus');
            $statement = $connection->prepare("SELECT * FROM companies");
            $statement->execute();

            $companies = $statement->fetchAll();

            $users = array();

            foreach ($companies as $key => $value) {
                $statement = $connection->prepare("SELECT * FROM company_users WHERE company_id = :company_id;");
                $statement->bindParam(':company_id', $value['id'], PDO::PARAM_STR);
                $statement->execute();

                array_push($users, $statement->rowCount());
            }
        ?>
        <div class="container p-3">
            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-8 col-xl-6">
                    <ul class="list-group">
                        <?php foreach ($companies as $key => $value) { ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo $value['name']; ?>
                                <span class="badge badge-<?php if ($users[$key] > 0) { echo 'success'; } else { echo 'danger'; } ?> badge-pill"><?php echo $users[$key] ?> user<?php if ($users[$key] != 1) { echo 's'; } ?></span>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
    </body>
</html>
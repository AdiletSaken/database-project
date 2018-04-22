<?php
    session_start();

    if (!isset($_SESSION['email']) || !isset($_SESSION['type'])) {
        header('Location: /index.php');
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Account</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
    </head>
    <body>
        <?php
            $page = "account";
            include "navbar.php";

            $connection = new PDO('pgsql:host=localhost port=5432 dbname=bonus');

            if ($_SESSION['type'] == 'person') {
                $statement = $connection->prepare("SELECT * FROM people WHERE email = :email;");
            } else {
                $statement = $connection->prepare("SELECT * FROM companies WHERE email = :email;");
            }
            $statement->bindParam(':email', $_SESSION['email'], PDO::PARAM_STR, 50);
            $statement->execute();

            if ($statement->rowCount() == 1) {
                $account = $statement->fetch();
            } else {
                header('Location: api/sign_out.php');
            }
        ?>
        <div class="container p-3">
            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-8 col-xl-6">
                    <?php if ($_SESSION['type'] == 'person') { ?>
                        <div class="alert alert-success" role="alert" style="box-sizing: border-box;">
                            <h4 class="alert-heading">Promo code:</h4>
                            <p>Your promo code is <b><?php echo $account['code']; ?></b>.</p>
                        </div>
                        <p>
                            <button class="btn btn-primary btn-block" type="button" data-toggle="collapse" data-target="#editAccount" aria-expanded="false" aria-controls="edit">Edit account</button>
                        </p>
                        <div class="collapse" id="editAccount">
                            <div class="card card-body">
                                <form action="api/person/edit.php" method="POST">
                                    <div class="row form-group">
                                        <label for="first-name" class="col-md-3 col-form-label">First name</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" id="first-name" name="first-name" placeholder="First name" value="<?php echo $account['first_name']; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="last-name" class="col-md-3 col-form-label">Last name</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" id="last-name" name="last-name" placeholder="Last name" value="<?php echo $account['last_name']; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="phone" class="col-md-3 col-form-label">Phone</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone" value="<?php echo $account['phone']; ?>">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="birthday" class="col-md-3 col-form-label">Birthday</label>
                                        <div class="col-md-9">
                                            <input type="date" class="form-control" id="birthday" name="birthday" placeholder="Birthday" value="<?php echo $account['birthday']; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="email" class="col-md-3 col-form-label">Email</label>
                                        <div class="col-md-9">
                                            <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?php echo $account['email']; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="password" class="col-md-3 col-form-label">Password</label>
                                        <div class="col-md-9">
                                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" value="********">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-0">
                                        <div class="col">
                                            <button type="submit" class="btn btn-primary btn-block">Save changes</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">
                            <?php
                                echo htmlspecialchars($_GET['title']);
                            ?>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?php
                            echo htmlspecialchars($_GET['content']);
                        ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
        <script src="scripts/script.js"></script>
    </body>
</html>
<?php
    session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>New transaction</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
    </head>
    <body>
        <?php
            $connection = new PDO('pgsql:host=localhost port=5432 dbname=bonus');

            if ($_SESSION['type'] == 'person') {
                $statement = $connection->prepare("SELECT * FROM people WHERE email = :email;");
            } else if ($_SESSION['type'] == 'company') {
                $statement = $connection->prepare("SELECT * FROM companies WHERE email = :email;");
            }
            $statement->bindParam(':email', $_SESSION['email'], PDO::PARAM_STR, 50);
            $statement->execute();

            if ($statement->rowCount() == 1) {
                $account = $statement->fetch();
            } else {
                header('Location: /api/sign_out.php');
            }

            $statement = $connection->prepare("SELECT * FROM companies WHERE id = :company_id;");
            $statement->bindParam(':company_id', $_SESSION['company_id'], PDO::PARAM_STR);
            $statement->execute();

            if ($statement->rowCount() == 1) {
                $company = $statement->fetch();
            } else {
                header('Location: /api/sign_out.php');
            }

            $page = "transaction";
            include "navbar.php";
        ?>
        <div class="container p-3">
            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-8 col-xl-6">
                    <div class="alert alert-primary" role="alert">
                        <h4 class="alert-heading">New transaction:</h4>
                        <p class="mb-0">Currently working for company <b><?php echo $company['name']; ?></b>.</p>
                    </div>
                    <form action="/api/company/new_transaction.php" method="POST">
                        <div class="row form-group">
                            <label for="promo-code" class="col-md-3 col-form-label">Promo code</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" id="promo-code" name="promo-code" placeholder="Promo code">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="price" class="col-md-3 col-form-label">Price</label>
                            <div class="col-md-9">
                                <input type="number" step="0.01" class="form-control" id="price" name="price" placeholder="Price">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="bonus" class="col-md-3 col-form-label">Bonus</label>
                            <div class="col-md-9">
                                <input type="number" class="form-control" id="bonus" name="bonus" placeholder="Bonus">
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col">
                                <button type="submit" class="btn btn-success btn-block">Create transaction</button>
                            </div>
                        </div>
                    </form>
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
        <script src="/scripts/script.js"></script>
    </body>
</html>
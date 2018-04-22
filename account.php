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

            if ($_SESSION['type'] == 'company') {
                $statement = $connection->prepare("SELECT * FROM people WHERE company_id = :company_id;");
                $statement->bindParam(':company_id', $account['id'], PDO::PARAM_STR);
                $statement->execute();
    
                $staff = array();

                while ($row = $statement->fetch()) {
                    array_push($staff, $row);
                }
            }

            $page = "account";
            include "navbar.php";
        ?>
        <div class="container p-3">
            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-8 col-xl-6">
                    <?php if ($_SESSION['type'] == 'person') { ?>
                        <div class="alert alert-success" role="alert">
                            <h4 class="alert-heading">Promo code:</h4>
                            <p class="mb-0">Your promo code is <b><?php echo $account['code']; ?></b>.</p>
                        </div>
                        <p>
                            <button class="btn btn-primary btn-block" type="button" data-toggle="collapse" data-target="#editAccount" aria-expanded="false" aria-controls="edit">Edit account</button>
                        </p>
                        <div class="collapse" id="editAccount">
                            <div class="card card-body mb-3">
                                <form action="/api/person/edit.php" method="POST">
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
                                            <input type="password" class="form-control" id="password" name="password" placeholder="New password">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-0">
                                        <div class="col">
                                            <button type="submit" class="btn btn-success btn-block">Save changes</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <form action="/api/delete.php" method="POST">
                            <button class="btn btn-danger btn-block" type="submit">Delete account</button>
                        </form>
                    <?php } else if ($_SESSION['type'] == 'company') { ?>
                        <div class="alert alert-primary text-center" role="alert">
                            <h3 class="alert-heading mb-0">
                                <?php
                                    echo $account['name'];
                                ?>
                            </h3>
                        </div>
                        <p>
                            <button class="btn btn-primary btn-block" type="button" data-toggle="collapse" data-target="#editAccount" aria-expanded="false" aria-controls="edit">Edit account</button>
                        </p>
                        <div class="collapse" id="editAccount">
                            <div class="card card-body mb-3">
                                <form action="/api/company/edit.php" method="POST">
                                    <div class="row form-group">
                                        <label for="name" class="col-md-3 col-form-label">Name</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Name" value="<?php echo $account['name']; ?>">
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
                                            <input type="password" class="form-control" id="password" name="password" placeholder="New password">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-0">
                                        <div class="col">
                                            <button type="submit" class="btn btn-success btn-block">Save changes</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <p>
                            <button class="btn btn-primary btn-block" type="button" data-toggle="collapse" data-target="#editPromo" aria-expanded="false" aria-controls="edit">Edit promo</button>
                        </p>
                        <div class="collapse" id="editPromo">
                            <div class="card card-body mb-3">
                                <form action="/api/company/edit_promo.php" method="POST">
                                    <div class="form-group row">
                                        <label for="full" class="col-md-4 col-form-label">Full percentage</label>
                                        <div class="col-md-8">
                                            <input type="number"step="0.01" class="form-control" id="full" name="full" placeholder="Full percentage" value="<?php echo $account['percentage_full']; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="mixed" class="col-md-4 col-form-label">Mixed percentage</label>
                                        <div class="col-md-8">
                                            <input type="number" step="0.01" class="form-control" id="mixed" name="mixed" placeholder="Mixed percentage" value="<?php echo $account['percentage_mixed']; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-0">
                                        <div class="col">
                                            <button type="submit" class="btn btn-success btn-block">Save changes</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <p>
                            <button class="btn btn-primary btn-block" type="button" data-toggle="collapse" data-target="#editStaff" aria-expanded="false" aria-controls="edit">Edit staff</button>
                        </p>
                        <div class="collapse" id="editStaff">
                            <div class="card card-body mb-3">
                                <?php if (sizeof($staff) > 0) { ?>
                                    <div class="list-group mb-3" id="list-tab" role="tablist">
                                        <?php foreach ($staff as $key => $value) { ?>
                                            <a class="list-group-item list-group-item-action <?php if ($key == 0) { echo 'active'; } ?>" id="list-<?php echo $key; ?>-list" data-toggle="list" href="#list-<?php echo $key; ?>" role="tab" aria-controls="<?php echo $key; ?>"><?php echo $value['first_name'].' '.$value['last_name'].' ('.$value['email'].')'; ?></a>
                                        <?php } ?>
                                    </div>
                                    <div class="tab-content" id="nav-tabContent">
                                        <?php foreach ($staff as $key => $value) { ?>
                                            <div class="tab-pane fade show <?php if ($key == 0) { echo 'active'; } ?>" id="list-<?php echo $key; ?>" role="tabpanel" aria-labelledby="list-<?php echo $key; ?>-list">
                                                <form action="/api/company/delete_staff.php" method="POST">
                                                    <div class="form-group row d-none">
                                                        <label for="deleteEmail" class="col-md-3 col-form-label">Email</label>
                                                        <div class="col-md-9">
                                                            <input type="email" class="form-control" id="deleteEmail" name="email" placeholder="Email" value="<?php echo $value['email']; ?>">
                                                        </div>
                                                    </div>
                                                    <button class="btn btn-danger btn-block" type="submit">Delete staff</button>
                                                </form>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <hr>
                                <?php } ?>
                                <form action="/api/company/add_staff.php" method="POST">
                                    <div class="form-group row">
                                        <label for="email" class="col-md-3 col-form-label">Email</label>
                                        <div class="col-md-9">
                                            <input type="email" class="form-control" id="email" name="email" placeholder="Email">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-0">
                                        <div class="col">
                                            <button type="submit" class="btn btn-success btn-block">Add staff</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <form action="/api/delete.php" method="POST">
                            <button class="btn btn-danger btn-block" type="submit">Delete account</button>
                        </form>
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
        <script src="/scripts/script.js"></script>
    </body>
</html>
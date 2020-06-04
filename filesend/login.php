<?php
require_once "src/config/db.php";

// redirect to upload page if already logged in
session_start();
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: upload.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $password = "";
    $error = $email_error = $password_error = "";

    // check if any of the fields is empty
    if (empty(trim($_POST["email"])) || empty(trim($_POST["password"]))) {
        $error = "Please enter all fields.";
    } else {
        $email = trim($_POST["email"]);
        $password = trim($_POST["password"]);
    }

    // proceed if no other errors
    if (empty($error)) {
        $sql = "SELECT id, email, password FROM Users WHERE email = ?";

        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $param_email);
            $param_email = $email;

            if ($stmt->execute()) {
                $stmt->store_result();

                // user with the email does exists
                if ($stmt->num_rows == 1) {
                    // proceed to check password
                    $stmt->bind_result($id, $email, $hashed_password);
                    if ($stmt->fetch()) {
                        if (password_verify($password, $hashed_password)) {
                            session_start();

                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["email"] = $email;

                            header("location: upload.php");
                        } else {
                            $email_error = "Something went wrong! The password might be incorrect.";
                        }
                    }
                } else {
                    $password_error = "No account associated with the email can be found.";
                }
            }
        }
        $stmt->close();
        $mysqli->close();
    }
}
?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
</head>

<body>
    <div class="container p-5">
        <h2>Login</h2>
        <p>Click here to <a href="register.php">register</a>.</p>
        <hr>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label><b>Email</b></label>
                <span class='ml-2 badge badge-warning'><?php echo $email_error; ?></span>
                <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
            </div>
            <div class="form-group">
                <label><b>Password</b></label>
                <span class='ml-2 badge badge-warning'><?php echo $password_error; ?></span>
                <input type="password" name="password" class="form-control">
            </div>

            <input type="submit" class="btn btn-primary" value="Login">
            <span class='ml-2 badge badge-warning'><?php echo $error; ?></span>
        </form>
    </div>
</body>

</html>
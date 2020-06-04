<?php
require_once "src/config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $password = $password2 = "";
    $email_error = $password_error = $password2_error = "";

    if (empty(trim($_POST["email"]))) {
        $email_error = "Please enter a email.";
    } else {

        // assume email is unique, then check if it already exists
        $sql = "SELECT id FROM Users WHERE email = ?";

        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $param_email);
            $param_email = trim($_POST["email"]);

            if ($stmt->execute()) {
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    $email_error = "This email is already registered.";
                } else {
                    $email = $param_email;
                }
            }
        }
        $stmt->close();
    }

    if (empty(trim($_POST["password"]))) {
        $password_error = "Please enter a password.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty(trim($_POST["password2"]))) {
        $password2_error = "Please confirm password.";
    } else {
        $password2 = trim($_POST["password2"]);
        if (empty($password_error) && ($password != $password2)) {
            $password2_error = "The passwords you entered do not match.";
        }
    }

    // proceed if no other errors
    if (empty($email_error) && empty($password_error) && empty($password2_error)) {

        $sql = "INSERT INTO Users (email, password) VALUES (?, ?)";

        if ($stmt = $mysqli->prepare($sql)) {

            // set email and password as a hashed one
            $stmt->bind_param("ss", $param_email, $param_password);
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT);

            if ($stmt->execute()) {
                header("location: login.php");
            }
        }
        $stmt->close();
    }
    $mysqli->close();
}
?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
</head>

<body>
    <div class="container p-5">
        <h2>Register</h2>
        <p>Click here to <a href="login.php">login</a>.</p>
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
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
            </div>
            <div class="form-group">
                <label><b>Confirm Password</b></label>
                <span class='ml-2 badge badge-warning'><?php echo $password2_error; ?></span>
                <input type="password" name="password2" class="form-control" value="<?php echo $password2; ?>">
            </div>
            <input type="submit" class="btn btn-primary" value="Register">
        </form>
    </div>
</body>

</html>
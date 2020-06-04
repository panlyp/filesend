<?php
require_once "src/config/db.php";
require_once "auth.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $status = "";
    $error = "";

    if ((!$_FILES["file"] || $_FILES["file"]["error"] <= 0)) {
        // following php.ini, size becomes 0 and no files will be posted if file size exceeds post_max_size
        if ($_FILES["file"]["size"] > 0) {
            session_start();

            $fileName = $_FILES['file']['name'];
            $fileSize = $_FILES["file"]["size"];
            $fileTmpName  = $_FILES["file"]["tmp_name"];
            //$fileType = $_FILES['file']['type'];
            $uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . "/upload/" . $_SESSION['id'] . '/';
            $filePath = $uploadDirectory . $_FILES["file"]["name"];

            // create a user directory under /upload
            if (!file_exists($uploadDirectory)) {
                mkdir($uploadDirectory, 0777, true);
            }
            // move file if the dir is writable
            if (is_dir($uploadDirectory) && is_writable($uploadDirectory)) {
                if (file_exists($filePath)) {
                    // TODO: current assumption is that a user will not upload 2 files with the same name
                    $error = "The file already exists!";
                } else {
                    $success = move_uploaded_file($fileTmpName, $filePath);

                    $sql = "INSERT INTO Files (userid, file_name, file_size, uploaded_at) VALUES (?, ?, ?, ?)";

                    if ($stmt = $mysqli->prepare($sql)) {
                        $stmt->bind_param("ssss", $param_userid, $param_file_name, $param_file_size, $param_uploaded_at);
                        $param_userid = $_SESSION['id'];
                        $param_file_name = $fileName;
                        $param_file_size = $fileSize;
                        $param_uploaded_at = date("Y-m-d H:i:s", time());

                        if ($success && $stmt->execute()) { // moved the file and sql executed successfully
                            $stmt->store_result();
                            $status = "Your file is uploaded successfully.";
                            header("location: upload.php");
                        } else {
                            $error = "Something went wrong!";
                        }
                    }

                    $stmt->close();
                    $mysqli->close();
                }
            } else {
                $error = 'Upload directory is not writable, or does not exist.';
            }
        } else {
            $error = "File exceeds maximum size (8MB)";
        }
    }
}
?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <title>Upload File</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
</head>

<body>
    <div class="container p-5">
        <h2>File Upload</h2>
        <p>Click here to <a href="logout.php">logout</a>.</p>
        <hr>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label><b>Select File (Maximum Size: 8MB)</b></label>
                <span class='ml-2 badge badge-warning'><?php echo $error; ?></span>
                <span class='ml-2 badge badge-secondary'><?php echo $status; ?></span>
                <input type="file" class="form-control-file" name="file">
                <input type="submit" class="btn btn-primary mt-2" value="Upload">
            </div>
        </form>
        <hr>
        <h4>My Files</h4>
        <?php include "display_files.php"; ?>
    </div>
</body>

</html>
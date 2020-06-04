<?php
require_once "src/config/db.php";
require_once "auth.php";

session_start();
$sql = "SELECT file_name, file_size, uploaded_at FROM Files WHERE userid = ?";

if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param("s", $param_userid);
    $param_userid = $_SESSION['id'];

    if ($stmt->execute()) {
        $results = $stmt->get_result();
        while ($record = $results->fetch_object()) {
            $files[] = $record;
        }
        if (!$files) {
            echo "No uploaded files.";
        } else {
            echo "<table id='file-desc' class='table'>";
            echo "<tr>" . "<th>File</th>" . "<th>Size (bytes)</th>" . "<th>Uploaded At</th>" . "</tr>";
            foreach ($files as $file) {
                echo "<tr><td><a href='upload/" . $_SESSION['id'] . "/"  . $file->file_name . "'>" . $file->file_name . "</a></td>";
                echo "<td>" . $file->file_size . "</td>";
                echo "<td>" . $file->uploaded_at . "</td></tr>";
            }
            echo "</table>";
        }
    }
}
$stmt->close();
$mysqli->close();
?>

<!DOCTYPE html>
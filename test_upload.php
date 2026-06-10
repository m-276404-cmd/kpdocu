<?php
include 'db_connect.php';
session_start();

// Set a test user ID (change to 1 for admin)
$_SESSION['login_id'] = 1;

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    echo "<h2>Upload Results:</h2>";
    
    // Check if files were uploaded
    if(isset($_FILES['ssm_files']) && !empty($_FILES['ssm_files']['name'][0])){
        echo "Files found!<br>";
        
        // Create uploads directory
        if (!is_dir('assets/uploads')) {
            mkdir('assets/uploads', 0777, true);
            echo "Created uploads folder<br>";
        }
        
        $saved_files = [];
        for($i = 0; $i < count($_FILES['ssm_files']['name']); $i++) {
            if($_FILES['ssm_files']['error'][$i] == 0) {
                $ext = pathinfo($_FILES['ssm_files']['name'][$i], PATHINFO_EXTENSION);
                $filename = 'test_' . time() . '_' . $i . '.' . $ext;
                $destination = 'assets/uploads/' . $filename;
                
                if(move_uploaded_file($_FILES['ssm_files']['tmp_name'][$i], $destination)){
                    $saved_files[] = $filename;
                    echo "Saved: " . $filename . "<br>";
                } else {
                    echo "Failed to move: " . $_FILES['ssm_files']['name'][$i] . "<br>";
                }
            }
        }
        
        // Save to database
        $files_json = json_encode($saved_files);
        $sql = "INSERT INTO documents (title, ssm_files, user_id, date_created) VALUES ('Test Document', '$files_json', 1, NOW())";
        
        if($conn->query($sql)){
            echo "<br>✅ Database saved! Document ID: " . $conn->insert_id;
            echo "<br>Files JSON: " . $files_json;
        } else {
            echo "<br>❌ Database error: " . $conn->error;
        }
    } else {
        echo "No files were uploaded";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Test File Upload</title>
</head>
<body>
    <h2>Test File Upload</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Select files to upload:</label>
        <input type="file" name="ssm_files[]" multiple>
        <br><br>
        <button type="submit">Upload</button>
    </form>
    
    <hr>
    <h3>Existing Documents:</h3>
    <?php
    $result = $conn->query("SELECT id, title, ssm_files FROM documents ORDER BY id DESC");
    while($row = $result->fetch_assoc()){
        echo "ID: " . $row['id'] . " - Title: " . $row['title'] . "<br>";
        echo "Files: " . $row['ssm_files'] . "<br><br>";
    }
    ?>
</body>
</html>
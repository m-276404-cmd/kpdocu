<?php
session_start();
include 'db_connect.php';
$_SESSION['login_id'] = 1;

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    echo "<h2>Form Submitted</h2>";
    echo "<pre>";
    echo "POST: ";
    print_r($_POST);
    echo "\nFILES: ";
    print_r($_FILES);
    echo "</pre>";
    
    // Process files
    if(isset($_FILES['ssm_files']) && !empty($_FILES['ssm_files']['name'][0])){
        $saved = [];
        for($i = 0; $i < count($_FILES['ssm_files']['name']); $i++){
            if($_FILES['ssm_files']['error'][$i] == 0){
                $ext = pathinfo($_FILES['ssm_files']['name'][$i], PATHINFO_EXTENSION);
                $filename = 'simple_' . time() . '_' . $i . '.' . $ext;
                move_uploaded_file($_FILES['ssm_files']['tmp_name'][$i], 'assets/uploads/' . $filename);
                $saved[] = $filename;
                echo "Saved: " . $filename . "<br>";
            }
        }
        
        $json = json_encode($saved);
        $title = $_POST['title'];
        $sql = "INSERT INTO documents (title, ssm_files, user_id) VALUES ('$title', '$json', 1)";
        if($conn->query($sql)){
            echo "✅ Database saved! ID: " . $conn->insert_id;
        } else {
            echo "❌ Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Simple Upload Test</title>
</head>
<body>
    <h2>Simple Upload Form (Same as your multi-step but simpler)</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Title:</label>
        <input type="text" name="title" value="Test Simple Upload" style="width: 300px;"><br><br>
        
        <label>SSM Files:</label>
        <input type="file" name="ssm_files[]" multiple><br><br>
        
        <button type="submit">Upload</button>
    </form>
</body>
</html>
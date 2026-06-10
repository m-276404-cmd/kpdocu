<?php
include 'db_connect.php';

echo "=== DOCUMENTS TABLE ===\n";
$r = $conn->query("DESCRIBE documents");
if ($r) {
    while($row = $r->fetch_assoc()) {
        echo "Field: {$row['Field']} | Type: {$row['Type']} | Null: {$row['Null']} | Key: {$row['Key']}\n";
    }
} else {
    echo "Error: " . $conn->error . "\n";
}

echo "\n=== USERS TABLE ===\n";
$r = $conn->query("DESCRIBE users");
if ($r) {
    while($row = $r->fetch_assoc()) {
        echo "Field: {$row['Field']} | Type: {$row['Type']} | Null: {$row['Null']} | Key: {$row['Key']}\n";
    }
} else {
    echo "Error: " . $conn->error . "\n";
}
?>

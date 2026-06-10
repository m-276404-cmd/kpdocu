<?php
include 'db_connect.php';

$result = $conn->query("SELECT id, title, ssm_files, tax_files, bank_files, financial_files, other_files FROM documents ORDER BY id DESC LIMIT 5");

echo "<h2>Checking Document Files in Database</h2>";

while($row = $result->fetch_assoc()) {
    echo "<hr>";
    echo "<strong>Document ID:</strong> " . $row['id'] . "<br>";
    echo "<strong>Title:</strong> " . $row['title'] . "<br>";
    echo "<strong>SSM Files:</strong> " . $row['ssm_files'] . "<br>";
    echo "<strong>Tax Files:</strong> " . $row['tax_files'] . "<br>";
    echo "<strong>Bank Files:</strong> " . $row['bank_files'] . "<br>";
    echo "<strong>Financial Files:</strong> " . $row['financial_files'] . "<br>";
    echo "<strong>Other Files:</strong> " . $row['other_files'] . "<br>";
}
?>
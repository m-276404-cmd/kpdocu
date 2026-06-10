<?php
include 'db_connect.php';

echo "<h2>All Documents in Database</h2>";

$result = $conn->query("SELECT * FROM documents ORDER BY id DESC");

if($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='8'>";
    echo "<tr><th>ID</th><th>Title</th><th>Owner Name</th><th>Owner IC</th><th>Status</th><th>Date Created</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['title'] ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($row['owner_name'] ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($row['owner_ic'] ?? '-') . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "<td>" . $row['date_created'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No documents found in database.</p>";
}
?>
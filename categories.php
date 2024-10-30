<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "uas";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get categories and count of blogs per category
$sql = "SELECT kategori.no, kategori.nama_kategori, COUNT(articles.id) AS blog_count
        FROM kategori
        LEFT JOIN articles ON kategori.no = articles.category_id
        GROUP BY kategori.no";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories - Web Programming Blog</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Categories</h1>
        <table border="1" cellspacing="0" cellpadding="10">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Category Name</th>
                    <th>Number of Blogs</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Check if any categories are found
                if ($result->num_rows > 0) {
                    // Output data for each category
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["no"] . "</td>";
                        echo "<td>" . $row["nama_kategori"] . "</td>";
                        echo "<td>" . $row["blog_count"] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No categories found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
// Close the connection
$conn->close();
?>

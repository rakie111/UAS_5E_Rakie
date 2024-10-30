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

// Fetch categories for the dropdown
$category_query = "SELECT no, nama_kategori FROM kategori";
$category_result = $conn->query($category_query);

// Handle form submission for adding a new blog post
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category_id = $_POST['category_id'];
    $author = $_POST['author'];
    
    // Image upload handling
    $image = $_FILES['image'];
    $image_name = time() . '_' . basename($image['name']); // Create a unique image name
    $target_directory = "uploads/"; // Directory where images will be stored
    $target_file = $target_directory . $image_name;
    $uploadOk = 1;

    // Check if image file is a actual image or fake image
    $check = getimagesize($image["tmp_name"]);
    if ($check === false) {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Check file size (limit to 2MB)
    if ($image["size"] > 2000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if everything is ok to upload file
    if ($uploadOk == 1) {
        if (move_uploaded_file($image["tmp_name"], $target_file)) {
            // Insert the new blog post into the articles table
            $insert_query = "INSERT INTO articles (title, content, category_id, author, image) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("ssiss", $title, $content, $category_id, $author, $image_name);

            if ($stmt->execute()) {
                echo "New blog post added successfully!";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Blog Post - Web Programming Blog</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Create New Blog Post</h1>
        
        <form method="POST" action="blog_crud.php" enctype="multipart/form-data">
            <label for="title">Title:</label><br>
            <input type="text" id="title" name="title" required><br><br>

            <label for="content">Content:</label><br>
            <textarea id="content" name="content" rows="5" required></textarea><br><br>

            <label for="category_id">Category:</label><br>
            <select id="category_id" name="category_id" required>
                <option value="">Select Category</option>
                <?php
                // Populate categories in dropdown
                if ($category_result->num_rows > 0) {
                    while ($row = $category_result->fetch_assoc()) {
                        echo "<option value='" . $row['no'] . "'>" . $row['nama_kategori'] . "</option>";
                    }
                } else {
                    echo "<option value=''>No categories available</option>";
                }
                ?>
            </select><br><br>

            <label for="author">Author:</label><br>
            <input type="text" id="author" name="author" required><br><br>

            <label for="image">Image:</label><br>
            <input type="file" id="image" name="image" accept="image/*" required><br><br>

            <button type="submit">Create Blog Post</button>
        </form>
    </div>
</body>
</html>

<?php
// Close the connection
$conn->close();
?>

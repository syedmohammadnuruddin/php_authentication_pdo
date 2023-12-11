<?php
session_start();
require_once 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        // Create operation - Add a new record
        $content = $_POST['content'];
        $stmt = $db->prepare("INSERT INTO data (user_id, content) VALUES (?, ?)");
        $stmt->execute([$user_id, $content]);
    } elseif (isset($_POST['update'])) {
        // Update operation - Update an existing record
        $data_id = $_POST['data_id'];
        $content = $_POST['content'];
        $stmt = $db->prepare("UPDATE data SET content = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$content, $data_id, $user_id]);
    } elseif (isset($_POST['delete'])) {
        // Delete operation - Delete a record
        $data_id = $_POST['data_id'];
        $stmt = $db->prepare("DELETE FROM data WHERE id = ? AND user_id = ?");
        $stmt->execute([$data_id, $user_id]);
    }
}

// Fetch user's data records
$stmt = $db->prepare("SELECT * FROM data WHERE user_id = ?");
$stmt->execute([$user_id]);
$data_records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<html>
<head>
    <title>CRUD</title>
</head>
<body>
    <h2>Welcome, <?php echo $user['username']; ?>!</h2>
    <a href="logout.php">Logout</a>

    <!-- Create form -->
    <h3>Create Record</h3>
    <form method="post" action="">
        <label>Content: <input type="text" name="content" required></label>
        <input type="submit" name="create" value="Create">
    </form>

    <!-- Display user's data records -->
    <h3>Your Data Records</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Content</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($data_records as $record): ?>
            <tr>
                <td><?php echo $record['id']; ?></td>
                <td><?php echo $record['content']; ?></td>
                <td>
                    <!-- Update form -->
                    <form method="post" action="">
                        <input type="hidden" name="data_id" value="<?php echo $record['id']; ?>">
                        <input type="text" name="content" value="<?php echo $record['content']; ?>" required>
                        <input type="submit" name="update" value="Update">
                    </form>

                    <!-- Delete form -->
                    <form method="post" action="">
                        <input type="hidden" name="data_id" value="<?php echo $record['id']; ?>">
                        <input type="submit" name="delete" value="Delete">
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>

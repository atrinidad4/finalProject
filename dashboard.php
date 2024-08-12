<?php
require_once 'includes/auth.php';
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
$action = isset($_GET['action']) ? $_GET['action'] : '';
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$success_message = '';
$error_message = '';

if ($action == 'delete' && $user_id > 0) {
    try {
        $stmt = $db->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute([':id' => $user_id]);
        $success_message = 'User deleted successfully.';
    } catch (PDOException $e) {
        $error_message = 'Error deleting user: ' . $e->getMessage();
    }
} elseif ($action == 'update' && $user_id > 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = isset($_POST['role']) ? 'admin' : 'user';

    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $db->prepare('UPDATE users SET username = :username, password = :password, role = :role WHERE id = :id');
            $stmt->execute([
                ':username' => $username,
                ':password' => password_hash($password, PASSWORD_DEFAULT),
                ':role' => $role,
                ':id' => $user_id
            ]);
            $success_message = 'User updated successfully.';
        } catch (PDOException $e) {
            $error_message = 'Error updating user: ' . $e->getMessage();
        }
    } else {
        $error_message = 'Username and password are required.';
    }
} elseif ($action == 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = isset($_POST['role']) ? 'admin' : 'user';

    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $db->prepare('INSERT INTO users (username, password, role) VALUES (:username, :password, :role)');
            $stmt->execute([
                ':username' => $username,
                ':password' => password_hash($password, PASSWORD_DEFAULT),
                ':role' => $role
            ]);
            $success_message = 'User created successfully.';
        } catch (PDOException $e) {
            $error_message = 'Error creating user: ' . $e->getMessage();
        }
    } else {
        $error_message = 'Username and password are required.';
    }
}

try {
    $stmt = $db->query('SELECT id, username, role FROM users');
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = 'Error fetching users: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css"> 
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <h1>Admin Dashboard</h1>

        <?php if ($success_message): ?>
            <p class="success"><?php echo htmlspecialchars($success_message); ?></p>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <section>
            <h2>Create User</h2>
            <form action="dashboard.php?action=create" method="post">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <label for="role">Admin:</label>
                <input type="checkbox" id="role" name="role">
                <button type="submit">Create User</button>
            </form>
        </section>

        <?php if ($user_id > 0 && $action == 'update'): ?>
            <?php
            try {
                $stmt = $db->prepare('SELECT username, role FROM users WHERE id = :id');
                $stmt->execute([':id' => $user_id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                $error_message = 'Error fetching user data: ' . $e->getMessage();
            }
            ?>
            <?php if ($user): ?>
                <section>
                    <h2>Update User</h2>
                    <form action="dashboard.php?action=update&id=<?php echo $user_id; ?>" method="post">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                        <label for="role">Admin:</label>
                        <input type="checkbox" id="role" name="role" <?php echo $user['role'] === 'admin' ? 'checked' : ''; ?>>
                        <button type="submit">Update User</button>
                    </form>
                </section>
            <?php endif; ?>
        <?php endif; ?>

        <a href="/pages/manage_pages.php"><h3>Manage Created Pages</h3></a>

        <section>
            <h2>Manage Users</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td>
                                <a href="dashboard.php?action=update&id=<?php echo $user['id']; ?>">Edit</a>
                                <a href="dashboard.php?action=delete&id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

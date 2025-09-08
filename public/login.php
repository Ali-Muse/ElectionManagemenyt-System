<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role']; // Also retrieve role

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ? AND role = ?");
    $stmt->bind_param("sss", $username, $password, $role);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();

    if ($userData) {
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['role'] = $userData['role'];
        $_SESSION['username'] = $userData['username'];

        if ($userData['role'] === "admin") {
            header("Location: /EMS2/admin/dashboard.php");
        } elseif ($userData['role'] === "voter") {
            header("Location: /EMS2/voter/dashboard.php");
        } elseif ($userData['role'] === "candidate") {
            header("Location: /EMS2/candidate/dashboard.php");
        }
        exit();
    } else {
        $error = "Username, password, or role do not match.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | EMS</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="min-h-screen flex flex-col bg-gray-50">
    <?php include '../includes/navbar.php'; ?>
    <main class="flex-1">
    <div class="container mx-auto mt-10 p-6 bg-white rounded shadow max-w-md">
        <h1 class="text-3xl font-bold mb-4">Login</h1>
        <?php if ($error): ?>
            <div class="bg-red-100 text-red-800 p-4 rounded mb-4"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-gray-700">Username</label>
                <input type="text" name="username" required class="w-full border p-2 rounded">
            </div>
            <div>
                <label class="block text-gray-700">Password</label>
                <input type="password" name="password" required class="w-full border p-2 rounded">
            </div>
            <div>
                <label class="block text-gray-700">Role</label>
                <select name="role" required class="w-full border p-2 rounded">
                    <option value="admin">Admin</option>
                    <option value="voter">Voter</option>
                    <option value="candidate">Candidate</option>
                </select>
            </div>
            <button type="submit" class="bg-purple-700 hover:bg-purple-800 text-white px-4 py-2 rounded">Login</button>
        </form>
    </div>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html> 
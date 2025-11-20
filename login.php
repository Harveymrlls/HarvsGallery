<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['action'])) {
        $message = "Invalid request. Please use the form.";
        $message_type = "error";
    } else {
        $action = $_POST['action'];
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        if ($action == 'signup') {
            if (empty($username) || empty($password)) {
                $message = "Username and password are required.";
                $message_type = "error";
            } else {
                // Check if username already exists
                $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
                $stmt->execute([$username]);
                if ($stmt->fetch()) {
                    $message = "Username already exists. Please choose another.";
                    $message_type = "error";
                } else {
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
                    try {
                        $stmt->execute([$username, $hashed]);
                        $message = "Signup successful! Please log in.";
                        $message_type = "success";
                    } catch (PDOException $e) {
                        error_log("Signup error: " . $e->getMessage());
                        $message = "Signup failed. Please try again.";
                        $message_type = "error";
                    }
                }
            }
        } elseif ($action == 'login') {
            $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: gallery.php");
                exit;
            } else {
                $message = "Invalid username or password.";
                $message_type = "error";
            }
        } else {
            $message = "Invalid action.";
            $message_type = "error";
        }
    }
}

// If we have a message to display, show the HTML with the message
if (isset($message)) {
    displayLoginPage($message, $message_type);
} else {
    // If no message (first visit), just show the regular login page
    displayLoginPage();
}

function displayLoginPage($message = '', $message_type = '') {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>HarvsGallery Login</title>
        <link rel="stylesheet" href="css/style.css"> 
        <link rel="stylesheet" href="css/login.css">
        <script src="https://cdn.tailwindcss.com"></script>
    </head>

    <body class="py-20 bg-purple-100 flex items-center justify-center min-h-screen">

        <div class="bg-white w-full max-w-md p-8 rounded-2xl shadow-lg">
            
            <h1 class="text-3xl font-bold text-center mb-6 text-gray-800">
                Welcome to <span style="color: #62109F">HarvsGallery</span>
            </h1>

            <!-- Login Form -->
            <div class="mb-10">
                <h2 class="text-xl font-semibold mb-3 text-gray-700">Login</h2>

                <form method="POST" action="login.php" class="space-y-4">
                    <input 
                        type="text" 
                        name="username" 
                        placeholder="Username"
                        class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none"
                        required
                    >
                    <input 
                        type="password" 
                        name="password" 
                        placeholder="Password"
                        class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none"
                        required
                    >
                    <button 
                        type="submit" 
                        name="action" 
                        value="login"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold transition"
                        style="background: #62109F;"
                    >
                        Login
                    </button>
                    <?php if ($message && $_POST['action'] == 'login'): ?>
                    <div class="message <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
                </form>
            </div>

            <!-- Divider -->
            <div class="flex items-center gap-4 mb-8">
                <div class="flex-grow h-px bg-gray-300"></div>
                <span class="text-gray-500 text-sm">OR</span>
                <div class="flex-grow h-px bg-gray-300"></div>
            </div>

            <!-- Sign Up Form -->
            <div>
                <h2 class="text-xl font-semibold mb-3 text-gray-700">Create an Account</h2>

                <form method="POST" action="login.php" class="space-y-4">
                    <input 
                        type="text" 
                        name="username" 
                        placeholder="Username"
                        class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-green-500 outline-none"
                        required
                    >
                    <input 
                        type="password" 
                        name="password" 
                        placeholder="Password"
                        class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-green-500 outline-none"
                        required
                    >
                    <button 
                        type="submit" 
                        name="action" 
                        value="signup"
                        class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold transition"
                    >
                        Sign Up
                    </button>
                    <?php if ($message && $_POST['action'] == 'signup'): ?>
                    <div class="message <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
                </form>
            </div>

        </div>

    </body>
    </html>
    <?php
}
?>


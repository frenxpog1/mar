<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "Hanem2004@";
$dbname = "MyDb";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle login form submission
$login_error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $login_error = "Please fill in all fields.";
    } else {
        try {
            $stmt = $conn->prepare("SELECT id, email, password FROM school_system WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                header("Location: dashboard.html"); // Redirect to a dashboard page
                exit();
            } else {
                $login_error = "Invalid email or password.";
            }
        } catch(PDOException $e) {
            $login_error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Afterclass</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <img src="logo.png" alt="Logo">
            <span>afterclass</span>
        </div>
        <div class="nav-links">
            <a href="#" class="active">Home</a>
            <a href="#">Features</a>
            <a href="#">About</a>
        </div>
        <div class="auth-buttons">
            <a href="#" class="login-btn">Login</a>
            <a href="signup.php" class="signup-btn">Sign Up</a>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <h1>Welcome Back</h1>
            <p>Log in with your credentials to sign in</p>

            <?php if ($login_error): ?>
                <p style="color: var(--danger); text-align: center;"><?php echo htmlspecialchars($login_error); ?></p>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div>
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div>
                    <label>
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="#">Forgot Password?</a>
                </div>
                <button type="submit" name="login" class="primary-btn">Sign In</button>
                <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
            </form>
        </div>
    </section>
</body>
</html>
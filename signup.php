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

// Handle signup form submission
$signup_error = '';
$signup_success = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $signup_error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $signup_error = "Invalid email format.";
    } else {
        try {
            // Check if email already exists
            $stmt = $conn->prepare("SELECT id FROM school_system WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            if ($stmt->fetch()) {
                $signup_error = "Email already registered.";
            } else {
                // Insert new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO school_system (email, password, created_at) VALUES (:email, :password, NOW())");
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashed_password);
                $stmt->execute();
                $signup_success = "Registration successful! You can now <a href='login.php'>log in</a>.";
            }
        } catch(PDOException $e) {
            $signup_error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Afterclass</title>
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
            <a href="login.php" class="login-btn">Login</a>
            <a href="#" class="signup-btn">Sign Up</a>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <h1>Welcome!</h1>
            <p>The Afterclass platform is here to help you manage your academic life in one place.</p>

            <?php if ($signup_error): ?>
                <p style="color: var(--danger); text-align: center;"><?php echo htmlspecialchars($signup_error); ?></p>
            <?php elseif ($signup_success): ?>
                <p style="color: var(--success); text-align: center;"><?php echo $signup_success; ?></p>
            <?php endif; ?>

            <div>
                <button class="primary-btn">Register with Google</button>
                <button class="primary-btn">Register with Apple</button>
            </div>
            <p>or</p>
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
                </div>
                <button type="submit" name="register" class="primary-btn">Register</button>
                <p>Already have an account? <a href="login.php">Sign In</a></p>
            </form>
        </div>
    </section>
</body>
</html>
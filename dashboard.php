<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

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

// Fetch user data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT email FROM school_system WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$user_name = explode('@', $user['email'])[0]; // Use email prefix as name (e.g., "francis")

// Fetch earnings
$stmt = $conn->prepare("SELECT total_earnings, pending_earnings, monthly_earnings, overall_earnings FROM earnings WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$earnings = $stmt->fetch(PDO::FETCH_ASSOC);
$total_earnings = $earnings['total_earnings'] ?? 0.00;
$pending_earnings = $earnings['pending_earnings'] ?? 0.00;
$monthly_earnings = $earnings['monthly_earnings'] ?? 0.00;
$overall_earnings = $earnings['overall_earnings'] ?? 0.00;

// Fetch tasks
$stmt = $conn->prepare("SELECT task_name, progress, assignees FROM tasks WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
$active_tasks = count($tasks);
$today_tasks = count(array_filter($tasks, function($task) {
    return date('Y-m-d', strtotime($task['due_date'])) == date('Y-m-d');
}));

// Fetch schedules
$stmt = $conn->prepare("SELECT event_name, event_time, event_date FROM schedules WHERE user_id = :user_id AND event_date >= CURDATE()");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch materials
$stmt = $conn->prepare("SELECT course_name, material_name FROM materials WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$materials = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch task summary
$stmt = $conn->prepare("SELECT completed_tasks, pending_tasks, missed_tasks, overdue_tasks FROM task_summary WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$summary = $stmt->fetch(PDO::FETCH_ASSOC);
$completed_tasks = $summary['completed_tasks'] ?? 0;
$pending_tasks = $summary['pending_tasks'] ?? 0;
$missed_tasks = $summary['missed_tasks'] ?? 0;
$overdue_tasks = $summary['overdue_tasks'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Afterclass</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body class="app-body">
    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="logo.png" alt="Logo">
            <span>afterclass</span>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="dashboard-link active"><i class="fas fa-home"></i><span>Dashboard</span></a>
            <a href="#"><i class="fas fa-book"></i><span>Study Hub</span></a>
            <a href="#"><i class="fas fa-comments"></i><span>Discussions</span></a>
            <a href="#" class="ai-link"><i class="fas fa-robot"></i><span>Afterclass[AI]te</span></a>
            <a href="#"><i class="fas fa-calendar"></i><span>Schedules</span></a>
            <a href="#"><i class="fas fa-briefcase"></i><span>Opportunities</span></a>
            <a href="#"><i class="fas fa-cog"></i><span>Settings</span></a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="app-header">
            <div class="header-welcome">
                <h1>Hello, <?php echo htmlspecialchars(ucfirst($user_name)); ?>!</h1>
                <p>Welcome back! Let's see what you've got.</p>
            </div>
            <div class="header-search">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search...">
            </div>
            <div class="header-profile">
                <i class="fas fa-bell"></i>
                <img src="profile.jpg" alt="Profile">
            </div>
        </header>

        <section class="dashboard-content">
            <div class="stats-cards">
                <div class="stat-card blue">
                    <div class="stat-info">
                        <h3>Total Earnings</h3>
                        <div class="stat-value">Php <?php echo number_format($total_earnings, 2); ?></div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-wallet"></i></div>
                </div>
                <div class="stat-card purple">
                    <div class="stat-info">
                        <h3>Pending</h3>
                        <div class="stat-value">Php <?php echo number_format($pending_earnings, 2); ?></div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
                </div>
                <div class="stat-card green">
                    <div class="stat-info">
                        <h3>Monthly</h3>
                        <div class="stat-value">Php <?php echo number_format($monthly_earnings, 2); ?></div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                </div>
                <div class="stat-card orange">
                    <div class="stat-info">
                        <h3>Total</h3>
                        <div class="stat-value">Php <?php echo number_format($overall_earnings, 2); ?></div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="tasks-section">
                    <div class="section-header">
                        <h2>Ongoing Tasks</h2>
                        <div class="task-count"><?php echo $active_tasks; ?> active • <?php echo $today_tasks; ?> today</div>
                    </div>
                    <div class="tasks-list">
                        <?php foreach ($tasks as $task): ?>
                            <div class="task-item">
                                <div class="task-info">
                                    <h3><?php echo htmlspecialchars($task['task_name']); ?></h3>
                                    <div class="task-meta">
                                        <div class="task-assignees">
                                            <?php
                                            $assignees = explode(',', $task['assignees']);
                                            foreach ($assignees as $assignee): ?>
                                                <img src="<?php echo htmlspecialchars($assignee); ?>" alt="Assignee">
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="task-status">
                                    <div class="progress-bar">
                                        <div class="progress" style="width: <?php echo $task['progress']; ?>%;"></div>
                                    </div>
                                    <div class="progress-text"><?php echo $task['progress']; ?>%</div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="calendar-section">
                    <div class="calendar">
                        <div class="calendar-header">
                            <h2>March 2025</h2>
                            <div class="month-selector">
                                <div class="month-nav">
                                    <button><i class="fas fa-chevron-left"></i></button>
                                    <button><i class="fas fa-chevron-right"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="weekdays">
                            <span>S</span><span>M</span><span>T</span><span>W</span><span>T</span><span>F</span><span>S</span>
                        </div>
                        <div class="days">
                            <div class="day prev-month">26</div><div class="day prev-month">27</div><div class="day prev-month">28</div>
                            <?php
                            // Generate calendar days (static for March 2025, highlight current day)
                            $current_day = date('j');
                            for ($day = 1; $day <= 31; $day++) {
                                $class = ($day == $current_day) ? 'day current' : 'day';
                                echo "<div class='$class'>$day</div>";
                            }
                            ?>
                            <div class="day next-month">1</div>
                        </div>
                    </div>

                    <div class="upcoming-events">
                        <h3>Upcoming Schedule</h3>
                        <?php
                        $current_date = '';
                        foreach ($schedules as $schedule):
                            $event_date = date('d M', strtotime($schedule['event_date']));
                            if ($event_date !== $current_date):
                                if ($current_date !== '') echo '</div>'; // Close previous date group
                                $current_date = $event_date;
                                echo "<div class='event-date'><h4>$event_date</h4>";
                            endif;
                        ?>
                            <div class="event-item">
                                <div class="event-time"><?php echo htmlspecialchars($schedule['event_time']); ?></div>
                                <div class="event-name"><?php echo htmlspecialchars($schedule['event_name']); ?></div>
                            </div>
                        <?php endforeach; ?>
                        <?php if ($current_date !== '') echo '</div>'; // Close last date group ?>
                    </div>

                    <div class="class-materials">
                        <h3>Available Class Materials</h3>
                        <div class="materials-list">
                            <?php foreach ($materials as $material): ?>
                                <div class="material-item">
                                    <div class="material-icon"><i class="fas fa-file-alt"></i></div>
                                    <div class="material-info">
                                        <h4><?php echo htmlspecialchars($material['course_name']); ?></h4>
                                        <p><?php echo htmlspecialchars($material['material_name']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="task-summary">
                    <h2>Tasks Summary</h2>
                    <p>Here's what you've accomplished so far. Keep up the good work!</p>
                    <div class="summary-stats">
                        <div class="summary-stat green">
                            <div class="stat-number"><?php echo $completed_tasks; ?></div>
                            <div class="stat-label">Completed Tasks</div>
                        </div>
                        <div class="summary-stat orange">
                            <div class="stat-number"><?php echo $pending_tasks; ?></div>
                            <div class="stat-label">Pending Tasks</div>
                        </div>
                        <div class="summary-stat red">
                            <div class="stat-number"><?php echo $missed_tasks; ?></div>
                            <div class="stat-label">Missed Tasks</div>
                        </div>
                        <div class="summary-stat purple">
                            <div class="stat-number"><?php echo $overdue_tasks; ?></div>
                            <div class="stat-label">Overdue Tasks</div>
                        </div>
                    </div>
                </div>

                <div class="quote-section">
                    <div class="quote">
                        <p>"Mag-aral ka kay logo ka na ka isip ni Einstein ba."</p>
                        <cite>- Grandma's words of wisdom</cite>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
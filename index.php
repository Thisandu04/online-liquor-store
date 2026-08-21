<?php
// index.php (root) — Age verification landing page
session_start();

if (isset($_SESSION['age_verified']) && $_SESSION['age_verified'] === true) {
    header("Location: customer/index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $day = $_POST['day'] ?? '';
    $month = $_POST['month'] ?? '';
    $year = $_POST['year'] ?? '';

    if (!ctype_digit($day) || !ctype_digit($month) || !ctype_digit($year)) {
        $error = 'Please enter a valid date of birth.';
    } else {
        $dob = DateTime::createFromFormat('Y-n-j', "$year-$month-$day");

        if (!$dob || $dob->format('Y-n-j') !== "$year-$month-$day") {
            $error = 'Please enter a valid date.';
        } else {
            $today = new DateTime();
            $age = $today->diff($dob)->y;

            if ($age >= 18) {
                $_SESSION['age_verified'] = true;
                header("Location: customer/index.php");
                exit;
            } else {
                $error = 'You must be 18 or older to access this site.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Age Verification - Online Liquor Store</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="age-verify-container">
        <h1>Welcome to Online Liquor Store</h1>
        <p>You must be 18 years or older to enter this site.</p>

        <?php if ($error): ?>
            <p class="error-msg"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" action="index.php" id="ageForm">
            <label>Date of Birth</label>
            <div class="dob-inputs">
                <input type="number" name="day" placeholder="DD" min="1" max="31" required>
                <input type="number" name="month" placeholder="MM" min="1" max="12" required>
                <input type="number" name="year" placeholder="YYYY" min="1900" max="2100" required>
            </div>
            <button type="submit">Enter Site</button>
        </form>

        <p class="disclaimer">By entering, you confirm the information provided is accurate.</p>
    </div>

    <script src="assets/js/age-verify.js"></script>
</body>
</html>
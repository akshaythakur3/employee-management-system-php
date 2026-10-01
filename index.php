<?php
require __DIR__ . '/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set'])) {
    $name = trim($_POST['ename'] ?? '');
    $salary = trim($_POST['esalary'] ?? '');
    $team = trim($_POST['eteam'] ?? '');

    if (!isCsrfTokenValid($_POST['csrf_token'] ?? null)) {
        $message = 'Your session expired. Please submit the form again.';
    } elseif ($name === '' || $team === '' || !is_numeric($salary) || (float)$salary < 0) {
        $message = 'Please fill in all employee fields.';
    } else {
        try {
            $conn = getDbConnection();
            $stmt = $conn->prepare('INSERT INTO employees (name, salary, team) VALUES (?, ?, ?)');
            $stmt->bind_param('sss', $name, $salary, $team);
            $stmt->execute();
            redirect('fetch.php');
        } catch (Throwable $e) {
            $message = 'Unable to save employee data right now.';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Employee Details</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2><u>Employee Details</u></h2>

        <?php if ($message !== ''): ?>
            <div class="notice"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form action="" method="post">
            <fieldset>
                <legend><b>General Information</b></legend>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                <label for="ename">Employee Name: <input type="text" name="ename" placeholder="Enter name" required></label><br><br>
                <label for="esalary">Employee Salary: <input type="number" name="esalary" min="0" step="0.01" placeholder="Enter salary" required></label><br><br>
                <label for="eteam">Employee Team: <input type="text" name="eteam" placeholder="Enter team" required></label><br><br>
                <button type="submit" name="set">SUBMIT</button>
                <button type="reset" name="reset">RESET</button>
                <a href="fetch.php" target="_self" class="link-btn">Fetch Data</a>
            </fieldset>
        </form>
    </div>
</body>
</html>


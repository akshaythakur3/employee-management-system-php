<?php
require __DIR__ . '/db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$employee = [
    'name' => '',
    'salary' => '',
    'team' => '',
];
$message = '';

if ($id <= 0) {
    redirect('fetch.php');
}

try {
    $conn = getDbConnection();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set'])) {
        $name = trim($_POST['uname'] ?? '');
        $salary = trim($_POST['usalary'] ?? '');
        $team = trim($_POST['uteam'] ?? '');

        if (!isCsrfTokenValid($_POST['csrf_token'] ?? null)) {
            $message = 'Your session expired. Please submit the form again.';
        } elseif ($name === '' || $team === '' || !is_numeric($salary) || (float)$salary < 0) {
            $message = 'Enter a name, team, and a valid non-negative salary.';
        } else {
            $stmt = $conn->prepare('UPDATE employees SET name = ?, salary = ?, team = ? WHERE id = ?');
            $stmt->bind_param('sssi', $name, $salary, $team, $id);
            $stmt->execute();
            redirect('fetch.php');
        }
    }

    $stmt = $conn->prepare('SELECT name, salary, team FROM employees WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $loadedEmployee = $result->fetch_assoc();
    if ($loadedEmployee === null) {
        redirect('fetch.php');
    }
    $employee = $loadedEmployee;
} catch (Throwable $e) {
    $message = 'Unable to load or update employee data.';
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Update Employee</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <?php if ($message !== ''): ?>
            <div class="notice"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form action="" method="post">
            <fieldset>
                <legend><b>Update Information</b></legend>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                <label for="uname">Employee Name: <input type="text" name="uname" required placeholder="Enter name" value="<?php echo htmlspecialchars($employee['name']); ?>"></label><br><br>
                <label for="usalary">Employee Salary: <input type="number" name="usalary" min="0" step="0.01" required placeholder="Enter salary" value="<?php echo htmlspecialchars($employee['salary']); ?>"></label><br><br>
                <label for="uteam">Employee Team: <input type="text" name="uteam" required placeholder="Enter team" value="<?php echo htmlspecialchars($employee['team']); ?>"></label><br><br>
                <button type="submit" name="set">SUBMIT</button>
                <button type="reset" name="reset">RESET</button>
            </fieldset>
        </form>
    </div>
</body>
</html>

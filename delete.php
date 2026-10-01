<?php
require __DIR__ . '/db.php';

$id = $_SERVER['REQUEST_METHOD'] === 'POST'
    ? filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT)
    : filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id <= 0) {
    redirect('fetch.php');
}

$message = '';
$employeeName = '';

try {
    $conn = getDbConnection();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isCsrfTokenValid($_POST['csrf_token'] ?? null)) {
            $message = 'Your session expired. Please reload and try again.';
        } else {
            $stmt = $conn->prepare('DELETE FROM employees WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            redirect('fetch.php');
        }
    }

    $stmt = $conn->prepare('SELECT name FROM employees WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $employee = $result->fetch_assoc();

    if ($employee === null) {
        redirect('fetch.php');
    }

    $employeeName = $employee['name'];
} catch (Throwable $e) {
    $message = 'Unable to delete this employee right now.';
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Confirm Employee Deletion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Delete Employee</h2>

        <?php if ($message !== ''): ?>
            <div class="notice"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if ($employeeName !== ''): ?>
            <p>Are you sure you want to delete <strong><?php echo htmlspecialchars($employeeName, ENT_QUOTES, 'UTF-8'); ?></strong>?</p>
            <form action="" method="post" class="inline-form">
                <input type="hidden" name="id" value="<?php echo (int)$id; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                <button type="submit">CONFIRM DELETE</button>
                <a href="fetch.php" class="link-btn">CANCEL</a>
            </form>
        <?php else: ?>
            <a href="fetch.php" class="link-btn">Back to employees</a>
        <?php endif; ?>
    </div>
</body>
</html>
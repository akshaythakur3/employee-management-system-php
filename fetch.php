<?php
require __DIR__ . '/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isCsrfTokenValid($_POST['csrf_token'] ?? null)) {
        $message = 'Your session expired. Please reload the page and try again.';
    } elseif (isset($_POST['update_id'])) {
        $id = filter_var($_POST['update_id'], FILTER_VALIDATE_INT);
        if ($id !== false && $id > 0) {
            redirect('update.php?id=' . $id);
        }
    } elseif (isset($_POST['delete_id'])) {
        $id = filter_var($_POST['delete_id'], FILTER_VALIDATE_INT);
        if ($id !== false && $id > 0) {
            redirect('delete.php?id=' . $id);
        }
    }
}

$employees = [];
$loadError = false;

try {
    $conn = getDbConnection();
    $stmt = $conn->prepare('SELECT id, name, salary, team FROM employees ORDER BY id ASC');
    $stmt->execute();
    $result = $stmt->get_result();
    $employees = $result->fetch_all(MYSQLI_ASSOC);
} catch (Throwable $e) {
    $employees = [];
    $loadError = true;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Fetched Data</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2><u>Data Fetched</u></h2>

        <?php if ($message !== ''): ?>
            <div class="notice"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php if ($loadError): ?>
            <div class="notice">Unable to load employee records right now.</div>
        <?php endif; ?>

        <div class="table-wrap">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Salary</th>
                    <th>Team</th>
                    <th>Update</th>
                    <th>Delete</th>
                </tr>

                <?php if ($loadError): ?>
                    <tr><td colspan="6">Employee records could not be loaded.</td></tr>
                <?php elseif (empty($employees)): ?>
                    <tr>
                        <td colspan="6">No employee records found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($employees as $employee): ?>
                        <tr>
                            <td><?php echo htmlspecialchars((string)$employee['id']); ?></td>
                            <td><?php echo htmlspecialchars($employee['name']); ?></td>
                            <td><?php echo htmlspecialchars($employee['salary']); ?></td>
                            <td><?php echo htmlspecialchars($employee['team']); ?></td>
                            <td>
                                <form action="" method="post" class="inline-form">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="update_id" value="<?php echo (int)$employee['id']; ?>">
                                    <button type="submit">UPDATE</button>
                                </form>
                            </td>
                            <td>
                                <form action="" method="post" class="inline-form">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="delete_id" value="<?php echo (int)$employee['id']; ?>">
                                    <button type="submit">DELETE</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </table>
        </div>

        <br>
        <b>Want to insert data? </b>
        <a href="index.php" target="_self" class="link-btn" style="margin-top: 0;">Click Here</a>
    </div>
</body>
</html>


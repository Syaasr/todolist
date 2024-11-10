<?php
include 'db.php';

$editMode = false;
$task_to_edit = null; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task'])) {
    if (isset($_POST['task_id']) && $_POST['task_id'] != '') {
        $task_id = $_POST['task_id'];
        $task = $_POST['task'];
        $stmt = $conn->prepare("UPDATE tasks SET task = ? WHERE id = ?");
        $stmt->bind_param("si", $task, $task_id);
        $stmt->execute();
        $stmt->close();
    } else {
        $task = $_POST['task'];
        $stmt = $conn->prepare("INSERT INTO tasks (task) VALUES (?)");
        $stmt->bind_param("s", $task);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: index.php");
    exit;
}

if (isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit;
}

if (isset($_POST['complete_id'])) {
    $complete_id = $_POST['complete_id'];
    $stmt = $conn->prepare("UPDATE tasks SET is_completed = !is_completed WHERE id = ?");
    $stmt->bind_param("i", $complete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit;
}

if (isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id'];
    $result = $conn->query("SELECT * FROM tasks WHERE id = $edit_id");
    $task_to_edit = $result->fetch_assoc();
    $editMode = true;
}

$tasks = $conn->query("SELECT * FROM tasks ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To Do List</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>To Do List</h1>
        <p class="author">By Syaikhasril Maulana Firdaus</p>
        <form method="POST">
            <input type="hidden" name="task_id" value="<?= $editMode ? $task_to_edit['id'] : '' ?>">
            <input type="text" name="task" placeholder="Tambah tugas baru" required value="<?= $editMode ? htmlspecialchars($task_to_edit['task']) : '' ?>">
            <button type="submit"><?= $editMode ? 'Perbarui Tugas' : 'Tambah Tugas' ?></button>
        </form>
        <ul>
            <?php while ($task = $tasks->fetch_assoc()): ?>
                <li class="<?= $task['is_completed'] ? 'completed' : '' ?>">
                    <span><?= htmlspecialchars($task['task']) ?></span>
                    <div class="actions">
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="complete_id" value="<?= $task['id'] ?>">
                            <button type="submit" class="button complete-btn"><?= $task['is_completed'] ? 'X' : '✓' ?></button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="edit_id" value="<?= $task['id'] ?>">
                            <button type="submit" class="button edit-btn">Edit</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?= $task['id'] ?>">
                            <button type="submit" class="button delete-btn">Hapus</button>
                        </form>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</body>
</html>

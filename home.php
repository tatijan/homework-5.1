<?php
$sql = "SELECT id FROM user WHERE login = ?";
$stm = $db->prepare($sql);
$stm->execute([
    $login
]);
$userId = $stm->fetchColumn();
$description = "";
$action = !empty($_GET['action']) ? $_GET['action'] : null;
$orderBy = "date_added";
$sortVariants = ['date_added', 'description', 'is_done'];
if (isset($_POST['sort']) && !empty($_POST['sort_by']) && in_array($_POST['sort_by'], $sortVariants)) {
    $orderBy = $_POST['sort_by'];
}
if (!isset($_GET['id']) && isset($_POST['save']) && !empty($_POST['description'])) {
    $description = $_POST['description'];
    $sql = "INSERT INTO task (user_id, description, date_added) VALUES (?, ?, NOW())";
    $stm = $db->prepare($sql);
    $stm->execute([
        $userId,
        $description
    ]);
    redirect('index');
}
if (!empty($action) && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($action == 'delete') {
        $sql = "DELETE FROM task WHERE id = ? AND user_id = ?";
        $stm = $db->prepare($sql);
        $stm->execute([
            $id,
            $userId
        ]);
    }
    if ($action == 'done') {
        $sql = "UPDATE task SET is_done = 1 WHERE id = ? AND (user_id = ? OR assigned_user_id = ?)";
        $stm = $db->prepare($sql);
        $stm->execute([
            $id,
            $userId,
            $userId
        ]);
        redirect('index');
    }
    if (!empty($_POST['description'])) {
        $description = $_POST['description'];
        $sql = "UPDATE task SET description = ? WHERE id = ? AND user_id = ?";
        $stm = $db->prepare($sql);
        $stm->execute([
            $description,
            $id,
            $userId
        ]);
        redirect('index');
    }
    if ($action == 'edit') {
        $sql = "SELECT description FROM task WHERE id = ?";
        $stm = $db->prepare($sql);
        $stm->execute([$id]);
        $description = $stm->fetchColumn();
    }
}
if (!empty($_POST['assign']) && !empty($_POST['assigned_user_id'])) {
    $formData = explode("_", $_POST['assigned_user_id']);
    $assignedUserId = (int)$formData[1];
    $taskId = (int)$formData[3];
    if (!empty($userId) && !empty($taskId)) {
        $sql = "UPDATE task SET assigned_user_id = ? WHERE id = ? AND user_id = ?";
        $stm = $db->prepare($sql);
        $stm->execute([
            $assignedUserId,
            $taskId,
            $userId
        ]);
        redirect('index');
    }
}
$sql = "SELECT t.*, u.login, u2.login author
        FROM task t
        LEFT JOIN user u ON t.assigned_user_id = u.id
        LEFT JOIN user u2 ON t.user_id = u2.id
        WHERE user_id = ?
        ORDER BY $orderBy";
$stm = $db->prepare($sql);
$stm->execute([
    $userId
]);
$myTasks = $stm->fetchAll();
//
$sql = "SELECT t.*, u.login, u2.login author
        FROM task t
        LEFT JOIN user u ON t.assigned_user_id = u.id
        LEFT JOIN user u2 ON t.user_id = u2.id
        WHERE assigned_user_id = ? ORDER BY $orderBy";
$stm = $db->prepare($sql);
$stm->execute([
    $userId
]);
$myAssignedTasks = $stm->fetchAll();
//
$sql = "SELECT * FROM user WHERE id <> ?";
$stm = $db->prepare($sql);
$stm->execute([
    $userId
]);
$userList = $stm->fetchAll();
$user = [];
foreach ($userList as $item) {
    $user[$item['id']] = $item['login'];
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Список дел</title>

    <style>
        table {
            border-spacing: 0;
            border-collapse: collapse;
        }
        table td, table th {
            border: 1px solid #ccc;
            padding: 5px;
        }
        table th {
            background: #eee;
        }
        .form-sort {
            display: inline-block;
        }
        .form-sort:first-child {
            margin-right: 20px;
        }
    </style>
</head>
<body>
<h1>Здравствуйте, <?php echo$_SESSION['login'] ?>! Вот ваш список дел:</h1>
<div class="form">
    <form class="form-sort" method="POST">
        <input type="text" name="description" placeholder="Описание задачи" value="<?=$description?>" />
        <input type="submit" name="save" value="<?php echo ($action == 'edit' ? 'Сохранить' : 'Добавить') ?>" />
    </form>

    <form  class="form-sort" method="POST">
        <label for="sort">Сортировать по:</label>
        <select name="sort_by">
            <option value="date_added">Дате добавления</option>
            <option value="is_done">Статусу</option>
            <option value="description">Описанию</option>
        </select>
        <input type="submit" name="sort" value="Отсортировать">
    </form>
</div> <br>

<?php printTasks($myTasks, $userId, $user); ?>

<p><strong>Также, посмотрите, что от Вас требуют другие люди:</strong></p>

<?php printTasks($myAssignedTasks, $userId); ?>
</body>
</html>
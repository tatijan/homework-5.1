<?php
session_start();
include_once './config.php';
function login($login)
{
    $_SESSION['login'] = $login;
    redirect('index');
}
// redirect
function redirect($page)
{
    header("Location: $page.php");
    die;
}
// для вывода таблицы
function printTasks($tasks, $userId, $users = [])
{
    echo "<table>";
    echo "
        <tr>
            <th>Описание задачи</th>
            <th>Дата добавления</th>
            
            <th>Статус</th>
            <th></th>
            <th>Ответственный</th>
            <th>Автор</th>
            ";
    if (!empty($users)) {
        echo "<th>Закрепить задачу за пользователем</th>\n";
    }
    echo "</tr>\n";
    foreach ($tasks as $row) {
        echo "<tr>\n";
        echo "  <td>" . $row['description'] . "</td>\n";
        echo "  <td>" . $row['date_added'] . "</td>\n";
        echo "  <td>" . ($row['is_done'] ? "<span style='color: green;'>Выполнено</span>" : "<span style='color: orange;'>В процессе</span>") . "</td>\n";
        echo "  <td>
            <a href='?id=" . $row['id'] . "&action=edit'>Изменить</a>&nbsp;";
        if (empty($row['assigned_user_id']) || $row['assigned_user_id'] == $userId) {
            echo "<a href='?id=" . $row['id'] . "&action=done'>Выполнить</a>&nbsp;";
        }
        echo "<a href='?id=" . $row['id'] . "&action=delete'>Удалить</a>
        </td>\n";
        echo "  <td>";
        if (!empty($row['assigned_user_id'])) {
            echo $row['login'];
        } else {
            echo "Вы";
        }
        echo "</td>\n";
        echo "  <td>" . $row['author'] . "</td>\n";
        if (!empty($users)) {
            echo "  <td>";
            echo "<form method='POST'>";
            echo "  <select name='assigned_user_id'>";
            foreach ($users as $id => $login) {
                echo "<option value='user_" . $id . "_task_" . $row['id'] . "'>" . $login . "</option>";
            }
            echo "  </select>";
            echo "  <input type='submit' name='assign' value='Переложить ответственность' />";
            echo "</form>";
            echo "</td>\n";
        }
        echo "</tr>\n";
    }
    echo "</table>";
}
//--  выход
function logout()
{
    session_destroy();
    header("Location: ../index.php");
    die;
}
echo "<h4><a href='template/logout.php'> Log out</a></h4>";
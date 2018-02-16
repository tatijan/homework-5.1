<?php
require __DIR__.'/vendor/autoload.php';
$api = new \Yandex\Geo\Api();
// Можно искать по точке
// $api->setPoint(37.571309, 55.767190);
if (!empty($_POST['address'])) {
    $address = filter_input(INPUT_POST, "address", FILTER_SANITIZE_STRING);
    $api->setQuery($address);
// Настройка фильтров
    $api
        ->setLimit(50) // кол-во результатов
        ->setLang(\Yandex\Geo\Api::LANG_RU) // локаль ответа
        ->load();
    $response = $api->getResponse();
    $response->getFoundCount(); // кол-во найденных адресов
    $response->getQuery(); // исходный запрос
    $response->getLatitude(); // широта для исходного запроса
    $response->getLongitude();
    $collection = $response->getList();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Задание 5.1</title>
    <style type="text/css">
        td, th {
            border: 1px solid black;
            padding: 4px;
            text-align: center;
        }
        th {
            background-color: lightgrey;
        }
        table {
            border-collapse: collapse;
        }
    </style>
</head>
<body>
<form method="POST">
    <div>
        <h3>Введите адрес для определения координат</h3>
    </div>
    <div>
        <input type="text" name="address">
        <button type="submit">Найти</button>
    </div>
</form>
<h4>По Вашему запросу "<?= $response->getQuery();?>" определены координаты:</h4>
<?php if (isset($collection)):?>
    <table>
        <thead>
        <th>№</th>
        <th>Долгота</th>
        <th>Широта</th>
        </thead>
        <?php $i=1;
        foreach ($collection as $item):?>
            <tbody>
            <tr>
                <td><?= $i;?></td>
                <td><?= $item->getLongitude();?></td>
                <td><?= $item->getLatitude();?></td>
            </tr>
            </tbody>
            <?php $i++;
        endforeach ?>
    </table>
<?php endif ?>
</body>
</html>
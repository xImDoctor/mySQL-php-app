<?php

// подключение
require_once 'data/db-data.php'; // данные базы
$conn = new mysqli(HOST, USER, PASSW, DB_NAME);

// Проверка соединения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Функция для форматированного вывода таблицы
function formatTable($result) {
    $output = "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse:collapse;'>";
    $output .= "<tr>";
    foreach ($result->fetch_fields() as $field) {
        $output .= "<th>" . htmlspecialchars($field->name) . "</th>";
    }
    $output .= "</tr>";

    while ($row = $result->fetch_assoc()) {
        $output .= "<tr>";
        foreach ($row as $value) {
            $output .= "<td>" . htmlspecialchars($value) . "</td>";
        }
        $output .= "</tr>";
    }
    $output .= "</table>";
    return $output;
}

// Выполнение пользовательского запроса
$query = isset($_POST['query']) ? $_POST['query'] : '';
$output = '';

if (!empty($query)) {
    $result = $conn->query($query);
    if ($result) {
        if ($result instanceof mysqli_result) {
            // Запрос с возвратом результата
            $output = formatTable($result);
        } else {
            // Запрос без возврата результата
            $output = "Запрос успешно выполнен.";
        }
    } else {
        $output = "Ошибка выполнения запроса: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client6</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        textarea { width: 100%; height: 100px; }
        button { margin-top: 10px; padding: 10px 20px; }
        table { margin-top: 20px; width: 100%; text-align: left; }
        th, td { padding: 8px 12px; }
        th { background-color: #f4f4f4; }
    </style>
</head>
<body>
    <h1>Client6: Работа с базой данных consult_company</h1>
    <form method="POST">
        <label for="query">Введите SQL-запрос:</label>
        <textarea name="query" id="query"><?= htmlspecialchars($query) ?></textarea><br>
        <button type="submit">Выполнить</button>
    </form>
    <div>
        <h2>Результат:</h2>
        <?= $output ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>

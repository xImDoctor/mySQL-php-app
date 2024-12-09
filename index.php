<?php

// подключение
require_once 'data/db-data.php'; // данные базы
$connection = new mysqli(HOST, USER, PASSW, DB_NAME);

// Проверка соединения
if ($connection->connect_error)
    die("Ошибка подключения: " . $connection->connect_error);


/**
 * Функция для форматирования таблицы
 * @param mixed $result - результат, полученный в запросе.
 * @return string   - Возвращает строку с HTML-разметкой итоговой таблицы с данными.
 */
function formatTable($result)
{
    $output = "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse:collapse;'>";
    $output .= "<tr>";

    foreach ($result->fetch_fields() as $field)
        $output .= "<th>" . htmlspecialchars($field->name) . "</th>";
    $output .= "</tr>";

    while ($row = $result->fetch_assoc()) {
        $output .= "<tr>";
        foreach ($row as $value)
            $output .= "<td>" . htmlspecialchars($value) . "</td>";
        $output .= "</tr>";
    }

    $output .= "</table>";

    return $output;
}

// Выполнение запроса
$query = isset($_POST['query']) ? $_POST['query'] : "";
$output = "";

if (!empty($query)) {
    $result = $connection->query($query);

    if ($result) {

        if ($result instanceof mysqli_result)
            $output = formatTable($result);           // Запрос с возвратом результата
        else
            $output = "Запрос успешно выполнен.";             // Запрос без возврата результата

    } else
        $output = "Ошибка выполнения запроса: " . $connection->error;
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client6</title>

    <link rel="stylesheet" href="css/main-style.css">    
    
    <script>
        function clearResult() {
            document.getElementById("result").innerHTML = "";
        }
    </script>
</head>

<body>
    <h1>Client6: Работа с базой данных <?= DB_NAME ?></h1>
    <form method="POST">
        <label for="query">Введите SQL-запрос:</label>
        <textarea name="query" id="query" required><?= htmlspecialchars($query) ?></textarea><br>
        <button type="submit">Выполнить</button>
    </form>
    <div>
        <h2>Результат:</h2>
        <p><a href="javascript:void(0);" id="cleanse" onclick="clearResult()">Очистить</a></p>
        <div id="result"><?= $output ?></div>
    </div>
</body>

</html>

<?php
$connection->close();
?>
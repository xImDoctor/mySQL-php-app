<?php

// подключение
require_once 'data/db-data.php'; // данные базы
$connection = new mysqli(HOST, USER, PASSW, DB_NAME);

// Проверка соединения
if ($connection->connect_error)
    die("Ошибка подключения: " . $connection->connect_error);

// Инициализация сессии
session_start();

// Очистка данных сессии (query и output) при запросе
if (isset($_POST['clear']) && $_POST['clear'] == '1') {
    // Очистка значений сессии
    unset($_SESSION['output']);
    unset($_SESSION['query']);
}

// Функция для форматирования таблицы
function formatTable($result)
{
    $output = "<table>";
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
$output = '';

if (!empty($query)) {
    try {
        // Выполнение запроса с проверкой ошибок
        $result = $connection->query($query);

        if ($result) {
            if ($result instanceof mysqli_result)
                $_SESSION['output'] = formatTable($result);  // Запрос с возвратом результата
            else
                $_SESSION['output'] = "Запрос успешно выполнен.";  // Запрос без возврата результата
        }
    } catch (mysqli_sql_exception $e) {
        $_SESSION['output'] = "Ошибка выполнения запроса: " . $e->getMessage();  // Сохраняем ошибку в сессии
    }
} else {
    $_SESSION['output'] = '';
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
        // Функция очистки результата с помощью отправки формы на сервер
        function clearResult() {
            document.getElementById("clearForm").submit();
        }
    </script>
</head>

<body>
    <h1>Client6: Работа с базой данных <?= DB_NAME ?></h1>
    <form method="POST">
        <label for="query">Введите SQL-запрос:</label>
        <textarea name="query" id="query" required><?= htmlspecialchars($_SESSION['query'] ?? '') ?></textarea><br>
        <button type="submit">Выполнить</button>
    </form>
    
    <div class="result-block">

        <?php
            if (isset($_SESSION['output']) && $_SESSION['output'] !== "")
                include_once "result-block.php";
        ?>
    </div>
</body>

</html>

<?php
$connection->close();
?>

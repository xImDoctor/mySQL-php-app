<?php

// подключение
require_once 'data/db-data.php';
$connection = new mysqli(HOST, USER, PASSW, DB_NAME); // ООП вариант, mysqli_connect как процедурный стиль

if ($connection->connect_error)
    die("Ошибка подключения: " . $connection->connect_error);


session_start();    // сессия для хранения значений


/**
 * Функция для форматирования таблицы
 * @param mixed $result - Принимает набор данных от SQL.
 * @return string - Возвращает строку с HTML-разметкой итоговой таблицы
 */
function formatTable($result): string
{
    $output = "<table>";
    $output .= "<tr>";

    foreach ($result->fetch_fields() as $field) // метаданные результирующего набора
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

// переменные для запроса и вывода
$query = isset($_POST['query']) ? $_POST['query'] : "";
$_SESSION['query'] = $query; 
$output = '';


// обработка запроса
if (!empty($query)) {
    try {

        $result = $connection->query($query);   //м-но MYSQLI_USE_RESULT, по умолчанию STORE

        if ($result) {
            if ($result instanceof mysqli_result) // проверка на наличие результата (объект класса mysqli_result)
                $_SESSION['output'] = formatTable($result);
            else
                $_SESSION['output'] = "Запрос успешно выполнен, затронуто строк: " . $connection->affected_rows;
        }
    } catch (mysqli_sql_exception $e) {
        $_SESSION['output'] = "Ошибка выполнения запроса: " . $e->getMessage();  // Сохраняем ошибку в сессии
    }
} else
    $_SESSION['output'] = '';


?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client6: <?=DB_NAME?></title>

    <link rel="stylesheet" href="css/main-style.css">
    <script src="clear.js" defer></script>
</head>

<body>
    <h1>Client6: Работа с базой данных <?= DB_NAME ?></h1>
    <form method="POST">
        <label for="query">Введите SQL-запрос:</label>
        <textarea name="query" id="query" required><?= htmlspecialchars($_SESSION['query'] ?? '') ?></textarea><br>
        <button type="submit">Выполнить</button>
    </form>
    
    <!-- вывод результата -->
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

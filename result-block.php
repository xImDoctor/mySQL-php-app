
<h2>Результат:</h2>

<!-- Форма для очистки сессии -->
<form id="clearForm" method="POST">
    <input type="hidden" name="clear" value="1">
</form>
<p><a class="cleanse" href="javascript:void(0);" onclick="clearResult()">Очистить</a></p>

<?php
echo "<div id=\"result\">" . $_SESSION['output'] . "</div>";

?>
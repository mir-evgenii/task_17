<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli = mysqli_connect("localhost", "test", "123456", "files_download");
if (!$mysqli) echo mysqli_connect_error();

if ($_POST) {

    $hash = hash_file('md5', $_FILES['file']['tmp_name']);

    $result = mysqli_query($mysqli, 'SELECT * FROM Files WHERE hash = \''.$hash.'\' LIMIT 1');
    $obj = mysqli_fetch_object($result);
    if ($obj) {
        $html = "
        <html>
        <head>
            <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
            <title>Результат загрузки файла</title>
        </head>
            <body>
            <h1>Результат загрузки файла</h1>
            <p>Данный файл уже был загружен!</p>
            <p>Параметры ранее загруженного файла:</p>
            <p>Имя: {$obj->name}</p>
            <p>Описание: {$obj->description}</p>
            <p>Время загрузки: {$obj->datetime}</p>
            <p><a href = 'http://localhost:8000/'>Назад</a></p>
        </body>
        </html>";

        echo $html;
    } else {

        $stmt = mysqli_prepare($mysqli, "INSERT INTO Files(hash, name, description, datetime) VALUES (?, ?, ?, ?)");

        $name = $_FILES['file']['name'];
        $desc = $_POST['desc'];
        $date = date('Y-m-d H:i:s');

        mysqli_stmt_bind_param($stmt, 'ssss', $hash, $name, $desc, $date);
        mysqli_stmt_execute($stmt);

        $html = "
            <html>
            <head>
                <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
                <title>Результат загрузки файла</title>
            </head>
            <body>
                <h1>Результат загрузки файла</h1>
                <p>Файл загружен!</p>
                <p><a href = 'http://localhost:8000/'>Назад</a></p>
            </body>
            </html>";

        echo $html;
    }
    
} else {
    $html = "
    <html>
    <head>
     <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
     <title>Загрузка файла</title>
    </head>
    <body>
     <h1>Загрузка файла</h1>
     <form enctype='multipart/form-data' method='post'>
        <input type='hidden' name='MAX_FILE_SIZE' value='30000'>
        <input type='file' name='file'><br><br>
        <textarea name='desc' cols='40' rows='3'></textarea><br><br>
        <input type='submit' value='Отправить'>
    </form> 
    </body>
   </html>";

    echo $html;
}

<?php
if (isset($_REQUEST[session_name()])) session_start();
$auth = $_SESSION['auth'];
$name_user = $_SESSION['name_user'];
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <?php include 'head.php'; ?>
    <title>Архиереи Барышской епархии</title>

    <link rel="stylesheet" href="/Index1.css">
    <link rel="stylesheet" href="/header.css">
    <link rel="stylesheet" href="/all.min.css">
</head>

<body>

<div class="page-wrapper">

    <?php include 'golova.php'; ?>
    <?php include 'menu.php'; ?>
    <?php include 'content.php'; ?>

    <div id="osnovnoe" class="main-column card news-list-block">

        <h1 class="section-title">Архиереи Барышской епархии</h1>

        <!-- КАРТОЧКА В СТИЛЕ НОВОГО САЙТА -->
        <div class="news-item news-entry">

            <div class="news-entry__frame">

                <div class="news-entry__content">

                    <div class="news-entry__text">

                        <div style="display:flex; gap:25px; align-items:flex-start; flex-wrap:wrap;">

                            <!-- ФОТО -->
                            <div class="news-entry__image">
                                <a href="IMG/filaret.jpg" rel="example_group">
                                    <img src="IMG/filaret.jpg"
                                         alt="Епископ Филарет"
                                         title="Епископ Филарет"
                                         style="width:200px; border-radius:8px; border:1px solid #d1d1d1;
                                                padding:6px; box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                                </a>
                            </div>

                            <!-- ТЕКСТОВЫЙ БЛОК -->
                            <div style="flex:1; min-width:260px;">

                                <!-- Имя архиерея -->
                                <a href="filaret.php"
                                   class="news-entry__title"
                                   style="display:block; font-size:22px; font-weight:600; margin-bottom:10px;">
                                    Епископ Филарет (Коньков) — 2012–2025 гг.
                                </a>

                                <!-- Архив -->
                                <div class="news-entry__tags" style="margin-top:15px;">
                                    <h2 style="font-size:18px; font-weight:600; margin-bottom:10px;">
                                        Архив:
                                    </h2>

                                    <ul style="list-style:none; padding:0; margin:0; line-height:1.7;">

                                        <li>
                                            <a href="slovo_filaret.php" class="btn">
                                                Слово архипастыря
                                            </a>
                                        </li>

                                        <li style="margin-top:10px;">
                                            <a href="sluzhenie_filaret.php" class="btn">
                                                Архипастырское служение
                                            </a>
                                        </li>

                                    </ul>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <?php include 'footer.php'; ?>

</div>

</body>
</html>
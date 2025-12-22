<?php
if (isset($_REQUEST[session_name()])) session_start();
$auth = $_SESSION['auth'];
$name_user = $_SESSION['name_user'];
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<?php include 'head.php'; ?>
<title>Управление</title>

<link rel="stylesheet" href="/Index1.css">
<link rel="stylesheet" href="/header.css">
<link rel="stylesheet" href="/all.min.css">
</head>

<body>

<div class="page-wrapper">

<?php include 'golova.php'; ?>
<?php $upravlenie = true; ?>
<?php include 'menu.php'; ?>
<?php include 'content.php'; ?>

<div id="osnovnoe" class="main-column card news-list-block">

<h1 class="section-title">Управление</h1>


<!-- ========================= КАРТОЧКА 1 ========================= -->
<div class="news-item news-entry">
    <div class="news-entry__frame">
        <div class="news-entry__content">

            <div class="news-entry__text" style="display:flex; gap:20px; align-items:flex-start;">

                <a href="IMG/filaret.jpg" rel="example_group">
                    <img src="IMG/mitr.webp" alt="Митрополит Лонгин"
                         title="Митрополит Лонгин"
                         style="width:200px; border-radius:8px; border:1px solid #ddd;
                                padding:6px; box-shadow:0 4px 12px rgba(0,0,0,0.15);">
                </a>

                <div>
                    <p><b>Митрополит Лонгин (Корчагин)</b> — временно управляющий Барышской епархией.</p>
                </div>

            </div>

        </div>
    </div>
</div>


<!-- ========================= КАРТОЧКА 2 ========================= -->
<div class="news-item news-entry">
    <div class="news-entry__frame">
        <div class="news-entry__content">

            <div class="news-entry__text" style="display:flex; gap:20px; align-items:flex-start;">

                <a href="IMG/o_alex_max.jpg" rel="example_group">
                    <img src="IMG/o_alex_min.jpg" alt="Протоиерей Александр Егоров"
                         title="Протоиерей Александр Егоров"
                         style="width:200px; border-radius:8px; border:1px solid #ddd;
                                padding:6px; box-shadow:0 4px 12px rgba(0,0,0,0.15);">
                </a>

                <div>
                    <p><b>Протоиерей Александр Егоров</b> — секретарь епархиального управления.</p>
                    <p>Тел.: +7 991 461 54 88.</p>
                </div>

            </div>

        </div>
    </div>
</div>


<!-- ========================= КАРТОЧКА 3 ========================= -->
<div class="news-item news-entry">
    <div class="news-entry__frame">
        <div class="news-entry__content">

            <div class="news-entry__text">
                <p><b>Светлана Валерьевна Егорова</b> — главный бухгалтер епархиального управления.</p>
            </div>

        </div>
    </div>
</div>


<!-- ========================= КАРТОЧКА 4 ========================= -->
<div class="news-item news-entry">
    <div class="news-entry__frame">
        <div class="news-entry__content">

            <div class="news-entry__text" style="display:flex; gap:20px; align-items:flex-start; flex-wrap:wrap;">

                <a href="IMG/o_daniil_max.jpg" rel="example_group">
                    <img src="IMG/o_daniil_min_1.jpg" alt="Иеромонах Даниил"
                         title="Иеромонах Даниил (Селеверстов)"
                         style="width:200px; border-radius:8px; border:1px solid #ddd;
                                padding:6px; box-shadow:0 4px 12px rgba(0,0,0,0.15);">
                </a>

                <div>
                    <p><b>иерей Евгений Трофимов</b> — благочинный I округа Епархии.</p>
                    <p>Тел.: 8-908-153-63-34.</p>
                    <p>E-mail помощника:
                        <a href="mailto:info@barysh-eparhia.ru">trofimove878@gmail.com</a>
                    </p>
                </div>

            </div>

        </div>
    </div>
</div>


<!-- ========================= КАРТОЧКА 5 ========================= -->
<div class="news-item news-entry">
    <div class="news-entry__frame">
        <div class="news-entry__content">

            <div class="news-entry__text" style="display:flex; gap:20px; align-items:flex-start; flex-wrap:wrap;">

                <a href="IMG/o_pavel_bobrov_max.jpg" rel="example_group">
                    <img src="IMG/o_pavel_bobrov_min.jpg" alt="Протоиерей Павел Бобров"
                         title="Протоиерей Павел Бобров"
                         style="width:200px; border-radius:8px; border:1px solid #ddd;
                                padding:6px; box-shadow:0 4px 12px rgba(0,0,0,0.15);">
                </a>

                <div>
                    <p><b>Протоиерей Павел Бобров</b> — благочинный II округа Епархии.</p>
                    <p>Тел.: 8-927-631-68-58.</p>
                    <p>E-mail помощника: <a href="mailto:seminarist173@gmail.com">seminarist173@gmail.com</a></p>
                </div>

            </div>

        </div>
    </div>
</div>


<!-- ========================= КАРТОЧКА 6 ========================= -->
<div class="news-item news-entry">
    <div class="news-entry__frame">
        <div class="news-entry__content">

            <div class="news-entry__text" style="display:flex; gap:20px; align-items:flex-start; flex-wrap:wrap;">

                <a href="IMG/o_iosif.jpg" rel="example_group">
                    <img src="IMG/o_iosif_mini.jpg" alt="Иеромонах Иосиф"
                         title="Иеромонах Иосиф (Пашенцев)"
                         style="width:200px; border-radius:8px; border:1px solid #ddd;
                                padding:6px; box-shadow:0 4px 12px rgba(0,0,0,0.15);">
                </a>

                <div>
                    <p><b>Иеромонах Иосиф (Пашенцев)</b> — благочинный III округа Епархии.</p>
                    <p>Тел.: 8-917-059-48-66.</p>
                    <p>E-mail:
                        <a href="mailto:igor_paschenzeff@mail.ru">igor_paschenzeff@mail.ru</a>
                    </p>
                </div>

            </div>

        </div>
    </div>
</div>


<!-- ========================= КАРТОЧКА 7 ========================= -->
<div class="news-item news-entry">
    <div class="news-entry__frame">
        <div class="news-entry__content">

            <div class="news-entry__text" style="display:flex; gap:20px; align-items:flex-start; flex-wrap:wrap;">

                <a href="IMG/filkin.jpg" rel="example_group">
                    <img src="IMG/filkin_mini.jpg" alt="Протоиерей Андрей Филькин"
                         title="Протоиерей Андрей Филькин"
                         style="width:200px; border-radius:8px; border:1px solid #ddd;
                                padding:6px; box-shadow:0 4px 12px rgba(0,0,0,0.15);">
                </a>

                <div>
                    <p><b>Протоиерей Андрей Филькин</b> — благочинный IV округа Епархии.</p>
                    <p>Тел.: 8-937-036-43-33.</p>
                    <p>E-mail:
                        <a href="mailto:blago3pavlovka@mail.ru">blago3pavlovka@mail.ru</a>
                    </p>
                    <p>Сайт:
                        <a href="http://pavlovka.cerkov.ru" target="_blank">pavlovka.cerkov.ru</a>
                    </p>
                </div>

            </div>

        </div>
    </div>
</div>


<!-- ========================= КАРТОЧКА 8 ========================= -->
<div class="news-item news-entry">
    <div class="news-entry__frame">
        <div class="news-entry__content">

            <div class="news-entry__text" style="display:flex; gap:20px; align-items:flex-start; flex-wrap:wrap;">

                <a href="IMG/о_nik_liv_max.jpg" rel="example_group">
                    <img src="IMG/о_nik_liv_min.jpg" alt="Протоиерей Николай Леванов"
                         title="Протоиерей Николай Леванов"
                         style="width:200px; border-radius:8px; border:1px solid #ddd;
                                padding:6px; box-shadow:0 4px 12px rgba(0,0,0,0.15);">
                </a>

                <div>
                    <p><b>Протоиерей Николай Леванов</b> — благочинный V округа Епархии.</p>
                    <p>Тел.: 8-927-823-52-33.</p>
                    <p>E-mail:
                        <a href="mailto:nikolay.levanov@mail.ru">nikolay.levanov@mail.ru</a>
                    </p>
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
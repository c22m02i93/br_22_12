<?
if (isset($_REQUEST[session_name()])) session_start();
$auth = $_SESSION['auth'];
$name_user = $_SESSION['name_user'];
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <? include 'head.php'; ?>
    <title>История епархии</title>

    <link rel="stylesheet" href="/Index1.css">
    <link rel="stylesheet" href="/header.css">
    <link rel="stylesheet" href="/all.min.css">
</head>

<body>

<div class="page-wrapper">

    <? include 'golova.php'; ?>
    <? include 'menu.php'; ?>
    <? include 'content.php'; ?>

    <div id="osnovnoe" class="main-column card news-list-block">

        <h1 class="section-title">История епархии</h1>

        <?
        // Текст истории
        $text = 'Барышская епархия входит в состав Симбирской митрополии.
Образована решением Священного Синода от |||26 июля 2012 г.||| (журнал № 61) путем выделения из состава Симбирской епархии. Включена в состав Симбирской митрополии.
Правящему архиерею Синод постановил иметь титул Барышский и Инзенский.
Объединяет приходы в административных границах Базарносызганского, Барышского, Инзенского, Николаевского, Павловского, Радищевского, Старокулаткинского районов Ульяновской области.
Первым правящим ариереем Барышской епархии избран игумен Филарет (Коньков), наместник Жадовского мужского монастыря.
||| 28 октября |||за Божественной литургией, которую возглавил Святейший Патриарх Московский и всея Руси Кирилл,  в Никольском соборе Никольского Черноостровского монастыря (Калужская обл.) архимандрит Филарет рукоположен во епископа Барышского и Инзенского.';

        // Паттерны оформления (новые стили)
        $patterns = [
            '/(?:\/{3})(.+)(?:\/{3})/U',
            '/(?:\|{3})(.+)(?:\|{3})/U',
            '/(?:\{{3})(http:\/\/[^\s\[<\(\)\|]+)(?:\}{3})-(?:\{{3})([^}]+)(?:\}{3})/i',
            '/(?:\{{3})(http:\/\/[^\s\[<\(\)\|]+)(?:\}{3})/i',
            '/(?:\{{3})([_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,3}))(?:\}{3})/',
            '/\n/',
            '/@R(\d+)[-]?([^@]*)@/',
            '/@L(\d+)[-]?([^@]*)@/',
        ];

        $replace = [
            '<i>${1}</i>',
            '<b>${1}</b>',
            '<a href="${1}" target="_blank">${2}</a>',
            '<a href="${1}" target="_blank">${1}</a>',
            '<a href="mailto:${1}">${1}</a>',
            '</p><p>',
            '<span class="photos"><a href="FOTO/${1}.jpg" rel="example_group">
                <img class="img-right" src="FOTO_MINI/${1}.jpg" alt="${2}" title="${2}" />
             </a></span>',
            '<span class="photos"><a href="FOTO/${1}.jpg" rel="example_group">
                <img class="img-left" src="FOTO_MINI/${1}.jpg" alt="${2}" title="${2}" />
             </a></span>',
        ];

        $text = preg_replace($patterns, $replace, $text);
        ?>

        <!-- КАРТОЧКА В СТИЛЕ НОВОГО САЙТА -->
        <div class="news-item news-entry">

            <div class="news-entry__frame">

                <div class="news-entry__content">

                    <div class="news-entry__text">
                        <p><?= $text ?></p>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <? include 'footer.php'; ?>

</div>

</body>
</html>
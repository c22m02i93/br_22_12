<?php
$vars = array(
    'arhi',
    'slovo_padre',
    'raspisanie',
    'new',
    'anons',
    'pub',
    'histor',
    'barysh',
    'upravlenie',
    'otdel',
    'klir',
    'mon',
    'prihods',
    'old_prihods',
    'map',
    'video',
    'gazeta',
    'radio',
    'kontakt',
    'top',
    'tip'
);

foreach ($vars as $v) {
    if (!isset($$v))
        $$v = '';
}
?>

<div class="main-nav">
    <ul class="nav-level-1">

        <!-- АРХИЕРЕЙ -->
        <li class="has-sub">
            <a class="nav-main-link">Архиерей</a>
            <ul class="nav-level-2">

                <li <?= $arhi == 'yes' ? 'class="active"' : '' ?>>
                    <a <?= $arhi == 'yes' ? '' : 'href="arhierei.php"' ?>>Биография</a>
                </li>

                <li <?= $slovo_padre == 'yes' ? 'class="active"' : '' ?>>
                    <a <?= $slovo_padre == 'yes' ? '' : 'href="slovo_padre.php"' ?>>Слово архипастыря</a>
                </li>

                <li <?= $raspisanie == 'yes' ? 'class="active"' : '' ?>>
                    <a <?= $raspisanie == 'yes' ? '' : 'href="raspisanie.php"' ?>>Служение</a>
                </li>

            </ul>
        </li>


        <!-- НОВОСТИ -->
        <li class="has-sub">
            <a class="nav-main-link">Новости</a>
            <ul class="nav-level-2">

                <li <?= $new == 'yes' ? 'class="active"' : '' ?>>
                    <a <?= $new == 'yes' ? '' : 'href="news.php"' ?>>Новости епархии</a>
                </li>

                <li <?= $anons == 'yes' ? 'class="active"' : '' ?>>
                    <a <?= $anons == 'yes' ? '' : 'href="anons.php"' ?>>Анонсы и объявления</a>
                </li>

                <li <?= $pub == 'yes' ? 'class="active"' : '' ?>>
                    <a <?= $pub == 'yes' ? '' : 'href="pub.php"' ?>>Публикации</a>
                </li>

            </ul>
        </li>


        <!-- ДОКУМЕНТЫ -->
        <li class="has-sub">
            <a class="nav-main-link">Документы</a>
            <ul class="nav-level-2">

                <li <?= $tip == 'ukaz' ? 'class="active"' : '' ?>>
                    <a <?= $tip == 'ukaz' ? '' : 'href="doks.php?tip=ukaz"' ?>>Указы</a>
                </li>

                <li <?= $tip == 'raspor' ? 'class="active"' : '' ?>>
                    <a <?= $tip == 'raspor' ? '' : 'href="doks.php?tip=raspor"' ?>>Распоряжения</a>
                </li>

                <li <?= $tip == 'cirk' ? 'class="active"' : '' ?>>
                    <a <?= $tip == 'cirk' ? '' : 'href="doks.php?tip=cirk"' ?>>Циркуляры</a>
                </li>

                <li <?= $tip == 'udostoverenie' ? 'class="active"' : '' ?>>
                    <a <?= $tip == 'udostoverenie' ? '' : 'href="doks.php?tip=udostoverenie"' ?>>Удостоверения</a>
                </li>

            </ul>
        </li>


        <!-- ЕПАРХИЯ -->
        <li class="has-sub">
            <a class="nav-main-link">Епархия</a>
            <ul class="nav-level-2">

                <li <?= $histor == 'yes' ? 'class="active"' : '' ?>>
                    <a <?= $histor == 'yes' ? '' : 'href="histor.php"' ?>>История</a>
                </li>

                <li <?= $barysh == 'yes' ? 'class="active"' : '' ?>>
                    <a <?= $barysh == 'yes' ? '' : 'href="barysh.php"' ?>>Архиереи Барышской епархии</a>
                </li>

                <li <?= $upravlenie == 'yes' ? 'class="active"' : '' ?>>
                    <a <?= $upravlenie == 'yes' ? '' : 'href="upravlenie.php"' ?>>Управление</a>
                </li>

                <li <?= $otdel == 'yes' ? 'class="active"' : '' ?>>
                    <a <?= $otdel == 'yes' ? '' : 'href="otdel.php"' ?>>Отделы</a>
                </li>

                <li <?= $klir == 'yes' ? 'class="active"' : '' ?>>
                    <a <?= $klir == 'yes' ? '' : 'href="klir.php"' ?>>Духовенство</a>
                </li>

            </ul>
        </li>


        <!-- ПРИХОДЫ -->
        <li class="has-sub">
            <a class="nav-main-link">Приходы</a>
            <ul class="nav-level-2">

                <li <?= $mon == 'yes' ? 'class="active"' : '' ?>>
                    <a <?= $mon == 'yes' ? '' : 'href="mon.php"' ?>>Жадовский монастырь</a>
                </li>

                <li <?= $prihods == 'yes' ? 'class="active"' : '' ?>>
                    <a <?= $prihods == 'yes' ? '' : 'href="prihods.php"' ?>>Действующие приходы</a>
                </li>

                <li <?= $old_prihods == 'yes' ? 'class="active"' : '' ?>>
                    <a <?= $old_prihods == 'yes' ? '' : 'href="old_prihods.php"' ?>>Разрушенные храмы</a>
                </li>

                <li <?= $map == 'yes' ? 'class="active"' : '' ?>>
                    <a <?= $map == 'yes' ? '' : 'href="map.php"' ?>>Карта приходов</a>
                </li>

            </ul>
        </li>


        <!-- МЕДИА -->
        <li class="has-sub">
            <a class="nav-main-link">Медиа</a>
            <ul class="nav-level-2">

                <li <?= $video == 'yes' ? 'class="active"' : '' ?>>
                    <a <?= $video == 'yes' ? '' : 'href="video.php"' ?>>Видео</a>
                </li>

                <li <?= $gazeta == 'yes' ? 'class="active"' : '' ?>>
                    <a <?= $gazeta == 'yes' ? '' : 'href="gazeta.php"' ?>>Газета «Моя епархия»</a>
                </li>

                <li <?= $radio == 'yes' ? 'class="active"' : '' ?>>
                    <a <?= $radio == 'yes' ? '' : 'href="sv_vecher.php"' ?>>Радио «Светлый вечер»</a>
                </li>

            </ul>
        </li>


        <!-- КОНТАКТЫ -->
        <li <?= $kontakt == 'yes' ? 'class="active"' : '' ?>>
            <a <?= $kontakt == 'yes' ? '' : 'href="kontakt.php"' ?>>Контакты</a>
        </li>

        <!-- ЧИТАЕМОЕ -->
        <li <?= $top == 'yes' ? 'class="active"' : '' ?>>
            <a <?= $top == 'yes' ? 'style="color:#b33;opacity:.8"' : 'href="top.php"' ?>>Читаемое</a>
        </li>

    </ul>
</div>

<style>
   .main-nav {
    background: #fff;
    padding: 12px 0;
    border-bottom: 1px solid #eee;
}

.nav-level-1 {
    list-style: none;
    margin: 0;
    padding: 0 20px;
    display: flex;
    gap: 28px;
    align-items: center;
    font-weight: 600;
}

.nav-level-1 > li {
    position: relative;
}

.nav-level-1 > li > a.nav-main-link {
    font-size: 1.05rem;
    color: #5c2f2f;
    cursor: pointer;
    padding: 6px 0;
    display: inline-block;
}

.nav-level-1 > li > a.nav-main-link:hover {
    color: #9b4747;
}

/* Подменю */
.nav-level-2 {
    display: none;
    position: absolute;
    left: 0;
    top: 34px;
    min-width: 220px;
    background: #fff;
    padding: 10px 0;
    list-style: none;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    border: 1px solid #f0e8e8;
    z-index: 50;
}

.has-sub:hover .nav-level-2 {
    display: block;
}

.nav-level-2 li a {
    display: block;
    padding: 8px 18px;
    color: #6c2f2f;
    font-size: 0.95rem;
}

.nav-level-2 li a:hover {
    background: #f7f3f3;
    color: #9b4747;
}

.nav-level-2 li.active > a {
    font-weight: 700;
    color: #8f3d3d;
}

/* Активный пункт верхнего меню */
.nav-level-1 > li.active > a {
    color: #b04040 !important;
}
</style>
<!-- ===========================
      HEAD (оптимизированный)
=========================== -->

<meta charset="UTF-8">

<meta name="keywords" content="Барышская епархия, Русская Православная Церковь, Симбирсая митрополия, епархия, Барыш, Ульяновск, Ульяновская область">

<!-- Основные стили сайта -->
<link rel="stylesheet" href="/style.css">
<link rel="stylesheet" href="/styles.css">
<link rel="stylesindex" href="/index1.css">

<!-- Preload локальных скриптов -->
<link rel="preload" href="/js/jquery.min.js" as="script">
<link rel="preload" href="/js/baguetteBox.min.js" as="script">
<link rel="preload" href="/css/baguetteBox.min.css" as="style">

<!-- Favicon -->
<link rel="icon" href="/favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">

<!-- baguetteBox (локально) -->
<link rel="stylesheet" href="/css/baguetteBox.min.css">

<!-- jQuery (локально) -->
<script src="/js/jquery.min.js" defer></script>

<!-- baguetteBox (локально) -->
<script src="/js/baguetteBox.min.js" defer></script>

<!-- Font Awesome (CDN – только webfonts) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer">

<!-- RSS -->
<link rel="alternate" type="application/rss+xml" title="RSS" href="/rss.php">

<?php list($msec,$sec)=explode(" ",microtime()); $mTimeStart=$sec+$msec; ?>

<!-- ===========================
      ХЕДЕР
=========================== -->

<div class="head" style="width:100%; background:#fff; border-bottom:1px solid #d7d7d7;">

    <div style="width:1100px; margin:0 auto;">

        <!-- Верхняя строка -->
        <div style="
            width:100%;
            padding:10px 0;
            display:flex;
            justify-content:space-between;
            align-items:center;
            font-family:'Segoe UI Light', arial;
        ">
            <div style="color:#999; font-size:14px; white-space:nowrap;">
                Русская Православная Церковь – Симбирская митрополия
            </div>

            <div style="display:flex; align-items:center; gap:15px; font-size:16px;">
                <a href="/rss.php" style="color:#666;"><i class="fa-solid fa-square-rss"></i></a>
                <a href="https://pda.barysh-eparhia.ru/" style="color:#666;"><i class="fa-solid fa-mobile-screen"></i></a>
                <a href="https://vk.com/barysheparhia" style="color:#666;"><i class="fa-brands fa-vk"></i></a>

                <!-- Поиск -->
                <a href="javascript:void(0);" onclick="openSearch()" style="color:#666;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </a>
            </div>
        </div>

        <!-- Лого + Название -->
        <div style="display:flex; align-items:center; gap:20px; padding-bottom:10px;">
            <a href="/"><img src="/IMG/logo.png" style="height:70px;" loading="lazy"></a>

            <div style="display:flex; flex-direction:column;">
                <div style="
                    font-family:'Segoe UI Light', arial;
                    font-size:38px;
                    color:#444;
                    letter-spacing:3px;
                ">
                    БАРЫШСКАЯ ЕПАРХИЯ
                </div>

                <div style="
                    font-family:'Segoe UI Light', arial;
                    color:#999;
                    font-size:14px;
                    margin-top:5px;
                ">
                    По благословению митрополита Симбирского и Новоспасского Лонгина, <br>временно управляющего Барышской епархией
                </div>
            </div>
        </div>

        <div style="border-top:1px solid #d7d7d7; margin-bottom:10px;"></div>

        <!-- Меню -->
    </div>
</div>

<!-- ===========================
      ВСПЛЫВАЮЩЕЕ ОКНО ПОИСКА
=========================== -->

<div id="search-popup" style="
    display:none;
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    background:rgba(0,0,0,0.55);
    z-index:9999;
">
    <div style="
        width:400px;
        background:#fff;
        padding:20px;
        border-radius:10px;
        margin:120px auto;
        text-align:center;
        font-family:Arial;
        box-shadow:0 0 20px rgba(0,0,0,0.3);
    ">
        <div style="font-size:20px; margin-bottom:10px;">Поиск по сайту</div>

        <form action="/search.php">
            <input type="text" name="q" placeholder="Введите запрос..."
                style="width:90%; padding:8px; margin-bottom:10px; font-size:16px;">
        </form>

        <button onclick="closeSearch()" 
                style="padding:8px 18px; border:none; background:#444; color:#fff; border-radius:6px; cursor:pointer;">
            Закрыть
        </button>
    </div>
</div>

<!-- Стили меню (оставил как есть) -->
<style>
/* ...оставлено без изменений... */
</style>

<!-- ===========================
      JS функционал
=========================== -->

<script defer>
function openSearch() {
    document.getElementById("search-popup").style.display = "block";
}
function closeSearch() {
    document.getElementById("search-popup").style.display = "none";
}
document.addEventListener("DOMContentLoaded", function() {
    if (document.querySelector('.gallery')) {
        baguetteBox.run('.gallery');
    }
});
</script>
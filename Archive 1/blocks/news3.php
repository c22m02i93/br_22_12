<?php
// TAB интерфейс: Новости / Публикации
?>

<div class="tabs-wrapper">

<div class="tabs-block">

    <div class="tabs-header">
        <div class="tab-btn active" data-tab="news">Новости епархии</div>
        <div class="tab-btn" data-tab="pubs">Публикации</div>
    </div>

    <div class="tabs-content">

        <!-- TAB 1: Новости -->
        <div class="tab-pane active" id="tab-news">
            <?php
            $news_all = mysql_query("
                SELECT *
                FROM host1409556_barysh.news_eparhia
                ORDER BY data DESC
                LIMIT 3
            ");

            while ($news = mysql_fetch_array($news_all)) {
                include __DIR__.'/news_card.php';
            }
            ?>
        </div>

        <!-- TAB 2: Публикации -->
        <div class="tab-pane" id="tab-pubs">
            <?php
            $pubs_all = mysql_query("
                SELECT *
                FROM host1409556_barysh.publikacii
                ORDER BY data DESC
                LIMIT 3
            ");

            while ($pub = mysql_fetch_array($pubs_all)) {
                include __DIR__.'/pub_card.php';
            }
            ?>
        </div>

    </div>
</div>

</div> <!-- /tabs-wrapper -->

<style>
/* Внешний контейнер — фикс от наложения float */
.tabs-wrapper {
    clear: both;
    width: 100%;
    margin-top: 25px;
    position: relative;
    z-index: 5;
}

.tabs-block {
    margin-bottom: 25px;
}

/* Заголовки вкладок */
.tabs-header {
    display: flex;
    border-bottom: 1px solid #e0e0e0;
    margin-bottom: 15px;
    position: relative;
    z-index: 10;
    background: #fff;
}

.tab-btn {
    padding: 10px 20px;
    cursor: pointer;
    font-size: 20px;
    color: #555;
}

.tab-btn.active {
    font-weight: bold;
    border-bottom: 3px solid #0057d9;
    color: #000;
}

/* Контент вкладок */
.tab-pane {
    display: none;
}

.tab-pane.active {
    display: block;
}
.tabs-wrapper {
    clear: left;
    width: 100%;
    margin-top: 25px;
    position: relative;
    z-index: 2;
}


/* Карточки */
.news-card {
    width: 100%;
    display: flex;
    background: #fff;
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 15px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.10);
    transition: 0.25s;
}

.news-card:hover {
    box-shadow: 0 6px 18px rgba(0,0,0,0.18);
}

.news-card-img img {
    width: 180px;
    height: 120px;
    object-fit: cover;
    border-radius: 8px;
    margin-right: 15px;
}

.news-card-body {
    flex: 1;
}

.news-card-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 6px;
}

.news-card-meta {
    font-size: 14px;
    color: #999;
    margin-bottom: 6px;
}

.news-card-meta img {
    width: 16px;
    opacity: 0.7;
}

.news-card-text {
    font-size: 15px;
    color: #444;
    line-height: 1.4em;
}

.views {
    margin-left: 10px;
}
</style>

<script>
// Переключение вкладок
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {

        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const target = btn.dataset.tab;

        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('active');
        });

        document.querySelector('#tab-' + target).classList.add('active');
    });
});
</script>
<?php
// ===== БАННЕРЫ (современный стиль, ровные карточки) =====
?>



<div class="banner-card">
    <a href="/prihod.php?id=21">
        <img src="/IMG/glotovka.png" alt="Боголюбово">
    </a>
</div>

<div class="banner-card">
    <a href="/saints.php">
        <img src="/IMG/saints.png" alt="Святые">
    </a>
</div>

<div class="banner-card">
    <a href="/hod.php">
        <img src="/IMG/hod.png" alt="Крестный ход">
    </a>
</div>

<div class="banner-card">
    <a href="http://ekzeget.ru/" target="_blank">
        <img src="/IMG/ekzeget.png" alt="Экзегет">
    </a>
</div>


<style>
.banner-card {
    width: 100%;
    background: #fff;
    border-radius: 14px;
    padding: 12px;
    margin-bottom: 18px;
    text-align: center;
    box-shadow: 0 4px 14px rgba(0,0,0,0.12);
    transition: 0.25s;
}

.banner-card:hover {
    box-shadow: 0 6px 18px rgba(0,0,0,0.19);
}

.banner-card img {
    width: 100%;
    border-radius: 10px;
}

.banner-text {
    font-size: 16px;
    color: #0044aa;
    font-weight: 600;
}
</style>
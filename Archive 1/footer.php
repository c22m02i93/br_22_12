<a id="toTop" href="#">?</a>

<div id="footer">
  <div class="footer-shell">
    <div class="footer-inner">

      <div class="footer-grid">

        <div class="footer-col">
          <h3>Архиерей</h3>
          <a href="arhierei.php">Биография</a>
          <a href="slovo_padre.php">Слово архипастыря</a>
          <a href="raspisanie.php">Служение</a>
        </div>

        <div class="footer-col">
          <h3>Новости</h3>
          <a href="news.php">Новости епархии</a>
          <a href="anons.php">Анонсы</a>
          <a href="pub.php">Публикации</a>
        </div>

        <div class="footer-col">
          <h3>Документы</h3>
          <a href="doks.php?tip=ukaz">Указы</a>
          <a href="doks.php?tip=raspor">Распоряжения</a>
          <a href="doks.php?tip=cirk">Циркуляры</a>
          <a href="doks.php?tip=udostoverenie">Удостоверения</a>
        </div>

        <div class="footer-col">
          <h3>Епархия</h3>
          <a href="histor.php">История</a>
          <a href="upravlenie.php">Управление</a>
          <a href="otdel.php">Отделы</a>
        </div>

         <div class="footer-col">
          <h3>Приходы</h3>
          <a href="mon.php">Жадовский монастырь</a>
          <a href="prihods.php">Действующие приходы</a>
          <a href="old_prihods.php">Разрушенные храмы</a>
          <a href="map.php">Карта приходов</a>
        </div>

        
        <div class="footer-col">
          <h3><a href="kontakt.php">Контакты</a></h3>
        </div>

      </div>

      <div class="footer-banners">


        <a href="http://www.patriarchia.ru/index.html"><img src="http://www.patriarchia.ru/images/patr_banner_88.gif"></a>
        <a href="https://mitropolia-simbirsk.ru/" target="_blank"><img src="IMG/simbmitropolia.png"></a>
        <a href="http://www.ekzeget.ru" target="_blank"><img src="http://www.zeget.ru/IMG/banner.gif"></a>
        <a href="http://-./" target="_blank"><img src="/IMG/386.jpg"></a>
        <a href="http://vsehsvjatyh-glotovka.prihod.ru/" target="_blank"><img src="http://prihod.ru/pravbanners/vsehsvjatyh-glotovka.png"></a>

        <!-- 24LOG block fully removed as requested -->
      </div>

      <div class="footer-bottom">
          <span class="footer-bottom-text">© Барышская епархия, 2012 –  <?php echo date("Y"); ?> гг. · Создание сайта:<a href="mailto:zhidkoff@list.ru"> Сергей Жидков </a></span>
        <?php
          list($msec,$sec)=explode(chr(32),microtime());
          $skor_gen=(round(($sec+$msec)-$mTimeStart,4));
          echo '<span class="footer-gen-time"> Страница сгенерирована за  <b>'.$skor_gen.'</b> с.</span>';
        ?>
      </div>

    </div>
  </div>
</div>



<!--/noindex-->
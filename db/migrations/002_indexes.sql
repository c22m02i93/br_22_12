-- Indexes to speed up date filtering and full-text search.
-- Run after 001_utf8mb4.sql so fulltext indexes use utf8mb4 metadata.

ALTER TABLE `admin`
  ADD INDEX `idx_admin_login` (`login`);

ALTER TABLE `anons`
  ADD INDEX `idx_anons_data` (`data`),
  ADD FULLTEXT `idx_anons_search` (`tema`, `kratko`, `text`, `albom`);

ALTER TABLE `blagochiniya`
  ADD INDEX `idx_blagochiniya_blag` (`blag`);

ALTER TABLE `doks`
  ADD INDEX `idx_doks_date` (`date`),
  ADD INDEX `idx_doks_year` (`year`),
  ADD FULLTEXT `idx_doks_search` (`name`, `text`);

ALTER TABLE `gazeta`
  ADD INDEX `idx_gazeta_date` (`data`),
  ADD INDEX `idx_gazeta_year_month` (`year`, `month`),
  ADD FULLTEXT `idx_gazeta_text` (`text`);

ALTER TABLE `klir`
  ADD INDEX `idx_klir_blag` (`blag`);

ALTER TABLE `krest_hod_2014` ADD INDEX `idx_krest_hod_2014_data` (`data`);
ALTER TABLE `krest_hod_2015` ADD INDEX `idx_krest_hod_2015_data` (`data`);
ALTER TABLE `krest_hod_2016` ADD INDEX `idx_krest_hod_2016_data` (`data`);
ALTER TABLE `krest_hod_2017` ADD INDEX `idx_krest_hod_2017_data` (`data`);
ALTER TABLE `krest_hod_2018` ADD INDEX `idx_krest_hod_2018_data` (`data`);
ALTER TABLE `krest_hod_2019` ADD INDEX `idx_krest_hod_2019_data` (`data`);
ALTER TABLE `krest_hod_2020` ADD INDEX `idx_krest_hod_2020_data` (`data`);
ALTER TABLE `krest_hod_2021` ADD INDEX `idx_krest_hod_2021_data` (`data`);
ALTER TABLE `krest_hod_2022` ADD INDEX `idx_krest_hod_2022_data` (`data`);
ALTER TABLE `krest_hod_2023` ADD INDEX `idx_krest_hod_2023_data` (`data`);
ALTER TABLE `krest_hod_2024` ADD INDEX `idx_krest_hod_2024_data` (`data`);
ALTER TABLE `krest_hod_2025` ADD INDEX `idx_krest_hod_2025_data` (`data`);

ALTER TABLE `news`
  ADD INDEX `idx_news_date` (`data`),
  ADD FULLTEXT `idx_news_search` (`tema`, `text`);

ALTER TABLE `news_day`
  ADD INDEX `idx_news_day_date` (`data`),
  ADD INDEX `idx_news_day_page` (`page`),
  ADD FULLTEXT `idx_news_day_search` (`tema`, `text`);

ALTER TABLE `news_eparhia`
  ADD INDEX `idx_news_eparhia_date` (`data`),
  ADD FULLTEXT `idx_news_eparhia_search` (`tema`, `kratko`, `text`, `albom`, `video`);

ALTER TABLE `news_eparhia_cron`
  ADD INDEX `idx_news_eparhia_cron_date` (`data`),
  ADD FULLTEXT `idx_news_eparhia_cron_search` (`tema`, `kratko`, `text`, `albom`, `video`);

ALTER TABLE `news_mitropolia`
  ADD INDEX `idx_news_mitropolia_date` (`data`),
  ADD INDEX `idx_news_mitropolia_section` (`section`),
  ADD FULLTEXT `idx_news_mitropolia_search` (`tema`, `kratko`);

ALTER TABLE `old_prihods`
  ADD INDEX `idx_old_prihods_blag` (`blag`),
  ADD FULLTEXT `idx_old_prihods_search` (`name`, `adres`, `histor`, `kontakt`, `web`, `other`, `albom`);

ALTER TABLE `padre`
  ADD INDEX `idx_padre_date` (`data`),
  ADD FULLTEXT `idx_padre_search` (`tema`, `text`);

ALTER TABLE `prihods`
  ADD INDEX `idx_prihods_blag` (`blag`),
  ADD FULLTEXT `idx_prihods_search` (`name`, `adres`, `histor`, `kontakt`, `web`, `other`, `albom`);

ALTER TABLE `publikacii`
  ADD INDEX `idx_publikacii_date` (`data`),
  ADD FULLTEXT `idx_publikacii_search` (`tema`, `kratko`, `text`, `albom`);

ALTER TABLE `radio`
  ADD INDEX `idx_radio_date` (`data`),
  ADD FULLTEXT `idx_radio_search` (`tema`, `kod`);

ALTER TABLE `raspisanie`
  ADD INDEX `idx_raspisanie_date` (`data`),
  ADD INDEX `idx_raspisanie_weekday` (`nedel`),
  ADD FULLTEXT `idx_raspisanie_search` (`data_text`, `text`, `sluzba`);

ALTER TABLE `saints`
  ADD FULLTEXT `idx_saints_search` (`name`, `text`, `url`);

ALTER TABLE `video`
  ADD INDEX `idx_video_date` (`data`),
  ADD FULLTEXT `idx_video_search` (`tema`, `kod`);

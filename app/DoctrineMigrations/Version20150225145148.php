<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150225145148 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql(<<<EOT

			INSERT INTO `pages` VALUES (1,'/disease',1,1,'Болезни группы риска','Болезни группы риска','болезнь, группа риска',NULL),(2,'/disease/obesity',1,1,'Ожирение','Ожирение','Ожирение',NULL),(3,'/disease/hyperlipidemia',1,1,'Гиперлипидемия','Гиперлипидемия','Гиперлипидемия',NULL),(4,'/disease/chronic_renal_failure',1,1,'Хроническая почечная недостаточность','Хроническая почечная недостаточность','Хроническая почечная недостаточность',NULL),(5,'/disease/smoking',1,1,'Курение','Курение','Курение',NULL),(6,'/disease/arterial_hypertension',1,1,'Артериальная гипертензия','Артериальная гипертензия','Артериальная гипертензия',NULL),(7,'/disease/aging',1,1,'Пожилой возраст','Пожилой возраст','Пожилой возраст',NULL),(8,'/disease/diabetes',1,1,'Сахарный диабет','Сахарный диабет','Сахарный диабет',NULL),(9,'/about/about',1,1,'Компания Такеда','Компания Такеда','Компания Такеда',NULL),(10,'/about/mission',1,1,'Миссия','Миссия','Миссия',NULL),(11,'/about/dealer',1,1,'Информация по закупкам','Информация по закупкам','Информация по закупкам',NULL),(12,'/about/facilities',1,1,'Оборудование','Оборудование','Оборудование',NULL),(13,'/history',1,1,'История','История','История',NULL),(14,'/contacts',1,1,'Контакты','Контакты','Контакты',NULL),(15,'/certificates',1,1,'Сертификаты','Сертификаты','Сертификаты',NULL);

EOT
);

        $this->addSql(<<<EOT

			INSERT INTO `pblocks` VALUES (1,'sonata.block.service.text','disease.main.graph','{\"content\":\"<div class=\\\"g-diseases\\\"><div class=\\\"g-diseases__head\\\"><ul><li>\\u0417\\u0434\\u043e\\u0440\\u043e\\u0432\\u0438\\u0439 \\u043e\\u0431\\u0440\\u0430\\u0437 \\u0436\\u0438\\u0437\\u043d\\u0438<\\/li><li>\\u0415\\u0436\\u0435\\u0434\\u043d\\u0435\\u0432\\u043d\\u0430\\u044f \\u043f\\u0440\\u043e\\u0444\\u0438\\u043b\\u0430\\u043a\\u0442\\u0438\\u043a\\u0430<\\/li><li>\\u041e\\u0431\\u0441\\u043b\\u0435\\u0434\\u043e\\u0432\\u0430\\u043d\\u0438\\u0435 \\u0440\\u0430\\u0437 \\u0432 \\u043f\\u043e\\u043b \\u0433\\u043e\\u0434\\u0430<\\/li><li>\\u0420\\u0443\\u0433\\u0443\\u043b\\u044f\\u0440\\u043d\\u043e\\u0435 \\u043d\\u0430\\u0431\\u043b\\u044e\\u0434\\u0435\\u043d\\u0438\\u0435 \\u0443 \\u0432\\u0440\\u0430\\u0447\\u0435\\u0439<\\/li><li>\\u041e\\u0447\\u0435\\u043d\\u044c \\u0432\\u0438\\u0441\\u043e\\u043a\\u0438\\u0439 \\u0443\\u0440\\u043e\\u0432\\u0435\\u043d\\u044c \\u0440\\u0438\\u0441\\u043a\\u0430<\\/li><\\/ul><\\/div><div class=\\\"g-diseases__in\\\"><div class=\\\"g-diseases__row\\\"><a href=\\\"\\/disease\\/obesity\\\" style=\\\"width: 33%;\\\">\\u041e\\u0436\\u0438\\u0440\\u0435\\u043d\\u0438\\u0435<\\/a><\\/div><div class=\\\"g-diseases__row\\\"><a href=\\\"\\/disease\\/hyperlipidemia\\\" style=\\\"width: 45%;\\\">\\u0413\\u0438\\u043f\\u0435\\u0440\\u043b\\u0438\\u043f\\u0438\\u0434\\u0435\\u043c\\u0438\\u044f<\\/a><\\/div><div class=\\\"g-diseases__row\\\"><a href=\\\"\\/disease\\/chronic_renal_failure\\\" style=\\\"width: 57%;\\\">\\u0425\\u0440\\u043e\\u043d\\u0438\\u0447\\u0435\\u0441\\u043a\\u0430\\u044f \\u043f\\u043e\\u0447\\u0435\\u0447\\u043d\\u0430\\u044f \\u043d\\u0435\\u0434\\u043e\\u0441\\u0442\\u0430\\u0442\\u043e\\u0447\\u043d\\u043e\\u0441\\u0442\\u044c<\\/a><\\/div><div class=\\\"g-diseases__row\\\"><a href=\\\"\\/disease\\/smoking\\\" style=\\\"width: 61%;\\\">\\u041a\\u0443\\u0440\\u0435\\u043d\\u0438\\u0435<\\/a><\\/div><div class=\\\"g-diseases__row\\\"><a href=\\\"\\/disease\\/arterial_hypertension\\\" style=\\\"width: 70%;\\\">\\u0410\\u0440\\u0442\\u0435\\u0440\\u0438\\u0430\\u043b\\u044c\\u043d\\u0430\\u044f \\u0433\\u0438\\u043f\\u0435\\u0440\\u0442\\u0435\\u043d\\u0437\\u0438\\u044f<\\/a><\\/div><div class=\\\"g-diseases__row\\\"><a href=\\\"\\/disease\\/aging\\\" style=\\\"width: 76%;\\\">\\u041f\\u043e\\u0436\\u0438\\u043b\\u043e\\u0439 \\u0432\\u043e\\u0437\\u0440\\u0430\\u0441\\u0442<\\/a><\\/div><div class=\\\"g-diseases__row\\\"><a href=\\\"\\/disease\\/diabetes\\\" style=\\\"width: 89%;\\\">\\u0421\\u0430\\u0445\\u0430\\u0440\\u043d\\u044b\\u0439 \\u0434\\u0438\\u0430\\u0431\\u0435\\u0442 \\/ \\u041c\\u0435\\u0442\\u0430\\u0431\\u043e\\u043b\\u0438\\u0447\\u0441\\u043a\\u0438\\u0439 \\u0441\\u0438\\u043d\\u0434\\u0440\\u043e\\u043c<\\/a><\\/div><\\/div><\\/div>\"}',NULL),(2,'sonata.block.service.text','disease.obesity','{\"content\":\"\\u041e\\u0436\\u0438\\u0440\\u0435\\u043d\\u0438\\u0435\"}',NULL),(3,'sonata.block.service.text','disease.hyperlipidemia','{\"content\":\"\\u0413\\u0438\\u043f\\u0435\\u0440\\u043b\\u0438\\u043f\\u0438\\u0434\\u0435\\u043c\\u0438\\u044f\"}',NULL),(4,'sonata.block.service.text','disease.chronic_renal_failure','{\"content\":\"\\u0425\\u0440\\u043e\\u043d\\u0438\\u0447\\u0435\\u0441\\u043a\\u0430\\u044f \\u043f\\u043e\\u0447\\u0435\\u0447\\u043d\\u0430\\u044f \\u043d\\u0435\\u0434\\u043e\\u0441\\u0442\\u0430\\u0442\\u043e\\u0447\\u043d\\u043e\\u0441\\u0442\\u044c\"}',NULL),(5,'sonata.block.service.text','disease.smoking','{\"content\":\"\\u041a\\u0443\\u0440\\u0435\\u043d\\u0438\\u0435\"}',NULL),(6,'sonata.block.service.text','disease.arterial_hypertension','{\"content\":\"\\u0410\\u0440\\u0442\\u0435\\u0440\\u0438\\u0430\\u043b\\u044c\\u043d\\u0430\\u044f \\u0433\\u0438\\u043f\\u0435\\u0440\\u0442\\u0435\\u043d\\u0437\\u0438\\u044f\"}',NULL),(7,'sonata.block.service.text','disease.aging','{\"content\":\"\\u041f\\u043e\\u0436\\u0438\\u043b\\u043e\\u0439 \\u0432\\u043e\\u0437\\u0440\\u0430\\u0441\\u0442\"}',NULL),(8,'sonata.block.service.text','disease.diabetes_1','{\"content\":\" <div class=\\\"content\\\"> <h2>\\u041f\\u0440\\u0438\\u0447\\u0438\\u043d\\u044b, \\u0441\\u0438\\u043c\\u043f\\u0442\\u043e\\u043c\\u044b, \\u043f\\u043e\\u0441\\u043b\\u0435\\u0434\\u0441\\u0442\\u0432\\u0438\\u044f<\\/h2> <p>\\u0413\\u0438\\u043f\\u043e\\u0434\\u0438\\u043d\\u0430\\u043c\\u0438\\u044f, \\u043f\\u0435\\u0440\\u0435\\u0435\\u0434\\u0430\\u043d\\u0438\\u0435, \\u0441\\u0442\\u0440\\u0435\\u0441\\u0441\\u044b, \\u0441\\u0442\\u043e\\u043b\\u044c \\u0445\\u0430\\u0440\\u0430\\u043a\\u0442\\u0435\\u0440\\u043d\\u044b\\u0435 \\u0434\\u043b\\u044f \\u043d\\u0430\\u0448\\u0435\\u0433\\u043e \\u0432\\u0440\\u0435\\u043c\\u0435\\u043d\\u0438, \\u043f\\u0440\\u0438\\u0432\\u0435\\u043b\\u0438 \\u043a \\u043d\\u0430\\u0441\\u0442\\u043e\\u044f\\u0449\\u0435\\u0439 \\u044d\\u043f\\u0438\\u0434\\u0435\\u043c\\u0438\\u0438 \\u043d\\u0435\\u0438\\u0437\\u043b\\u0435\\u0447\\u0438\\u043c\\u043e\\u0439 \\u0431\\u043e\\u043b\\u0435\\u0437\\u043d\\u0438, \\u0441\\u0442\\u0430\\u0432\\u0448\\u0435\\u0439 \\u0431\\u0438\\u0447\\u043e\\u043c 21 \\u0432\\u0435\\u043a\\u0430. \\u041f\\u043e \\u043e\\u0446\\u0435\\u043d\\u043a\\u0430\\u043c \\u0412\\u041e\\u0417, \\u043a 2030 \\u0433\\u043e\\u0434\\u0443 \\u043e\\u0442 \\u0441\\u0430\\u0445\\u0430\\u0440\\u043d\\u043e\\u0433\\u043e \\u0434\\u0438\\u0430\\u0431\\u0435\\u0442\\u0430 \\u0431\\u0443\\u0434\\u0443\\u0442 \\u0443\\u043c\\u0438\\u0440\\u0430\\u0442\\u044c \\u0434\\u043e 7 \\u043c\\u043b\\u043d. \\u0447\\u0435\\u043b\\u043e\\u0432\\u0435\\u043a \\u0432 \\u0433\\u043e\\u0434.<\\/p><\\/div><div class=\\\"details__media\\\"> <img src=\\\"http:\\/\\/placehold.it\\/720x480\\\" alt=\\\"\\\"> <\\/div>\"}',NULL),(9,'sonata.block.service.text','disease.diabetes_2','{\"content\":\"<div class=\\\"content content_mb70\\\"> <p>\\u0412 \\u0434\\u0438\\u0441\\u0444\\u0443\\u043d\\u043a\\u0446\\u0438\\u0438 \\u043f\\u043e\\u0434\\u0436\\u0435\\u043b\\u0443\\u0434\\u043e\\u0447\\u043d\\u043e\\u0439 \\u0436\\u0435\\u043b\\u0435\\u0437\\u044b, \\u0447\\u0435\\u043c \\u044f\\u0432\\u043b\\u044f\\u0435\\u0442\\u0441\\u044f, \\u043f\\u043e \\u0441\\u0443\\u0442\\u0438, \\u0441\\u0430\\u0445\\u0430\\u0440\\u043d\\u044b\\u0439 \\u0434\\u0438\\u0430\\u0431\\u0435\\u0442, \\u0447\\u0430\\u0441\\u0442\\u043e \\u0432\\u0438\\u043d\\u043e\\u0432\\u0430\\u0442\\u0430 \\u043d\\u0430\\u0441\\u043b\\u0435\\u0434\\u0441\\u0442\\u0432\\u0435\\u043d\\u043d\\u043e\\u0441\\u0442\\u044c: \\u0432 \\u0441\\u043b\\u0443\\u0447\\u0430\\u0435 \\u0434\\u0438\\u0430\\u0431\\u0435\\u0442\\u0430 \\u043f\\u0435\\u0440\\u0432\\u043e\\u0433\\u043e \\u0442\\u0438\\u043f\\u0430 \\u0432\\u0435\\u0440\\u043e\\u044f\\u0442\\u043d\\u043e\\u0441\\u0442\\u044c \\u0437\\u0430\\u0431\\u043e\\u043b\\u0435\\u0442\\u044c \\u0441\\u043e\\u0441\\u0442\\u0430\\u0432\\u043b\\u044f\\u0435\\u0442 3-10%, \\u0435\\u0441\\u043b\\u0438 \\u0431\\u043e\\u043b\\u0435\\u0435\\u0442 \\u043e\\u0434\\u0438\\u043d \\u0440\\u043e\\u0434\\u0438\\u0442\\u0435\\u043b\\u044c, \\u0438 70% - \\u0435\\u0441\\u043b\\u0438 \\u043e\\u0431\\u0430, \\u043f\\u0440\\u0438\\u0447\\u0435\\u043c \\u0431\\u043e\\u043b\\u0435\\u0437\\u043d\\u044c \\u043f\\u0440\\u043e\\u044f\\u0432\\u0438\\u0442 \\u0441\\u0435\\u0431\\u044f \\u0434\\u043e 30 \\u043b\\u0435\\u0442. \\u0414\\u0438\\u0430\\u0431\\u0435\\u0442 \\u0432\\u0442\\u043e\\u0440\\u043e\\u0433\\u043e \\u0442\\u0438\\u043f\\u0430 \\u043f\\u0435\\u0440\\u0435\\u0434\\u0430\\u0441\\u0442\\u0441\\u044f \\u0443\\u0436\\u0435 \\u0432 80 \\u0441\\u043b\\u0443\\u0447\\u0430\\u044f\\u0445 \\u0438\\u0437 \\u0441\\u0442\\u0430, \\u0434\\u0430\\u0436\\u0435 \\u0435\\u0441\\u043b\\u0438 \\u0431\\u043e\\u043b\\u0435\\u043d \\u0442\\u043e\\u043b\\u044c\\u043a\\u043e \\u043e\\u0434\\u0438\\u043d \\u0440\\u043e\\u0434\\u0438\\u0442\\u0435\\u043b\\u044c, \\u043e\\u0434\\u043d\\u0430\\u043a\\u043e \\u0431\\u043e\\u043b\\u0435\\u0437\\u043d\\u044c \\u0441\\u043b\\u0443\\u0447\\u0438\\u0442\\u0441\\u044f \\u0432 \\u0441\\u0442\\u0430\\u0440\\u0448\\u0435\\u043c \\u0432\\u043e\\u0437\\u0440\\u0430\\u0441\\u0442\\u0435. <\\/p><p>\\u041a\\u043e\\u043d\\u0435\\u0447\\u043d\\u043e, \\u043d\\u0430\\u0441\\u043b\\u0435\\u0434\\u0441\\u0442\\u0432\\u0435\\u043d\\u043d\\u043e\\u0441\\u0442\\u044c \\u043d\\u0435 \\u043f\\u0440\\u0438\\u0433\\u043e\\u0432\\u043e\\u0440, \\u0434\\u043b\\u044f \\u0437\\u0430\\u0431\\u043e\\u043b\\u0435\\u0432\\u0430\\u043d\\u0438\\u044f \\u043d\\u0443\\u0436\\u0435\\u043d \\u00ab\\u0441\\u043f\\u0443\\u0441\\u043a\\u043e\\u0432\\u043e\\u0439 \\u043a\\u0440\\u044e\\u0447\\u043e\\u043a\\u00bb, \\u043d\\u0430\\u043f\\u0440\\u0438\\u043c\\u0435\\u0440 \\u043e\\u0436\\u0438\\u0440\\u0435\\u043d\\u0438\\u0435. \\u042d\\u0442\\u043e \\u0432\\u0442\\u043e\\u0440\\u0430\\u044f \\u0433\\u043b\\u043e\\u0431\\u0430\\u043b\\u044c\\u043d\\u0430\\u044f \\u043f\\u0440\\u0438\\u0447\\u0438\\u043d\\u0430 \\u0421\\u0414 \\u2013 \\u043f\\u0440\\u0438 \\u043f\\u043b\\u043e\\u0445\\u043e\\u0439 \\u043d\\u0430\\u0441\\u043b\\u0435\\u0434\\u0441\\u0442\\u0432\\u0435\\u043d\\u043d\\u043e\\u0441\\u0442\\u0438 \\u0440\\u0438\\u0441\\u043a \\u0437\\u0430\\u0431\\u043e\\u043b\\u0435\\u0442\\u044c \\u043f\\u043e\\u0447\\u0442\\u0438 100-\\u043f\\u0440\\u043e\\u0446\\u0435\\u043d\\u0442\\u043d\\u044b\\u0439. \\u041e\\u0446\\u0435\\u043d\\u0438\\u0442\\u044c \\u0441\\u0442\\u0435\\u043f\\u0435\\u043d\\u044c \\u0441\\u0432\\u043e\\u0435\\u0433\\u043e \\u0440\\u0438\\u0441\\u043a\\u0430 \\u0432\\u044b \\u043c\\u043e\\u0436\\u0435\\u0442\\u0435 \\u0441\\u0430\\u043c\\u0438, \\u0438\\u0437\\u043c\\u0435\\u0440\\u0438\\u0432 \\u043e\\u043a\\u0440\\u0443\\u0436\\u043d\\u043e\\u0441\\u0442\\u044c \\u0442\\u0430\\u043b\\u0438\\u0438 \\u0438 \\u043f\\u043e\\u0434\\u0441\\u0447\\u0438\\u0442\\u0430\\u0432 \\u0438\\u043d\\u0434\\u0435\\u043a\\u0441 \\u043c\\u0430\\u0441\\u0441\\u044b \\u0442\\u0435\\u043b\\u0430 (\\u043c\\u0430\\u0441\\u0441\\u0443 \\u0442\\u0435\\u043b\\u0430 \\u0432 \\u043a\\u0433 \\u0440\\u0430\\u0437\\u0434\\u0435\\u043b\\u0438\\u0442\\u044c \\u043d\\u0430 \\u043a\\u0432\\u0430\\u0434\\u0440\\u0430\\u0442 \\u0440\\u043e\\u0441\\u0442\\u0430 \\u0432 \\u043c). \\u0422\\u0430\\u043b\\u0438\\u044f \\u0431\\u043e\\u043b\\u044c\\u0448\\u0435 90 \\u0441\\u043c \\u0443 \\u0436\\u0435\\u043d\\u0449\\u0438\\u043d \\u0438 100 \\u0443 \\u043c\\u0443\\u0436\\u0447\\u0438\\u043d \\u0438 \\u0438\\u043d\\u0434\\u0435\\u043a\\u0441 \\u043c\\u0430\\u0441\\u0441\\u044b \\u0442\\u0435\\u043b\\u0430 \\u0432\\u044b\\u0448\\u0435 30 \\u2014 \\u043f\\u043e\\u0440\\u043e\\u0433\\u043e\\u0432\\u044b\\u0435 \\u0432\\u0435\\u043b\\u0438\\u0447\\u0438\\u043d\\u044b, \\u043e\\u0442 \\u043a\\u043e\\u0442\\u043e\\u0440\\u044b\\u0445 \\u0434\\u043e \\u0434\\u0438\\u0430\\u0431\\u0435\\u0442\\u0430 \\u0440\\u0443\\u043a\\u043e\\u0439 \\u043f\\u043e\\u0434\\u0430\\u0442\\u044c. <\\/p><p>\\u041f\\u043e\\u0434\\u0442\\u043e\\u043b\\u043a\\u043d\\u0443\\u0442\\u044c \\u0440\\u0430\\u0437\\u0432\\u0438\\u0442\\u0438\\u0435 \\u0434\\u0438\\u0430\\u0431\\u0435\\u0442\\u0430 \\u043c\\u043e\\u0433\\u0443\\u0442 \\u0442\\u0430\\u043a\\u0436\\u0435 \\u043f\\u0430\\u043d\\u043a\\u0440\\u0435\\u0430\\u0442\\u0438\\u0442 \\u0438 \\u0434\\u0440\\u0443\\u0433\\u0438\\u0435 \\u0437\\u0430\\u0431\\u043e\\u043b\\u0435\\u0432\\u0430\\u043d\\u0438\\u044f \\u043f\\u043e\\u0434\\u0436\\u0435\\u043b\\u0443\\u0434\\u043e\\u0447\\u043d\\u043e\\u0439 \\u0436\\u0435\\u043b\\u0435\\u0437\\u044b, \\u0438\\u043d\\u0444\\u0435\\u043a\\u0446\\u0438\\u043e\\u043d\\u043d\\u044b\\u0435 \\u0431\\u043e\\u043b\\u0435\\u0437\\u043d\\u0438. \\u0414\\u0430\\u0436\\u0435 \\u043e\\u0431\\u044b\\u0447\\u043d\\u044b\\u0439 \\u0433\\u0440\\u0438\\u043f\\u043f \\u043c\\u043e\\u0436\\u0435\\u0442 \\u043f\\u043e\\u043b\\u043e\\u0436\\u0438\\u0442\\u044c \\u043d\\u0430\\u0447\\u0430\\u043b\\u043e \\u0421\\u0414, \\u0435\\u0441\\u043b\\u0438 \\u0435\\u0441\\u0442\\u044c \\u0438 \\u0434\\u0440\\u0443\\u0433\\u0438\\u0435 \\u0444\\u0430\\u043a\\u0442\\u043e\\u0440\\u044b \\u0440\\u0438\\u0441\\u043a\\u0430 (\\u043a\\u0440\\u043e\\u043c\\u0435 \\u043d\\u0430\\u0441\\u043b\\u0435\\u0434\\u0441\\u0442\\u0432\\u0435\\u043d\\u043d\\u043e\\u0441\\u0442\\u0438 \\u0438 \\u043e\\u0436\\u0438\\u0440\\u0435\\u043d\\u0438\\u044f, \\u044d\\u0442\\u043e \\u043f\\u0435\\u0440\\u0435\\u0443\\u0442\\u043e\\u043c\\u043b\\u0435\\u043d\\u0438\\u0435, \\u043f\\u0435\\u0440\\u0435\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f, \\u0441\\u0438\\u0434\\u044f\\u0447\\u0438\\u0439 \\u043e\\u0431\\u0440\\u0430\\u0437 \\u0436\\u0438\\u0437\\u043d\\u0438, \\u043d\\u0435\\u043f\\u0440\\u0430\\u0432\\u0438\\u043b\\u044c\\u043d\\u043e\\u0435 \\u043f\\u0438\\u0442\\u0430\\u043d\\u0438\\u0435, \\u043f\\u043e\\u0436\\u0438\\u043b\\u043e\\u0439 \\u0432\\u043e\\u0437\\u0440\\u0430\\u0441\\u0442).<\\/p><p>\\u041f\\u0440\\u0438 \\u044d\\u0442\\u043e\\u043c \\u043c\\u043d\\u043e\\u0433\\u0438\\u0435 \\u0434\\u0438\\u0430\\u0431\\u0435\\u0442\\u0438\\u043a\\u0438 \\u0434\\u043e\\u043b\\u0433\\u0438\\u0435 \\u0433\\u043e\\u0434\\u044b \\u043f\\u043e\\u043d\\u044f\\u0442\\u0438\\u044f \\u043d\\u0435 \\u0438\\u043c\\u0435\\u044e\\u0442 \\u043e \\u0442\\u043e\\u043c, \\u0447\\u0442\\u043e \\u0431\\u043e\\u043b\\u044c\\u043d\\u044b. \\u0421\\u0438\\u043c\\u043f\\u0442\\u043e\\u043c\\u0430\\u043c\\u0438, \\u043a\\u0440\\u043e\\u043c\\u0435 \\u043f\\u043e\\u0432\\u044b\\u0448\\u0435\\u043d\\u0438\\u044f \\u0443\\u0440\\u043e\\u0432\\u043d\\u044f \\u0441\\u0430\\u0445\\u0430\\u0440\\u0430 \\u0432 \\u043a\\u0440\\u043e\\u0432\\u0438 (\\u044d\\u0442\\u043e\\u0442 \\u0430\\u043d\\u0430\\u043b\\u0438\\u0437 \\u0432\\u0440\\u0430\\u0447\\u0438 \\u0432\\u0441\\u0435\\u043c \\u0440\\u0435\\u043a\\u043e\\u043c\\u0435\\u043d\\u0434\\u0443\\u044e\\u0442 \\u0441\\u0434\\u0430\\u0432\\u0430\\u0442\\u044c \\u0440\\u0430\\u0437 \\u0432 \\u043f\\u043e\\u043b\\u0433\\u043e\\u0434\\u0430), \\u043c\\u043e\\u0433\\u0443\\u0442 \\u0431\\u044b\\u0442\\u044c:<\\/p><ul> <li>\\u043f\\u043e\\u0441\\u0442\\u043e\\u044f\\u043d\\u043d\\u044b\\u0435 \\u0436\\u0430\\u0436\\u0434\\u0430 \\u0438 \\u0430\\u043f\\u043f\\u0435\\u0442\\u0438\\u0442,<\\/li><li>\\u043e\\u0431\\u0438\\u043b\\u044c\\u043d\\u043e\\u0435 \\u043c\\u043e\\u0447\\u0435\\u0438\\u0441\\u043f\\u0443\\u0441\\u043a\\u0430\\u043d\\u0438\\u0435 \\u0438 \\u0441\\u0430\\u0445\\u0430\\u0440 \\u0432 \\u043c\\u043e\\u0447\\u0435,<\\/li><li>\\u043f\\u043e\\u0441\\u0442\\u043e\\u044f\\u043d\\u043d\\u0430\\u044f \\u0443\\u0441\\u0442\\u0430\\u043b\\u043e\\u0441\\u0442\\u044c, <\\/li><li>\\u0443\\u0445\\u0443\\u0434\\u0448\\u0435\\u043d\\u0438\\u0435 \\u0437\\u0440\\u0435\\u043d\\u0438\\u044f,<\\/li><li>\\u043e\\u043d\\u0435\\u043c\\u0435\\u043d\\u0438\\u0435 \\u043a\\u043e\\u043d\\u0435\\u0447\\u043d\\u043e\\u0441\\u0442\\u0435\\u0439,<\\/li><li>\\u0430\\u043a\\u043d\\u0435,<\\/li><li>\\u0434\\u043e\\u043b\\u0433\\u043e \\u043d\\u0435 \\u0437\\u0430\\u0436\\u0438\\u0432\\u0430\\u044e\\u0449\\u0438\\u0435 \\u0446\\u0430\\u0440\\u0430\\u043f\\u0438\\u043d\\u044b,<\\/li><li>\\u043a\\u043e\\u0436\\u043d\\u044b\\u0439 \\u0437\\u0443\\u0434,<\\/li><li>\\u043f\\u0440\\u043e\\u0431\\u043b\\u0435\\u043c\\u044b \\u0441 \\u0432\\u0435\\u0441\\u043e\\u043c (\\u0431\\u044b\\u0441\\u0442\\u0440\\u043e \\u043f\\u043e\\u043f\\u0440\\u0430\\u0432\\u0438\\u043b\\u0438\\u0441\\u044c \\u0438\\u043b\\u0438, \\u043d\\u0430\\u043e\\u0431\\u043e\\u0440\\u043e\\u0442, \\u043f\\u043e\\u0445\\u0443\\u0434\\u0435\\u043b\\u0438).<\\/li><\\/ul> <p>\\u0413\\u043b\\u0430\\u0432\\u043d\\u0430\\u044f \\u043e\\u043f\\u0430\\u0441\\u043d\\u043e\\u0441\\u0442\\u044c \\u0434\\u0438\\u0430\\u0431\\u0435\\u0442\\u0430 \\u2013 \\u0435\\u0433\\u043e \\u043e\\u0441\\u043b\\u043e\\u0436\\u043d\\u0435\\u043d\\u0438\\u044f. \\u0413\\u043b\\u044e\\u043a\\u043e\\u0437\\u0430, \\u043a\\u043e\\u0442\\u043e\\u0440\\u0443\\u044e \\u043e\\u0440\\u0433\\u0430\\u043d\\u0438\\u0437\\u043c \\u0434\\u0438\\u0430\\u0431\\u0435\\u0442\\u0438\\u043a\\u0430 \\u043d\\u0435 \\u043c\\u043e\\u0436\\u0435\\u0442 \\u0443\\u0441\\u0432\\u043e\\u0438\\u0442\\u044c, \\u0441\\u0433\\u0443\\u0449\\u0430\\u0435\\u0442 \\u043a\\u0440\\u043e\\u0432\\u044c, \\u043d\\u0430\\u043d\\u043e\\u0441\\u0438\\u0442 \\u0432\\u0440\\u0435\\u0434 \\u043e\\u0440\\u0433\\u0430\\u043d\\u0430\\u043c \\u0438 \\u0442\\u043a\\u0430\\u043d\\u044f\\u043c. \\u041a\\u043b\\u0435\\u0442\\u043a\\u0430\\u043c \\u043f\\u0440\\u0438\\u0445\\u043e\\u0434\\u0438\\u0442\\u0441\\u044f \\u0447\\u0435\\u0440\\u043f\\u0430\\u0442\\u044c \\u044d\\u043d\\u0435\\u0440\\u0433\\u0438\\u044e \\u0438\\u0437 \\u0436\\u0438\\u0440\\u043e\\u0432, \\u043f\\u0440\\u0438 \\u044d\\u0442\\u043e\\u043c \\u043e\\u0431\\u0440\\u0430\\u0437\\u043e\\u0432\\u044b\\u0432\\u0430\\u044e\\u0442\\u0441\\u044f \\u044f\\u0434\\u043e\\u0432\\u0438\\u0442\\u044b\\u0435 \\u0441\\u043e\\u0435\\u0434\\u0438\\u043d\\u0435\\u043d\\u0438\\u044f, \\u043d\\u0430\\u0440\\u0443\\u0448\\u0430\\u044e\\u0442\\u0441\\u044f \\u0432\\u0441\\u0435 \\u0432\\u0438\\u0434\\u044b \\u043e\\u0431\\u043c\\u0435\\u043d\\u0430 \\u0432\\u0435\\u0449\\u0435\\u0441\\u0442\\u0432. \\u041f\\u043e\\u0441\\u0442\\u0435\\u043f\\u0435\\u043d\\u043d\\u043e \\u0440\\u0430\\u0437\\u0440\\u0443\\u0448\\u0430\\u044e\\u0442\\u0441\\u044f \\u0441\\u043e\\u0441\\u0443\\u0434\\u0438\\u0441\\u0442\\u0430\\u044f \\u0438 \\u043d\\u0435\\u0440\\u0432\\u043d\\u0430\\u044f \\u0441\\u0438\\u0441\\u0442\\u0435\\u043c\\u044b, \\u043c\\u043e\\u0436\\u0435\\u0442 \\u0431\\u044b\\u0442\\u044c \\u0441\\u043b\\u0435\\u043f\\u043e\\u0442\\u0430, \\u0442\\u0440\\u043e\\u0444\\u0438\\u0447\\u0435\\u0441\\u043a\\u0438\\u0435 \\u044f\\u0437\\u0432\\u044b, \\u0438\\u043d\\u0444\\u0430\\u0440\\u043a\\u0442, \\u0438\\u043d\\u0441\\u0443\\u043b\\u044c\\u0442, \\u0430\\u043c\\u043f\\u0443\\u0442\\u0430\\u0446\\u0438\\u044f \\u043a\\u043e\\u043d\\u0435\\u0447\\u043d\\u043e\\u0441\\u0442\\u0435\\u0439.<\\/p><\\/div>\"}',NULL),(10,'sonata.block.service.text','about.about_1','{\"content\":\"<div class=\\\"content\\\"><h2>\\u041e \\u043a\\u043e\\u043c\\u043f\\u0430\\u043d\\u0438\\u0438 \\u0422\\u0430\\u043a\\u0435\\u0434\\u0430 \\u0420\\u043e\\u0441\\u0441\\u0438\\u044f-\\u0421\\u041d\\u0413<\\/h2><\\/div><div class=\\\"details__media\\\"><a href=\\\"#\\\"><img src=\\\"http:\\/\\/placehold.it\\/720x480\\\" alt=\\\"\\\"><i class=\\\"icon icon-play\\\"><\\/i><\\/a><\\/div>\"}',NULL),(11,'sonata.block.service.text','about.about_2','{\"content\":\"<div class=\\\"content\\\"><p>\\u0422\\u0430\\u043a\\u0435\\u0434\\u0430 \\u0420\\u043e\\u0441\\u0441\\u0438\\u044f-\\u0421\\u041d\\u0413 (\\u0440\\u0430\\u043d\\u0435\\u0435 \\\"\\u041d\\u0438\\u043a\\u043e\\u043c\\u0435\\u0434 \\u0420\\u043e\\u0441\\u0441\\u0438\\u044f-\\u0421\\u041d\\u0413\\\") \\u0432\\u0445\\u043e\\u0434\\u0438\\u0442 \\u0432 \\u0441\\u043e\\u0441\\u0442\\u0430\\u0432 Takeda Pharmaceutical Company Limited (\\\"\\u0422\\u0430\\u043a\\u0435\\u0434\\u0430\\\") \\u0441 \\u0441\\u0435\\u043d\\u0442\\u044f\\u0431\\u0440\\u044f 2011 \\u0433\\u043e\\u0434\\u0430. \\u041a\\u0430\\u043a \\u043a\\u0440\\u0443\\u043f\\u043d\\u0435\\u0439\\u0448\\u0430\\u044f \\u0444\\u0430\\u0440\\u043c\\u0430\\u0446\\u0435\\u0432\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u0430\\u044f \\u043a\\u043e\\u043c\\u043f\\u0430\\u043d\\u0438\\u044f \\u0432 \\u042f\\u043f\\u043e\\u043d\\u0438\\u0438 \\u0438 \\u043e\\u0434\\u0438\\u043d \\u0438\\u0437 \\u043c\\u0438\\u0440\\u043e\\u0432\\u044b\\u0445 \\u043b\\u0438\\u0434\\u0435\\u0440\\u043e\\u0432 \\u0438\\u043d\\u0434\\u0443\\u0441\\u0442\\u0440\\u0438\\u0438, \\\"\\u0422\\u0430\\u043a\\u0435\\u0434\\u0430\\\" \\u043f\\u0440\\u0438\\u0434\\u0435\\u0440\\u0436\\u0438\\u0432\\u0430\\u0435\\u0442\\u0441\\u044f \\u0441\\u0442\\u0440\\u0435\\u043c\\u043b\\u0435\\u043d\\u0438\\u044f \\u043a \\u0443\\u043b\\u0443\\u0447\\u0448\\u0435\\u043d\\u0438\\u044e \\u0437\\u0434\\u043e\\u0440\\u043e\\u0432\\u044c\\u044f \\u043f\\u0430\\u0446\\u0438\\u0435\\u043d\\u0442\\u043e\\u0432 \\u043f\\u043e \\u0432\\u0441\\u0435\\u043c\\u0443 \\u043c\\u0438\\u0440\\u0443 \\u043f\\u0443\\u0442\\u0435\\u043c \\u0432\\u043d\\u0435\\u0434\\u0440\\u0435\\u043d\\u0438\\u044f \\u0432\\u0435\\u0434\\u0443\\u0449\\u0438\\u0445 \\u0438\\u043d\\u043d\\u043e\\u0432\\u0430\\u0446\\u0438\\u0439 \\u0432 \\u043e\\u0431\\u043b\\u0430\\u0441\\u0442\\u0438 \\u043c\\u0435\\u0434\\u0438\\u0446\\u0438\\u043d\\u044b.<\\/p><p>\\\"\\u0422\\u0430\\u043a\\u0435\\u0434\\u0430\\\" \\u0438\\u043c\\u0435\\u0435\\u0442 \\u043f\\u0440\\u0435\\u0434\\u0441\\u0442\\u0430\\u0432\\u0438\\u0442\\u0435\\u043b\\u044c\\u0441\\u0442\\u0432\\u0430 \\u0432 \\u0431\\u043e\\u043b\\u0435\\u0435 \\u0447\\u0435\\u043c 70 \\u0441\\u0442\\u0440\\u0430\\u043d\\u0430\\u0445 \\u043c\\u0438\\u0440\\u0430, \\u0441 \\u0442\\u0440\\u0430\\u0434\\u0438\\u0446\\u0438\\u043e\\u043d\\u043d\\u043e \\u0441\\u0438\\u043b\\u044c\\u043d\\u044b\\u043c\\u0438 \\u043f\\u043e\\u0437\\u0438\\u0446\\u0438\\u044f\\u043c\\u0438 \\u0432 \\u0410\\u0437\\u0438\\u0438, \\u0421\\u0435\\u0432\\u0435\\u0440\\u043d\\u043e\\u0439 \\u0410\\u043c\\u0435\\u0440\\u0438\\u043a\\u0435, \\u0415\\u0432\\u0440\\u043e\\u043f\\u0435, \\u0430 \\u0442\\u0430\\u043a\\u0436\\u0435 \\u043d\\u0430 \\u0431\\u044b\\u0441\\u0442\\u0440\\u043e\\u0440\\u0430\\u0441\\u0442\\u0443\\u0449\\u0438\\u0445 \\u0440\\u0430\\u0437\\u0432\\u0438\\u0432\\u0430\\u044e\\u0449\\u0438\\u0445\\u0441\\u044f \\u0440\\u044b\\u043d\\u043a\\u0430\\u0445, \\u0432\\u043a\\u043b\\u044e\\u0447\\u0430\\u044f \\u041b\\u0430\\u0442\\u0438\\u043d\\u0441\\u043a\\u0443\\u044e \\u0410\\u043c\\u0435\\u0440\\u0438\\u043a\\u0443, \\u0420\\u043e\\u0441\\u0441\\u0438\\u044e-\\u0421\\u041d\\u0413 \\u0438 \\u041a\\u0438\\u0442\\u0430\\u0439. \\u041a\\u043e\\u043c\\u043f\\u0430\\u043d\\u0438\\u044f \\u0441\\u043e\\u0441\\u0440\\u0435\\u0434\\u043e\\u0442\\u0430\\u0447\\u0438\\u0432\\u0430\\u0435\\u0442 \\u0441\\u0432\\u043e\\u044e \\u0434\\u0435\\u044f\\u0442\\u0435\\u043b\\u044c\\u043d\\u043e\\u0441\\u0442\\u044c \\u043d\\u0430 \\u0442\\u0430\\u043a\\u0438\\u0445 \\u0442\\u0435\\u0440\\u0430\\u043f\\u0435\\u0432\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u0438\\u0445 \\u043e\\u0431\\u043b\\u0430\\u0441\\u0442\\u044f\\u0445, \\u043a\\u0430\\u043a: \\u0441\\u0435\\u0440\\u0434\\u0435\\u0447\\u043d\\u043e-\\u0441\\u043e\\u0441\\u0443\\u0434\\u0438\\u0441\\u0442\\u044b\\u0435 \\u0438 \\u043c\\u0435\\u0442\\u0430\\u0431\\u043e\\u043b\\u0438\\u0447\\u0435\\u0441\\u043a\\u0438\\u0435 \\u0437\\u0430\\u0431\\u043e\\u043b\\u0435\\u0432\\u0430\\u043d\\u0438\\u044f, \\u0438\\u043c\\u043c\\u0443\\u043d\\u043d\\u044b\\u0435 \\u043d\\u0430\\u0440\\u0443\\u0448\\u0435\\u043d\\u0438\\u044f \\u0438 \\u0440\\u0435\\u0441\\u043f\\u0438\\u0440\\u0430\\u0442\\u043e\\u0440\\u043d\\u044b\\u0435 \\u0437\\u0430\\u0431\\u043e\\u043b\\u0435\\u0432\\u0430\\u043d\\u0438\\u044f, \\u043e\\u043d\\u043a\\u043e\\u043b\\u043e\\u0433\\u0438\\u044f \\u0438 \\u0437\\u0430\\u0431\\u043e\\u043b\\u0435\\u0432\\u0430\\u043d\\u0438\\u044f \\u0446\\u0435\\u043d\\u0442\\u0440\\u0430\\u043b\\u044c\\u043d\\u043e\\u0439 \\u043d\\u0435\\u0440\\u0432\\u043d\\u043e\\u0439 \\u0441\\u0438\\u0441\\u0442\\u0435\\u043c\\u044b.<\\/p><p>\\u0411\\u043e\\u043b\\u0435\\u0435 \\u043f\\u043e\\u0434\\u0440\\u043e\\u0431\\u043d\\u0443\\u044e \\u0438\\u043d\\u0444\\u043e\\u0440\\u043c\\u0430\\u0446\\u0438\\u044e \\u043e \\\"\\u0422\\u0430\\u043a\\u0435\\u0434\\u0430\\\" \\u0432\\u044b \\u043c\\u043e\\u0436\\u0435\\u0442\\u0435 \\u043d\\u0430\\u0439\\u0442\\u0438 \\u043d\\u0430 \\u0441\\u0430\\u0439\\u0442\\u0435 \\u043a\\u043e\\u043c\\u043f\\u0430\\u043d\\u0438\\u0438 <a href=\\\"#\\\">http:\\/\\/www.takeda.com<\\/a> \\u0438\\u043b\\u0438 \\u043e \\\"\\u0422\\u0430\\u043a\\u0435\\u0434\\u0430\\\" \\u0432 \\u0420\\u043e\\u0441\\u0441\\u0438\\u0438 \\u043d\\u0430 <a href=\\\"#\\\">http:\\/\\/www.takeda.com.ru<\\/a><\\/p><\\/div>\"}',NULL),(12,'sonata.block.service.text','about.mission','{\"content\":\"<div class=\\\"details__left\\\"> <div class=\\\"content\\\"> <h2>\\u041c\\u0438\\u0441\\u0441\\u0438\\u044f<\\/h2> <\\/div><\\/div>\"}',NULL),(13,'sonata.block.service.text','about.dealer','{\"content\":\"<div class=\\\"details__left\\\"> <div class=\\\"content\\\"> <h2>\\u0418\\u043d\\u0444\\u043e\\u0440\\u043c\\u0430\\u0446\\u0438\\u044f \\u043f\\u043e \\u0437\\u0430\\u043a\\u0443\\u043f\\u043a\\u0430\\u043c<\\/h2> <\\/div><\\/div>\"}',NULL),(14,'sonata.block.service.text','about.facilities','{\"content\":\"<div class=\\\"details__left\\\"> <div class=\\\"content\\\"> <h2>\\u041e\\u0431\\u043e\\u0440\\u0443\\u0434\\u043e\\u0432\\u0430\\u043d\\u0438\\u0435<\\/h2> <\\/div><\\/div>\"}',NULL),(15,'sonata.block.service.text','history','{\"content\":\"\"}',NULL),(16,'engage360d_takeda.block.map','contacts_1','{\"map\":{\"lat\":55.73089228,\"lon\":37.576726,\"zoom\":17},\"placemarks\":[{\"lat\":55.730892277271,\"lon\":37.576726,\"settings\":{\"balloonOptions\":{\"maxWidth\":70,\"hasCloseButton\":false,\"mapAutoPan\":0}}}]}',NULL),(17,'sonata.block.service.text','contacts_2','{\"content\":\"<div class=\\\"content\\\"><h2>\\u041e\\u041e\\u041e \\u00ab\\u0422\\u0430\\u043a\\u0435\\u0434\\u0430 \\u0424\\u0430\\u0440\\u043c\\u0430\\u0441\\u044c\\u044e\\u0442\\u0438\\u043a\\u0430\\u043b\\u0441\\u00bb<\\/h2><p>119048 \\u041c\\u043e\\u0441\\u043a\\u0432\\u0430, \\u0443\\u043b. \\u0423\\u0441\\u0430\\u0447\\u0451\\u0432\\u0430 \\u0434\\u043e\\u043c 2, \\u0441\\u0442\\u0440.1, \\u0411\\u0438\\u0437\\u043d\\u0435\\u0441-\\u0446\\u0435\\u043d\\u0442\\u0440 \\u00ab\\u0424\\u044c\\u044e\\u0436\\u043d \\u041f\\u0430\\u0440\\u043a\\u00bb<br>+7 (495) 933-55-11, \\u0444\\u0430\\u043a\\u0441. +7 (495) 502-16-25<br><a href=\\\"mailto:russia@takeda.com\\\">russia@takeda.com<\\/a><\\/p><\\/div>\"}',NULL),(18,'sonata.block.service.text','certificates','{\"content\":\"\"}',NULL);

EOT
);

        $this->addSql(<<<EOT

			INSERT INTO `pages_pblocks` VALUES (1,1),(2,2),(3,3),(4,4),(5,5),(6,6),(7,7),(8,8),(8,9),(9,10),(9,11),(10,12),(11,13),(12,14),(13,15),(14,16),(14,17),(15,18);

EOT
);

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}

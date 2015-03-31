<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150331144625 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'EOT'

			DELETE FROM `pages_pblocks` WHERE page_id = 4;

EOT
        );

        $this->addSql(<<<'EOT'

			UPDATE `pages_pblocks` SET page_id = 4 WHERE page_id = 17;

EOT
        );

        $this->addSql(<<<'EOT'

			DELETE FROM `pages` WHERE id = 17;

EOT
        );

        $this->addSql(<<<'EOT'

			UPDATE `pages` SET url = '/factors/tromb', title = 'Тромбоз и тромбоэмболия', description = 'Тромбоз и тромбоэмболия', keywords = 'Тромбоз и тромбоэмболия' WHERE id = 4;

EOT
        );


        $this->addSql(<<<'EOT'

            update pblocks set json='{\"content\":\"<div class=\\\"g-diseases\\\"><div class=\\\"g-diseases__head\\\"><ul><li>\\u0417\\u0434\\u043e\\u0440\\u043e\\u0432\\u0438\\u0439 \\u043e\\u0431\\u0440\\u0430\\u0437 \\u0436\\u0438\\u0437\\u043d\\u0438<\\/li><li>\\u0415\\u0436\\u0435\\u0434\\u043d\\u0435\\u0432\\u043d\\u0430\\u044f \\u043f\\u0440\\u043e\\u0444\\u0438\\u043b\\u0430\\u043a\\u0442\\u0438\\u043a\\u0430<\\/li><li>\\u041e\\u0431\\u0441\\u043b\\u0435\\u0434\\u043e\\u0432\\u0430\\u043d\\u0438\\u0435 \\u0440\\u0430\\u0437 \\u0432 \\u043f\\u043e\\u043b \\u0433\\u043e\\u0434\\u0430<\\/li><li>\\u0420\\u0443\\u0433\\u0443\\u043b\\u044f\\u0440\\u043d\\u043e\\u0435 \\u043d\\u0430\\u0431\\u043b\\u044e\\u0434\\u0435\\u043d\\u0438\\u0435 \\u0443 \\u0432\\u0440\\u0430\\u0447\\u0435\\u0439<\\/li><li>\\u041e\\u0447\\u0435\\u043d\\u044c \\u0432\\u0438\\u0441\\u043e\\u043a\\u0438\\u0439 \\u0443\\u0440\\u043e\\u0432\\u0435\\u043d\\u044c \\u0440\\u0438\\u0441\\u043a\\u0430<\\/li><\\/ul><\\/div><div class=\\\"g-diseases__in\\\"><div class=\\\"g-diseases__row\\\"><a href=\\\"\\/factors\\/obesity\\\" style=\\\"width: 33%;\\\">\\u041e\\u0436\\u0438\\u0440\\u0435\\u043d\\u0438\\u0435<\\/a><\\/div><div class=\\\"g-diseases__row\\\"><a href=\\\"\\/factors\\/hyperlipidemia\\\" style=\\\"width: 45%;\\\">\\u0413\\u0438\\u043f\\u0435\\u0440\\u043b\\u0438\\u043f\\u0438\\u0434\\u0435\\u043c\\u0438\\u044f<\\/a><\\/div><div class=\\\"g-diseases__row\\\"><a href=\\\"\\/factors\\/tromb\\\" style=\\\"width: 57%;\\\">Тромбоз и тромбоэмболия<\\/a><\\/div><div class=\\\"g-diseases__row\\\"><a href=\\\"\\/factors\\/smoking\\\" style=\\\"width: 61%;\\\">\\u041a\\u0443\\u0440\\u0435\\u043d\\u0438\\u0435<\\/a><\\/div><div class=\\\"g-diseases__row\\\"><a href=\\\"\\/factors\\/arterial_hypertension\\\" style=\\\"width: 70%;\\\">\\u0410\\u0440\\u0442\\u0435\\u0440\\u0438\\u0430\\u043b\\u044c\\u043d\\u0430\\u044f \\u0433\\u0438\\u043f\\u0435\\u0440\\u0442\\u0435\\u043d\\u0437\\u0438\\u044f<\\/a><\\/div><div class=\\\"g-diseases__row\\\"><a href=\\\"\\/factors\\/aging\\\" style=\\\"width: 76%;\\\">\\u041f\\u043e\\u0436\\u0438\\u043b\\u043e\\u0439 \\u0432\\u043e\\u0437\\u0440\\u0430\\u0441\\u0442<\\/a><\\/div><div class=\\\"g-diseases__row\\\"><a href=\\\"\\/factors\\/diabetes\\\" style=\\\"width: 89%;\\\">\\u0421\\u0430\\u0445\\u0430\\u0440\\u043d\\u044b\\u0439 \\u0434\\u0438\\u0430\\u0431\\u0435\\u0442 \\/ \\u041c\\u0435\\u0442\\u0430\\u0431\\u043e\\u043b\\u0438\\u0447\\u0441\\u043a\\u0438\\u0439 \\u0441\\u0438\\u043d\\u0434\\u0440\\u043e\\u043c<\\/a><\\/div><\\/div><\\/div>\"}' where id = 1;
EOT
        );

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}

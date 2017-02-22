<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161128152217 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE kuma_broodjesbundle_end_product ADD slack_name VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX name_unique ON kuma_broodjesbundle_end_product (slack_name, user_id)');
        $this->addSql('ALTER TABLE kuma_broodjesbundle_user_info CHANGE slack_name slack_name VARCHAR(255) DEFAULT NULL, CHANGE slack_access_token slack_access_token VARCHAR(255) DEFAULT NULL, CHANGE slack_id slack_id VARCHAR(255) DEFAULT NULL, CHANGE slack_team_id slack_team_id VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX name_unique ON kuma_broodjesbundle_end_product');
        $this->addSql('ALTER TABLE kuma_broodjesbundle_end_product DROP slack_name');
        $this->addSql('ALTER TABLE kuma_broodjesbundle_user_info CHANGE slack_name slack_name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE slack_access_token slack_access_token VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE slack_id slack_id VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE slack_team_id slack_team_id VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
    }
}

<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161123163103 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE kuma_users DROP expired, DROP credentials_expired, CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_39EF0B86C05FB297 ON kuma_users (confirmation_token)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_39EF0B86C05FB297 ON kuma_users');
        $this->addSql('ALTER TABLE kuma_users ADD expired TINYINT(1) NOT NULL, ADD credentials_expired TINYINT(1) NOT NULL, CHANGE confirmation_token confirmation_token VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}

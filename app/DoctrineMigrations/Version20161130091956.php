<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161130091956 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE kuma_broodjesbundle_default_orders ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE kuma_broodjesbundle_default_orders ADD CONSTRAINT FK_10EB0CB0A76ED395 FOREIGN KEY (user_id) REFERENCES kuma_users (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_10EB0CB0A76ED395 ON kuma_broodjesbundle_default_orders (user_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE kuma_broodjesbundle_default_orders DROP FOREIGN KEY FK_10EB0CB0A76ED395');
        $this->addSql('DROP INDEX UNIQ_10EB0CB0A76ED395 ON kuma_broodjesbundle_default_orders');
        $this->addSql('ALTER TABLE kuma_broodjesbundle_default_orders DROP user_id');
    }
}

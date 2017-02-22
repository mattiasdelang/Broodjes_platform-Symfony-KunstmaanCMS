<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161129154541 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE kuma_broodjesbundle_default_orders (id BIGINT AUTO_INCREMENT NOT NULL, day INT DEFAULT NULL, pauze INT DEFAULT NULL, endProduct_id BIGINT DEFAULT NULL, INDEX IDX_10EB0CB0D495789F (endProduct_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE kuma_broodjesbundle_default_orders ADD CONSTRAINT FK_10EB0CB0D495789F FOREIGN KEY (endProduct_id) REFERENCES kuma_broodjesbundle_end_product (id)');
        $this->addSql('ALTER TABLE kuma_broodjesbundle_end_product DROP default_day');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE kuma_broodjesbundle_default_orders');
        $this->addSql('ALTER TABLE kuma_broodjesbundle_end_product ADD default_day VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}

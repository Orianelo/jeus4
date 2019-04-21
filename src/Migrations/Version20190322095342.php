<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190322095342 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE game CHANGE terrain_j1 terrain_j1 JSON NOT NULL COMMENT \'(DC2Type:json_array)\', CHANGE terrain_j2 terrain_j2 JSON NOT NULL COMMENT \'(DC2Type:json_array)\', CHANGE de de JSON NOT NULL COMMENT \'(DC2Type:json_array)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE game CHANGE terrain_j1 terrain_j1 VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE terrain_j2 terrain_j2 LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE de de LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}

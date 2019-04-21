<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190403181217 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE chat (id INT AUTO_INCREMENT NOT NULL, destinataire_id INT NOT NULL, expediteur_id INT NOT NULL, message LONGTEXT NOT NULL, INDEX IDX_659DF2AAA4F84F6E (destinataire_id), INDEX IDX_659DF2AA10335F61 (expediteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chat_game (id INT AUTO_INCREMENT NOT NULL, partie_id INT NOT NULL, joueur INT NOT NULL, message LONGTEXT NOT NULL, INDEX IDX_85B7ACCDE075F7A4 (partie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chat ADD CONSTRAINT FK_659DF2AAA4F84F6E FOREIGN KEY (destinataire_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE chat ADD CONSTRAINT FK_659DF2AA10335F61 FOREIGN KEY (expediteur_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE chat_game ADD CONSTRAINT FK_85B7ACCDE075F7A4 FOREIGN KEY (partie_id) REFERENCES game (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE chat');
        $this->addSql('DROP TABLE chat_game');
    }
}

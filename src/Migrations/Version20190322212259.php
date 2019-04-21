<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190322212259 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE friend_invitation (id INT AUTO_INCREMENT NOT NULL, joueur_invitant_id INT NOT NULL, joueur_invite_id INT NOT NULL, UNIQUE INDEX UNIQ_5F5A95A62145B507 (joueur_invitant_id), UNIQUE INDEX UNIQ_5F5A95A6141FED1F (joueur_invite_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE friend_invitation ADD CONSTRAINT FK_5F5A95A62145B507 FOREIGN KEY (joueur_invitant_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE friend_invitation ADD CONSTRAINT FK_5F5A95A6141FED1F FOREIGN KEY (joueur_invite_id) REFERENCES player (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE friend_invitation');
    }
}

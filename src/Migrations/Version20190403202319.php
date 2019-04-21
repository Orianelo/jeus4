<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190403202319 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE player_player');
        $this->addSql('ALTER TABLE player ADD friend JSON NOT NULL COMMENT \'(DC2Type:json_array)\'');
        $this->addSql('ALTER TABLE friend_invitation DROP FOREIGN KEY FK_5F5A95A6141FED1F');
        $this->addSql('ALTER TABLE friend_invitation DROP FOREIGN KEY FK_5F5A95A62145B507');
        $this->addSql('DROP INDEX UNIQ_5F5A95A6141FED1F ON friend_invitation');
        $this->addSql('DROP INDEX UNIQ_5F5A95A62145B507 ON friend_invitation');
        $this->addSql('ALTER TABLE friend_invitation ADD joueur_invitant INT NOT NULL, ADD joueur_invite INT NOT NULL, DROP joueur_invitant_id, DROP joueur_invite_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE player_player (player_source INT NOT NULL, player_target INT NOT NULL, INDEX IDX_719ECBBC08AE9AD (player_source), INDEX IDX_719ECBBD96FB922 (player_target), PRIMARY KEY(player_source, player_target)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE player_player ADD CONSTRAINT FK_719ECBBC08AE9AD FOREIGN KEY (player_source) REFERENCES player (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE player_player ADD CONSTRAINT FK_719ECBBD96FB922 FOREIGN KEY (player_target) REFERENCES player (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE friend_invitation ADD joueur_invitant_id INT NOT NULL, ADD joueur_invite_id INT NOT NULL, DROP joueur_invitant, DROP joueur_invite');
        $this->addSql('ALTER TABLE friend_invitation ADD CONSTRAINT FK_5F5A95A6141FED1F FOREIGN KEY (joueur_invite_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE friend_invitation ADD CONSTRAINT FK_5F5A95A62145B507 FOREIGN KEY (joueur_invitant_id) REFERENCES player (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5F5A95A6141FED1F ON friend_invitation (joueur_invite_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5F5A95A62145B507 ON friend_invitation (joueur_invitant_id)');
        $this->addSql('ALTER TABLE player DROP friend');
    }
}

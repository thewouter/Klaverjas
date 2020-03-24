<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200324213009 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE trick (id INT AUTO_INCREMENT NOT NULL, player_1_id INT NOT NULL, player_2_id INT NOT NULL, player_3_id INT NOT NULL, player_4_id INT NOT NULL, card_1_id INT DEFAULT NULL, card_2_id INT DEFAULT NULL, card_3_id INT DEFAULT NULL, card_4_id INT DEFAULT NULL, game_id INT NOT NULL, INDEX IDX_D8F0A91E52C90CC9 (player_1_id), INDEX IDX_D8F0A91E407CA327 (player_2_id), INDEX IDX_D8F0A91EF8C0C442 (player_3_id), INDEX IDX_D8F0A91E6517FCFB (player_4_id), INDEX IDX_D8F0A91E1CE05E58 (card_1_id), INDEX IDX_D8F0A91EE55F1B6 (card_2_id), INDEX IDX_D8F0A91EB6E996D3 (card_3_id), INDEX IDX_D8F0A91E2B3EAE6A (card_4_id), INDEX IDX_D8F0A91EE48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91E52C90CC9 FOREIGN KEY (player_1_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91E407CA327 FOREIGN KEY (player_2_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91EF8C0C442 FOREIGN KEY (player_3_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91E6517FCFB FOREIGN KEY (player_4_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91E1CE05E58 FOREIGN KEY (card_1_id) REFERENCES card (id)');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91EE55F1B6 FOREIGN KEY (card_2_id) REFERENCES card (id)');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91EB6E996D3 FOREIGN KEY (card_3_id) REFERENCES card (id)');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91E2B3EAE6A FOREIGN KEY (card_4_id) REFERENCES card (id)');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91EE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE game DROP tricks');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE trick');
        $this->addSql('ALTER TABLE game ADD tricks LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:array)\'');
    }
}

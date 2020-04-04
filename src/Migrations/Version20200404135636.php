<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200404135636 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE player (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trick (id INT AUTO_INCREMENT NOT NULL, player_1_id INT NOT NULL, player_2_id INT NOT NULL, player_3_id INT NOT NULL, player_4_id INT NOT NULL, card_1_id INT DEFAULT NULL, card_2_id INT DEFAULT NULL, card_3_id INT DEFAULT NULL, card_4_id INT DEFAULT NULL, game_id INT NOT NULL, INDEX IDX_D8F0A91E52C90CC9 (player_1_id), INDEX IDX_D8F0A91E407CA327 (player_2_id), INDEX IDX_D8F0A91EF8C0C442 (player_3_id), INDEX IDX_D8F0A91E6517FCFB (player_4_id), INDEX IDX_D8F0A91E1CE05E58 (card_1_id), INDEX IDX_D8F0A91EE55F1B6 (card_2_id), INDEX IDX_D8F0A91EB6E996D3 (card_3_id), INDEX IDX_D8F0A91E2B3EAE6A (card_4_id), INDEX IDX_D8F0A91EE48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE card_player (card_id INT NOT NULL, player_id INT NOT NULL, INDEX IDX_EA2629114ACC9A20 (card_id), INDEX IDX_EA26291199E6F5DF (player_id), PRIMARY KEY(card_id, player_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91E52C90CC9 FOREIGN KEY (player_1_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91E407CA327 FOREIGN KEY (player_2_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91EF8C0C442 FOREIGN KEY (player_3_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91E6517FCFB FOREIGN KEY (player_4_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91E1CE05E58 FOREIGN KEY (card_1_id) REFERENCES card (id)');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91EE55F1B6 FOREIGN KEY (card_2_id) REFERENCES card (id)');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91EB6E996D3 FOREIGN KEY (card_3_id) REFERENCES card (id)');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91E2B3EAE6A FOREIGN KEY (card_4_id) REFERENCES card (id)');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91EE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE card_player ADD CONSTRAINT FK_EA2629114ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE card_player ADD CONSTRAINT FK_EA26291199E6F5DF FOREIGN KEY (player_id) REFERENCES player (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE card_client');
        $this->addSql('ALTER TABLE game DROP tricks');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B1D6DEC72');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B85C82428');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B977D8BC6');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519BFD8439C');
        $this->addSql('ALTER TABLE room ADD in_game TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B1D6DEC72 FOREIGN KEY (them1_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B85C82428 FOREIGN KEY (us1_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B977D8BC6 FOREIGN KEY (us2_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519BFD8439C FOREIGN KEY (them2_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE client ADD player_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C744045599E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C744045599E6F5DF ON client (player_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91E52C90CC9');
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91E407CA327');
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91EF8C0C442');
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91E6517FCFB');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B85C82428');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B977D8BC6');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B1D6DEC72');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519BFD8439C');
        $this->addSql('ALTER TABLE card_player DROP FOREIGN KEY FK_EA26291199E6F5DF');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C744045599E6F5DF');
        $this->addSql('CREATE TABLE card_client (card_id INT NOT NULL, client_id INT NOT NULL, INDEX IDX_B57B57214ACC9A20 (card_id), INDEX IDX_B57B572119EB6921 (client_id), PRIMARY KEY(card_id, client_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE card_client ADD CONSTRAINT FK_B57B572119EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE card_client ADD CONSTRAINT FK_B57B57214ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE player');
        $this->addSql('DROP TABLE trick');
        $this->addSql('DROP TABLE card_player');
        $this->addSql('DROP INDEX UNIQ_C744045599E6F5DF ON client');
        $this->addSql('ALTER TABLE client DROP player_id');
        $this->addSql('ALTER TABLE game ADD tricks LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B85C82428');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B977D8BC6');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B1D6DEC72');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519BFD8439C');
        $this->addSql('ALTER TABLE room DROP in_game');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B85C82428 FOREIGN KEY (us1_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B977D8BC6 FOREIGN KEY (us2_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B1D6DEC72 FOREIGN KEY (them1_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519BFD8439C FOREIGN KEY (them2_id) REFERENCES client (id)');
    }
}

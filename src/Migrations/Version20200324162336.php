<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200324162336 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Initial database setup';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, room_id INT NOT NULL, points LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', tricks LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_232B318C54177093 (room_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room (id INT AUTO_INCREMENT NOT NULL, us1_id INT DEFAULT NULL, us2_id INT DEFAULT NULL, them1_id INT DEFAULT NULL, them2_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_729F519B85C82428 (us1_id), UNIQUE INDEX UNIQ_729F519B977D8BC6 (us2_id), UNIQUE INDEX UNIQ_729F519B1D6DEC72 (them1_id), UNIQUE INDEX UNIQ_729F519BFD8439C (them2_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, cards LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C54177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B85C82428 FOREIGN KEY (us1_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B977D8BC6 FOREIGN KEY (us2_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B1D6DEC72 FOREIGN KEY (them1_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519BFD8439C FOREIGN KEY (them2_id) REFERENCES client (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C54177093');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B85C82428');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B977D8BC6');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B1D6DEC72');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519BFD8439C');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE client');
    }
}

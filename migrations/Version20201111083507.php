<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201111083507 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add user to credential table';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_57F1D4BA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__credential AS SELECT id, user_id, url, login, password FROM credential');
        $this->addSql('DROP TABLE credential');
        $this->addSql('CREATE TABLE credential (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, url CLOB DEFAULT NULL COLLATE BINARY, login VARCHAR(255) DEFAULT NULL COLLATE BINARY, password VARCHAR(255) DEFAULT NULL COLLATE BINARY, name VARCHAR(255) NOT NULL, CONSTRAINT FK_57F1D4BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO credential (id, user_id, url, login, password) SELECT id, user_id, url, login, password FROM __temp__credential');
        $this->addSql('DROP TABLE __temp__credential');
        $this->addSql('CREATE INDEX IDX_57F1D4BA76ED395 ON credential (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_57F1D4BA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__credential AS SELECT id, user_id, url, login, password FROM credential');
        $this->addSql('DROP TABLE credential');
        $this->addSql('CREATE TABLE credential (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, url CLOB DEFAULT NULL, login VARCHAR(255) DEFAULT NULL, password VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO credential (id, user_id, url, login, password) SELECT id, user_id, url, login, password FROM __temp__credential');
        $this->addSql('DROP TABLE __temp__credential');
        $this->addSql('CREATE INDEX IDX_57F1D4BA76ED395 ON credential (user_id)');
    }
}

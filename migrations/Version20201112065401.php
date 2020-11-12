<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201112065401 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add reset password';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reset_password_request (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , expires_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_7CE748AA76ED395 ON reset_password_request (user_id)');
        $this->addSql('DROP INDEX IDX_57F1D4BA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__credential AS SELECT id, user_id, url, login, password, name FROM credential');
        $this->addSql('DROP TABLE credential');
        $this->addSql('CREATE TABLE credential (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, url CLOB DEFAULT NULL COLLATE BINARY, login VARCHAR(255) DEFAULT NULL COLLATE BINARY, password VARCHAR(255) DEFAULT NULL COLLATE BINARY, name VARCHAR(255) NOT NULL COLLATE BINARY, CONSTRAINT FK_57F1D4BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO credential (id, user_id, url, login, password, name) SELECT id, user_id, url, login, password, name FROM __temp__credential');
        $this->addSql('DROP TABLE __temp__credential');
        $this->addSql('CREATE INDEX IDX_57F1D4BA76ED395 ON credential (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP INDEX IDX_57F1D4BA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__credential AS SELECT id, user_id, url, login, password, name FROM credential');
        $this->addSql('DROP TABLE credential');
        $this->addSql('CREATE TABLE credential (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, url CLOB DEFAULT NULL, login VARCHAR(255) DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO credential (id, user_id, url, login, password, name) SELECT id, user_id, url, login, password, name FROM __temp__credential');
        $this->addSql('DROP TABLE __temp__credential');
        $this->addSql('CREATE INDEX IDX_57F1D4BA76ED395 ON credential (user_id)');
    }
}

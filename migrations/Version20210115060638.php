<?php

declare(strict_types=1);

namespace MyProject\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210115060638 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tasks ALTER id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE tasks ALTER id DROP DEFAULT');
        $this->addSql('DROP INDEX uniq_1483a5e9e7927c74');
        $this->addSql('ALTER TABLE users ALTER id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE users ALTER id DROP DEFAULT');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE users ALTER id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE users ALTER id DROP DEFAULT');
        $this->addSql('CREATE UNIQUE INDEX uniq_1483a5e9e7927c74 ON users (email)');
        $this->addSql('ALTER TABLE tasks ALTER id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE tasks ALTER id DROP DEFAULT');
    }
}

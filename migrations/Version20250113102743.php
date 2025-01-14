<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
<<<<<<<< HEAD:migrations/Version20250104210311.php
final class Version20250104210311 extends AbstractMigration
========
final class Version20250113102743 extends AbstractMigration
>>>>>>>> 99c741ce9c6fae0332c57c1a73d14a20dbb2a636:migrations/Version20250113102743.php
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
<<<<<<<< HEAD:migrations/Version20250104210311.php
        $this->addSql('ALTER TABLE user ADD reset_token VARCHAR(255) DEFAULT NULL, ADD reset_token_expires_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user RENAME INDEX uniq_identifier_email TO UNIQ_8D93D649E7927C74');
========
        $this->addSql('ALTER TABLE category DROP slug');
>>>>>>>> 99c741ce9c6fae0332c57c1a73d14a20dbb2a636:migrations/Version20250113102743.php
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
<<<<<<<< HEAD:migrations/Version20250104210311.php
        $this->addSql('ALTER TABLE `user` DROP reset_token, DROP reset_token_expires_at');
        $this->addSql('ALTER TABLE `user` RENAME INDEX uniq_8d93d649e7927c74 TO UNIQ_IDENTIFIER_EMAIL');
========
        $this->addSql('ALTER TABLE category ADD slug VARCHAR(50) NOT NULL');
>>>>>>>> 99c741ce9c6fae0332c57c1a73d14a20dbb2a636:migrations/Version20250113102743.php
    }
}

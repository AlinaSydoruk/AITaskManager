<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250213124946 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'ALTER TABLE absence ADD created_at, updated_at, deleted_at';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE absence ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('UPDATE absence SET created_at = NOW(), updated_at = NOW()');
        $this->addSql('ALTER TABLE absence MODIFY created_at DATETIME NOT NULL, MODIFY updated_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE absence DROP created_at, DROP updated_at, DROP deleted_at');
    }
}

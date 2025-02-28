<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250228090517 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE absence ADD substitute_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE absence ADD CONSTRAINT FK_765AE0C919ECAD51 FOREIGN KEY (substitute_id) REFERENCES employee (id)');
        $this->addSql('CREATE INDEX IDX_765AE0C919ECAD51 ON absence (substitute_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE absence DROP FOREIGN KEY FK_765AE0C919ECAD51');
        $this->addSql('DROP INDEX IDX_765AE0C919ECAD51 ON absence');
        $this->addSql('ALTER TABLE absence DROP substitute_id');
    }
}

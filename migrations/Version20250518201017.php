<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250518201017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE telegram_chat (id BIGINT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE telegram_chat_user (telegram_chat_id BIGINT NOT NULL, user_id INT NOT NULL, INDEX IDX_535C07BB41DC10D3 (telegram_chat_id), INDEX IDX_535C07BBA76ED395 (user_id), PRIMARY KEY(telegram_chat_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE telegram_chat_user ADD CONSTRAINT FK_535C07BB41DC10D3 FOREIGN KEY (telegram_chat_id) REFERENCES telegram_chat (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE telegram_chat_user ADD CONSTRAINT FK_535C07BBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD telegram_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649CC0B3066 ON user (telegram_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE telegram_chat_user DROP FOREIGN KEY FK_535C07BB41DC10D3');
        $this->addSql('ALTER TABLE telegram_chat_user DROP FOREIGN KEY FK_535C07BBA76ED395');
        $this->addSql('DROP TABLE telegram_chat');
        $this->addSql('DROP TABLE telegram_chat_user');
        $this->addSql('DROP INDEX UNIQ_8D93D649CC0B3066 ON user');
        $this->addSql('ALTER TABLE user DROP telegram_id');
    }
}

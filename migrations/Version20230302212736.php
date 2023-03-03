<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230302212736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick_comment CHANGE trick_id trick_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE trick_comment ADD CONSTRAINT FK_F7292B34B281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id)');
        $this->addSql('ALTER TABLE trick_comment ADD CONSTRAINT FK_F7292B34A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F7292B34B281BE2E ON trick_comment (trick_id)');
        $this->addSql('CREATE INDEX IDX_F7292B34A76ED395 ON trick_comment (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick_comment DROP FOREIGN KEY FK_F7292B34B281BE2E');
        $this->addSql('ALTER TABLE trick_comment DROP FOREIGN KEY FK_F7292B34A76ED395');
        $this->addSql('DROP INDEX IDX_F7292B34B281BE2E ON trick_comment');
        $this->addSql('DROP INDEX IDX_F7292B34A76ED395 ON trick_comment');
        $this->addSql('ALTER TABLE trick_comment CHANGE trick_id trick_id INT NOT NULL, CHANGE user_id user_id INT NOT NULL');
    }
}

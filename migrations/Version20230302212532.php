<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230302212532 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick DROP INDEX UNIQ_D8F0A91E12469DE2, ADD INDEX IDX_D8F0A91E12469DE2 (category_id)');
        $this->addSql('ALTER TABLE trick DROP INDEX UNIQ_D8F0A91EA76ED395, ADD INDEX IDX_D8F0A91EA76ED395 (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick DROP INDEX IDX_D8F0A91E12469DE2, ADD UNIQUE INDEX UNIQ_D8F0A91E12469DE2 (category_id)');
        $this->addSql('ALTER TABLE trick DROP INDEX IDX_D8F0A91EA76ED395, ADD UNIQUE INDEX UNIQ_D8F0A91EA76ED395 (user_id)');
    }
}

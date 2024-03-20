<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240320225346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dialog_message (id UUID NOT NULL, from_user UUID NOT NULL, to_user UUID NOT NULL, text TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN dialog_message.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN dialog_message.from_user IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN dialog_message.to_user IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN dialog_message.created_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE dialog_message');
    }
}

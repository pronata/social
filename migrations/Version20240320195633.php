<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240320195633 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE friend (user_id UUID NOT NULL, friend_id UUID NOT NULL, PRIMARY KEY(user_id, friend_id))');
        $this->addSql('COMMENT ON COLUMN friend.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN friend.friend_id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE friend');
    }
}

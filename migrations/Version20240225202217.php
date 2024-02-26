<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240225202217 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE EXTENSION IF NOT EXISTS pg_trgm');
        //$this->addSql('CREATE INDEX trgm_firstname_idx ON social_user USING gin (LOWER(first_name) gin_trgm_ops)');
        //$this->addSql('CREATE INDEX trgm_second_name_idx ON social_user USING gin (LOWER(second_name) gin_trgm_ops)');
        $this->addSql('CREATE INDEX trgm_frst_scd_idx ON social_user USING gin (LOWER(first_name) gin_trgm_ops, LOWER(second_name) gin_trgm_ops)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        //$this->addSql('DROP INDEX trgm_firstname_idx');
        //$this->addSql('DROP INDEX trgm_second_name_idx');
        $this->addSql('DROP INDEX trgm_frst_scd_idx');

        $this->addSql('DROP EXTENSION IF EXISTS pg_trgm');
    }
}

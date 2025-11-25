<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration to add level column to line_pokemon table
 */
final class Version20251125123521 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add level column to line_pokemon table';
    }

    public function up(Schema $schema): void
    {
        // Add level column as nullable first
        $this->addSql('ALTER TABLE line_pokemon ADD level INT DEFAULT NULL');
        
        // Set default value (1) for existing records
        $this->addSql('UPDATE line_pokemon SET level = 1 WHERE level IS NULL');
        
        // Make the column NOT NULL
        $this->addSql('ALTER TABLE line_pokemon CHANGE level level INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // Remove the level column
        $this->addSql('ALTER TABLE line_pokemon DROP level');
    }
}


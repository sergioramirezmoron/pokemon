<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251124083228 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pokemon (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, num_pokemon INT NOT NULL, img VARCHAR(255) NOT NULL, created_by_id INT NOT NULL, INDEX IDX_62DC90F3B03A8386 (created_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE pokemon_type_pokemon (pokemon_id INT NOT NULL, type_pokemon_id INT NOT NULL, INDEX IDX_B7A84DF2FE71C3E (pokemon_id), INDEX IDX_B7A84DF32E4CA1B (type_pokemon_id), PRIMARY KEY (pokemon_id, type_pokemon_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, trainer_id INT NOT NULL, INDEX IDX_C4E0A61FFB08EDF6 (trainer_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE team_pokemon (team_id INT NOT NULL, pokemon_id INT NOT NULL, INDEX IDX_9DA5E1C4296CD8AE (team_id), INDEX IDX_9DA5E1C42FE71C3E (pokemon_id), PRIMARY KEY (team_id, pokemon_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE type_pokemon (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_pokemon (user_id INT NOT NULL, pokemon_id INT NOT NULL, INDEX IDX_3AD18EF9A76ED395 (user_id), INDEX IDX_3AD18EF92FE71C3E (pokemon_id), PRIMARY KEY (user_id, pokemon_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE pokemon ADD CONSTRAINT FK_62DC90F3B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE pokemon_type_pokemon ADD CONSTRAINT FK_B7A84DF2FE71C3E FOREIGN KEY (pokemon_id) REFERENCES pokemon (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pokemon_type_pokemon ADD CONSTRAINT FK_B7A84DF32E4CA1B FOREIGN KEY (type_pokemon_id) REFERENCES type_pokemon (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61FFB08EDF6 FOREIGN KEY (trainer_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE team_pokemon ADD CONSTRAINT FK_9DA5E1C4296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team_pokemon ADD CONSTRAINT FK_9DA5E1C42FE71C3E FOREIGN KEY (pokemon_id) REFERENCES pokemon (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_pokemon ADD CONSTRAINT FK_3AD18EF9A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_pokemon ADD CONSTRAINT FK_3AD18EF92FE71C3E FOREIGN KEY (pokemon_id) REFERENCES pokemon (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pokemon DROP FOREIGN KEY FK_62DC90F3B03A8386');
        $this->addSql('ALTER TABLE pokemon_type_pokemon DROP FOREIGN KEY FK_B7A84DF2FE71C3E');
        $this->addSql('ALTER TABLE pokemon_type_pokemon DROP FOREIGN KEY FK_B7A84DF32E4CA1B');
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61FFB08EDF6');
        $this->addSql('ALTER TABLE team_pokemon DROP FOREIGN KEY FK_9DA5E1C4296CD8AE');
        $this->addSql('ALTER TABLE team_pokemon DROP FOREIGN KEY FK_9DA5E1C42FE71C3E');
        $this->addSql('ALTER TABLE user_pokemon DROP FOREIGN KEY FK_3AD18EF9A76ED395');
        $this->addSql('ALTER TABLE user_pokemon DROP FOREIGN KEY FK_3AD18EF92FE71C3E');
        $this->addSql('DROP TABLE pokemon');
        $this->addSql('DROP TABLE pokemon_type_pokemon');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE team_pokemon');
        $this->addSql('DROP TABLE type_pokemon');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_pokemon');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

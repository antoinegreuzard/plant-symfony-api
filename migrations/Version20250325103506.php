<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250325103506 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE plant (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, variety VARCHAR(255) DEFAULT NULL, plant_type VARCHAR(20) NOT NULL, purchase_date DATE DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, description CLOB DEFAULT NULL, created_at DATETIME NOT NULL, watering_frequency INTEGER NOT NULL, fertilizing_frequency INTEGER NOT NULL, repotting_frequency INTEGER NOT NULL, pruning_frequency INTEGER NOT NULL, last_watering DATE DEFAULT NULL, last_fertilizing DATE DEFAULT NULL, last_repotting DATE DEFAULT NULL, last_pruning DATE DEFAULT NULL, sunlight_level VARCHAR(10) NOT NULL, temperature DOUBLE PRECISION DEFAULT NULL, humidity_level VARCHAR(10) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AB030D725E237E06 ON plant (name)');
        $this->addSql('CREATE TABLE plant_photo (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, plant_id INTEGER NOT NULL, image VARCHAR(255) NOT NULL, caption VARCHAR(255) DEFAULT NULL, uploaded_at DATETIME NOT NULL, CONSTRAINT FK_E76D1D901D935652 FOREIGN KEY (plant_id) REFERENCES plant (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_E76D1D901D935652 ON plant_photo (plant_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE plant');
        $this->addSql('DROP TABLE plant_photo');
    }
}

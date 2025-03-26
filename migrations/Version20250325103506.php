<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250325103506 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create tables: plant and plant_photo';
    }

    public function up(Schema $schema): void
    {
        // Création de la table plant
        $plantTable = $schema->createTable('plant');
        $plantTable->addColumn('id', 'integer', ['autoincrement' => true]);
        $plantTable->addColumn('name', 'string', ['length' => 255]);
        $plantTable->addColumn('variety', 'string', ['length' => 255, 'notnull' => false]);
        $plantTable->addColumn('plant_type', 'string', ['length' => 20]);
        $plantTable->addColumn('purchase_date', 'date', ['notnull' => false]);
        $plantTable->addColumn('location', 'string', ['length' => 255, 'notnull' => false]);
        $plantTable->addColumn('description', 'text', ['notnull' => false]);
        $plantTable->addColumn('created_at', 'datetime');
        $plantTable->addColumn('watering_frequency', 'integer');
        $plantTable->addColumn('fertilizing_frequency', 'integer');
        $plantTable->addColumn('repotting_frequency', 'integer');
        $plantTable->addColumn('pruning_frequency', 'integer');
        $plantTable->addColumn('last_watering', 'date', ['notnull' => false]);
        $plantTable->addColumn('last_fertilizing', 'date', ['notnull' => false]);
        $plantTable->addColumn('last_repotting', 'date', ['notnull' => false]);
        $plantTable->addColumn('last_pruning', 'date', ['notnull' => false]);
        $plantTable->addColumn('sunlight_level', 'string', ['length' => 10]);
        $plantTable->addColumn('temperature', 'float', ['notnull' => false]);
        $plantTable->addColumn('humidity_level', 'string', ['length' => 10]);
        $plantTable->setPrimaryKey(['id']);
        $plantTable->addUniqueIndex(['name'], 'UNIQ_AB030D725E237E06');

        // Création de la table plant_photo
        $photoTable = $schema->createTable('plant_photo');
        $photoTable->addColumn('id', 'integer', ['autoincrement' => true]);
        $photoTable->addColumn('plant_id', 'integer');
        $photoTable->addColumn('image', 'string', ['length' => 255]);
        $photoTable->addColumn('caption', 'string', ['length' => 255, 'notnull' => false]);
        $photoTable->addColumn('uploaded_at', 'datetime');
        $photoTable->setPrimaryKey(['id']);
        $photoTable->addForeignKeyConstraint(
            'plant',
            ['plant_id'],
            ['id'],
            ['onDelete' => 'CASCADE'],
            'FK_E76D1D901D935652'
        );
        $photoTable->addIndex(['plant_id'], 'IDX_E76D1D901D935652');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('plant_photo');
        $schema->dropTable('plant');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250404095906 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute une relation user_id dans la table plant avec une contrainte de clé étrangère (API fluente)';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('plant');

        $table->addColumn('user_id', 'integer', [
            'notnull' => false,
        ]);

        $table->addIndex(['user_id'], 'IDX_PLANT_USER_ID');

        $table->addForeignKeyConstraint(
            'user',
            ['user_id'],
            ['id'],
            ['onDelete' => 'CASCADE'],
            'FK_PLANT_USER_ID'
        );
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable('plant');

        $table->removeForeignKey('FK_PLANT_USER_ID');

        $table->dropIndex('IDX_PLANT_USER_ID');

        $table->dropColumn('user_id');
    }
}

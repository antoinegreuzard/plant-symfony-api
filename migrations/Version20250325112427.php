<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250325112427 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Créer la table refresh_tokens';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('refresh_tokens');

        $table->addColumn('id', 'integer', [
            'autoincrement' => true,
        ]);
        $table->addColumn('refresh_token', 'string', [
            'length' => 128,
        ]);
        $table->addColumn('username', 'string', [
            'length' => 255,
        ]);
        $table->addColumn('valid', 'datetime');

        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['refresh_token'], 'UNIQ_9BACE7E1C74F2195');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('refresh_tokens');
    }
}

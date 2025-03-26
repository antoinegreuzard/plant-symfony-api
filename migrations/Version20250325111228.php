<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250325111228 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create user table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('user');

        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('email', 'string', ['length' => 180]);
        $table->addColumn('roles', 'json');
        $table->addColumn('password', 'string', ['length' => 255]);

        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['email'], 'UNIQ_IDENTIFIER_EMAIL');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('user');
    }
}

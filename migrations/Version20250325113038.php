<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250325113038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add username column to user table and make email nullable';
    }

    public function up(Schema $schema): void
    {
        $userTable = $schema->getTable('user');

        // Rendre l'email nullable
        $userTable->getColumn('email')->setNotnull(false);

        // Ajouter la colonne username
        $userTable->addColumn('username', 'string', ['length' => 180, 'notnull' => true]);

        // Ajouter un index unique sur username
        $userTable->addUniqueIndex(['username'], 'UNIQ_IDENTIFIER_USERNAME');
    }

    public function down(Schema $schema): void
    {
        $userTable = $schema->getTable('user');

        // Supprimer l'index unique sur username
        $userTable->dropIndex('UNIQ_IDENTIFIER_USERNAME');

        // Supprimer la colonne username
        $userTable->dropColumn('username');

        // Remettre email non-nullable
        $userTable->getColumn('email')->setNotnull(true);
    }
}

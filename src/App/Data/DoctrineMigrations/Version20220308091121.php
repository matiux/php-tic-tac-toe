<?php

declare(strict_types=1);

namespace App\Data\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220308091121 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE matches CHANGE last_player last_player VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE matches CHANGE id id CHAR(36) NOT NULL COLLATE `utf8_general_ci` COMMENT \'(DC2Type:guid)\', CHANGE board board LONGTEXT NOT NULL COLLATE `utf8_general_ci` COMMENT \'(DC2Type:array)\', CHANGE last_player last_player VARCHAR(255) NOT NULL COLLATE `utf8_general_ci`, CHANGE winning_combination winning_combination LONGTEXT NOT NULL COLLATE `utf8_general_ci` COMMENT \'(DC2Type:array)\'');
    }
}

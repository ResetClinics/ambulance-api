<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230214211130 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE team ADD administrator_id INT NOT NULL');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61F4B09E92C FOREIGN KEY (administrator_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C4E0A61F4B09E92C ON team (administrator_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61F4B09E92C');
        $this->addSql('DROP INDEX IDX_C4E0A61F4B09E92C ON team');
        $this->addSql('ALTER TABLE team DROP administrator_id');
    }
}

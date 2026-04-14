<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260401090000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add payroll calculator value history with initial backfill';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE payroll_calculator_value_history (id INT AUTO_INCREMENT NOT NULL, calculator_id INT NOT NULL, value LONGTEXT DEFAULT NULL, effective_from DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', INDEX IDX_4249BCBA45199DAB (calculator_id), INDEX IDX_4249BCBAEA63C2A4 (effective_from), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE payroll_calculator_value_history ADD CONSTRAINT FK_4249BCBA45199DAB FOREIGN KEY (calculator_id) REFERENCES payroll_calculator (id) ON DELETE CASCADE');
        $this->addSql("INSERT INTO payroll_calculator_value_history (calculator_id, value, effective_from) SELECT id, value, '2015-01-01' FROM payroll_calculator");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE payroll_calculator_value_history');
    }
}

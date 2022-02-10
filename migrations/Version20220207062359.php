<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220207062359 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE candidacy (id INT AUTO_INCREMENT NOT NULL, is_validated TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE candidate ADD candidacy_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE candidate ADD CONSTRAINT FK_C8B28E4459B22434 FOREIGN KEY (candidacy_id) REFERENCES candidacy (id)');
        $this->addSql('CREATE INDEX IDX_C8B28E4459B22434 ON candidate (candidacy_id)');
        $this->addSql('ALTER TABLE job_offer ADD candidacy_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE job_offer ADD CONSTRAINT FK_288A3A4E59B22434 FOREIGN KEY (candidacy_id) REFERENCES candidacy (id)');
        $this->addSql('CREATE INDEX IDX_288A3A4E59B22434 ON job_offer (candidacy_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidate DROP FOREIGN KEY FK_C8B28E4459B22434');
        $this->addSql('ALTER TABLE job_offer DROP FOREIGN KEY FK_288A3A4E59B22434');
        $this->addSql('DROP TABLE candidacy');
        $this->addSql('DROP INDEX IDX_C8B28E4459B22434 ON candidate');
        $this->addSql('ALTER TABLE candidate DROP candidacy_id');
        $this->addSql('DROP INDEX IDX_288A3A4E59B22434 ON job_offer');
        $this->addSql('ALTER TABLE job_offer DROP candidacy_id');
    }
}

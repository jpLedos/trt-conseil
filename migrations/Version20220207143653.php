<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220207143653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidacy ADD candidate_id INT NOT NULL, ADD job_offer_id INT NOT NULL, DROP is_validated');
        $this->addSql('ALTER TABLE candidacy ADD CONSTRAINT FK_D930569D91BD8781 FOREIGN KEY (candidate_id) REFERENCES candidate (id)');
        $this->addSql('ALTER TABLE candidacy ADD CONSTRAINT FK_D930569D3481D195 FOREIGN KEY (job_offer_id) REFERENCES job_offer (id)');
        $this->addSql('CREATE INDEX IDX_D930569D91BD8781 ON candidacy (candidate_id)');
        $this->addSql('CREATE INDEX IDX_D930569D3481D195 ON candidacy (job_offer_id)');
        $this->addSql('DROP INDEX IDX_C8B28E4459B22434 ON candidate');
        $this->addSql('ALTER TABLE candidate DROP candidacy_id');
        $this->addSql('DROP INDEX IDX_288A3A4E59B22434 ON job_offer');
        $this->addSql('ALTER TABLE job_offer DROP candidacy_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidacy DROP FOREIGN KEY FK_D930569D91BD8781');
        $this->addSql('ALTER TABLE candidacy DROP FOREIGN KEY FK_D930569D3481D195');
        $this->addSql('DROP INDEX IDX_D930569D91BD8781 ON candidacy');
        $this->addSql('DROP INDEX IDX_D930569D3481D195 ON candidacy');
        $this->addSql('ALTER TABLE candidacy ADD is_validated TINYINT(1) NOT NULL, DROP candidate_id, DROP job_offer_id');
        $this->addSql('ALTER TABLE candidate ADD candidacy_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_C8B28E4459B22434 ON candidate (candidacy_id)');
        $this->addSql('ALTER TABLE job_offer ADD candidacy_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_288A3A4E59B22434 ON job_offer (candidacy_id)');
    }
}

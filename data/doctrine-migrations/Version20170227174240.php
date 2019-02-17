<?php

namespace Application;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170227174240 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE scores (id INT AUTO_INCREMENT NOT NULL, user VARCHAR(255) NOT NULL, year DOUBLE PRECISION NOT NULL, startedWithZero TINYINT(1) DEFAULT \'0\', publishDate DATETIME DEFAULT NULL, INDEX IDX_750375E8D93D649 (user), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id VARCHAR(255) NOT NULL, displayName VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE scores ADD CONSTRAINT FK_750375E8D93D649 FOREIGN KEY (user) REFERENCES users (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE scores DROP FOREIGN KEY FK_750375E8D93D649');
        $this->addSql('DROP TABLE scores');
        $this->addSql('DROP TABLE users');
    }
}

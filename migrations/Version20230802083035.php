<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230802083035 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles ADD fk_category_id INT NOT NULL');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD31687BB031D6 FOREIGN KEY (fk_category_id) REFERENCES categories (id)');
        $this->addSql('CREATE INDEX IDX_BFDD31687BB031D6 ON articles (fk_category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD31687BB031D6');
        $this->addSql('DROP INDEX IDX_BFDD31687BB031D6 ON articles');
        $this->addSql('ALTER TABLE articles DROP fk_category_id');
    }
}

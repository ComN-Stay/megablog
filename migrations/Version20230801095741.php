<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230801095741 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, fk_article_id INT NOT NULL, fk_user_id INT DEFAULT NULL, text LONGTEXT NOT NULL, date_add DATE NOT NULL, INDEX IDX_5F9E962A82FA4C0F (fk_article_id), INDEX IDX_5F9E962A5741EEB9 (fk_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A82FA4C0F FOREIGN KEY (fk_article_id) REFERENCES articles (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A5741EEB9 FOREIGN KEY (fk_user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A82FA4C0F');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A5741EEB9');
        $this->addSql('DROP TABLE comments');
    }
}

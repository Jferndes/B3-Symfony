<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250403092804 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tags (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tags_manga (tags_id INT NOT NULL, manga_id INT NOT NULL, INDEX IDX_1D3E685B8D7B4FB4 (tags_id), INDEX IDX_1D3E685B7B6461 (manga_id), PRIMARY KEY(tags_id, manga_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tags_manga ADD CONSTRAINT FK_1D3E685B8D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tags_manga ADD CONSTRAINT FK_1D3E685B7B6461 FOREIGN KEY (manga_id) REFERENCES manga (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tags_manga DROP FOREIGN KEY FK_1D3E685B8D7B4FB4');
        $this->addSql('ALTER TABLE tags_manga DROP FOREIGN KEY FK_1D3E685B7B6461');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP TABLE tags_manga');
    }
}

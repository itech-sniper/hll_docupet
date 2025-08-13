<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250813130649 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE breeds (id INT AUTO_INCREMENT NOT NULL, pet_type_id INT NOT NULL, name VARCHAR(100) NOT NULL, is_dangerous TINYINT(1) NOT NULL, description VARCHAR(500) DEFAULT NULL, INDEX IDX_FD953C82DB020C75 (pet_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pet_types (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, description VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_F44C49935E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pets (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, breed_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, date_of_birth DATE DEFAULT NULL, approximate_age INT DEFAULT NULL, sex VARCHAR(20) NOT NULL, is_dangerous_animal TINYINT(1) NOT NULL, custom_breed VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_8638EA3FC54C8C93 (type_id), INDEX IDX_8638EA3FA8B4A30F (breed_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE breeds ADD CONSTRAINT FK_FD953C82DB020C75 FOREIGN KEY (pet_type_id) REFERENCES pet_types (id)');
        $this->addSql('ALTER TABLE pets ADD CONSTRAINT FK_8638EA3FC54C8C93 FOREIGN KEY (type_id) REFERENCES pet_types (id)');
        $this->addSql('ALTER TABLE pets ADD CONSTRAINT FK_8638EA3FA8B4A30F FOREIGN KEY (breed_id) REFERENCES breeds (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE breeds DROP FOREIGN KEY FK_FD953C82DB020C75');
        $this->addSql('ALTER TABLE pets DROP FOREIGN KEY FK_8638EA3FC54C8C93');
        $this->addSql('ALTER TABLE pets DROP FOREIGN KEY FK_8638EA3FA8B4A30F');
        $this->addSql('DROP TABLE breeds');
        $this->addSql('DROP TABLE pet_types');
        $this->addSql('DROP TABLE pets');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

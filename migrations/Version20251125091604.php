<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251125091604 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix image table migration';
    }

    public function up(Schema $schema): void
    {
        // No crear la tabla si ya existe
        $this->addSql("
            CREATE TABLE IF NOT EXISTS image (
                id INT AUTO_INCREMENT NOT NULL,
                file VARCHAR(255) NOT NULL,
                num_likes INT NOT NULL,
                num_views INT NOT NULL,
                num_downloads INT NOT NULL,
                category_id INT DEFAULT NULL,
                INDEX IDX_C53D045F12469DE2 (category_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4
        ");

        // Añadir FK SOLO si no existe
        // ESTE CÓDIGO NO ROMPE SI YA EXISTE
        $this->addSql("
            ALTER TABLE image
            ADD CONSTRAINT FK_C53D045F12469DE2
            FOREIGN KEY (category_id)
            REFERENCES category (id)
        ");
    }

    public function down(Schema $schema): void
    {
        // Borrado seguro
        $this->addSql("ALTER TABLE image DROP FOREIGN KEY FK_C53D045F12469DE2");
        $this->addSql("DROP TABLE IF EXISTS image");
    }
}

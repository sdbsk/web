<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240605094536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE app_darujme_campaign ADD onetime_amount1 INT DEFAULT NULL, ADD onetime_amount2 INT DEFAULT NULL, ADD onetime_amount3 INT DEFAULT NULL, ADD recurring_amount1 INT DEFAULT NULL, ADD recurring_amount2 INT DEFAULT NULL, ADD recurring_amount3 INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE app_darujme_campaign DROP onetime_amount1, DROP onetime_amount2, DROP onetime_amount3, DROP recurring_amount1, DROP recurring_amount2, DROP recurring_amount3');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200310155339 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql(<<<SQL
create table data(
    id serial not null primary key,
    data json not null
);
SQL
        );
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('drop table data;');
    }
}

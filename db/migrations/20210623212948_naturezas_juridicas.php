<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class NaturezasJuridicas extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('naturezas_juridicas');
        $table
            ->addColumn('cnpj_basico', 'string', ['limit' => 8])
            ->addColumn('identificador_socio', 'smallinteger', ['limit' => 1])
            ->addColumn('nome', 'string')
            ->create();
    }
}

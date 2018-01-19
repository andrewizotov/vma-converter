<?php
/** 
 * @copyright   Copyright (c) http://www.manadev.com
 * @license     http://www.manadev.com/license  Proprietary License
 */

namespace Vma\VmaConverter\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface {

    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $setup->startSetup();
        $db = $setup->getConnection();

        $tableName = 'vma_last_entities';

        /** @var Table $table */
        $table = $db->newTable($setup->getTable($tableName))

            ->addColumn('product_last', Table::TYPE_BIGINT, null, ['nullable' => true])
            ->addColumn('order_last', Table::TYPE_BIGINT, null, ['nullable' => true])
            ->addColumn('category_last', Table::TYPE_BIGINT, null, ['nullable' => true]);

        $db->createTable($table);

        $db->insert($setup->getTable($tableName), ['product_last'=>null,'order_last'=>null,'category_last'=>null]);

        $setup->endSetup();
    }
}
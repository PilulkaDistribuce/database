<?php


namespace Pilulka\Database;


class StructureConvention extends \NotORM_Structure_Convention
{

    private static $referenceMap = [];

    public static function addReference($table, $accessor, $referencedTable)
    {
        self::$referenceMap["$table.$accessor"] = $referencedTable;
    }

    public function getReferencedTable($accessor, $table)
    {
        if(isset(self::$referenceMap["$table.$accessor"])) {
            return parent::getReferencedTable(
                self::$referenceMap["$table.$accessor"],
                $table
            );
        }
        return parent::getReferencedTable($accessor, $table);
    }

}


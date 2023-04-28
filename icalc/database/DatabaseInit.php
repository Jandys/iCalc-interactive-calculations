<?php

namespace icalc\db;

/**
 * Class DatabaseInit
 *
 * Contains a static method for initializing the database tables associated with several model classes.
 * @since 1.0.0
 */
class DatabaseInit
{

    /**
     * Initializes the database tables associated with several model classes by calling their create_table() methods.
     *
     * @return bool Returns true if all table creation queries executed successfully, false otherwise.
     * @since 1.0.0
     */
    public static function init()
    {
        $product = \icalc\db\model\Product::create_table();
        $service = \icalc\db\model\Service::create_table();
        $unit = \icalc\db\model\Unit::create_table();
        $icalcDesc = \icalc\db\model\IcalculationsDescription::create_table();
        $icalcs = \icalc\db\model\Icalculations::create_table();

        // Returns true if all table creation queries executed successfully, false otherwise.
        return $product && $service && $unit && $icalcDesc && $icalcs;
    }
}
<?php
/*
 *
 *   This file is part of the 'iCalc - Interactive Calculations' project.
 *
 *   Copyright (C) 2023, Jakub Jandák
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program. If not, see <https://www.gnu.org/licenses/>.
 *
 *
 */

namespace icalc\db;

use icalc\db\model\Icalculations;
use icalc\db\model\IcalculationsDescription;
use icalc\db\model\Product;
use icalc\db\model\Service;
use icalc\db\model\Unit;

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
        $product = Product::create_table();
        $service = Service::create_table();
        $unit = Unit::create_table();
        $icalcDesc = IcalculationsDescription::create_table();
        $icalcs = Icalculations::create_table();

        // Returns true if all table creation queries executed successfully, false otherwise.
        return $product && $service && $unit && $icalcDesc && $icalcs;
    }
}
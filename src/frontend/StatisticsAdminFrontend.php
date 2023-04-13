<?php
namespace icalc\fe;

class StatisticsAdminFrontend extends AbstractAdminFrontend
{

    public static function configuration()
    {
        // TODO: Implement configuration() method.
        self::populateIcalcJSData();

    }
}
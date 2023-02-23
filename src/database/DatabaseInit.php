<?php
namespace icalc\db;

class DatabaseInit
{
    public static function init()
    {
        $product = \icalc\db\model\Product::create_table();
        $service = \icalc\db\model\Service::create_table();
        $tag = \icalc\db\model\Tag::create_table();
        return $product && $service && $tag;
    }
}
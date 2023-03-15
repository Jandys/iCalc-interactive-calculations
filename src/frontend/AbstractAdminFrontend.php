<?php

namespace icalc\fe;

abstract class AbstractAdminFrontend
{

    abstract public static function configuration();

    abstract public static function configuredModalEdit($modalId, $id, $formFields): string;

    abstract public static function configureCreationModal($modalId): string;

}
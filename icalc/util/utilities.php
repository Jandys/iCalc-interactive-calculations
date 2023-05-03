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

namespace icalc\util;

use WP_REST_Request;

function callJSFunction($output, $with_script_tags = true)
{
    $js_code = $output;
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
}

function getPossibleCookieValue(WP_REST_Request $request, string $cookieName): string
{
    $cookieHeader = $request->get_header('Cookie');

    if ($cookieHeader) {
        $cookieHeaderParts = explode(';', $cookieHeader);
        foreach ($cookieHeaderParts as $cookiePart) {
            $cookiePart = trim($cookiePart);
            if (str_starts_with($cookiePart, $cookieName . '=')) {
                return explode('=', $cookiePart)[1];
            }
        }
    }
    return "";
}
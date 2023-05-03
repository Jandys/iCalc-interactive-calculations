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

namespace icalc\fe;

use function icalc\util\callJSFunction;

/**
 * Abstract class for admin frontend functionality.
 */
abstract class AbstractAdminFrontend
{
    const TOKEN_NEEDED = "Token Expired";

    /**
     * Abstract method for returning the configuration array for the frontend.
     */
    abstract public static function configuration();

    /**
     * Method for configuring the modal for editing an existing item.
     *
     * @param string $modalId The ID of the modal to be configured.
     * @param string $id The ID of the item being edited.
     * @param array $formFields The fields of the form.
     * @return string Returns the HTML for the configured modal.
     */
    public static function configuredModalEdit($modalId, $id, $formFields): string
    {
        return "";
    }

    /**
     * Method for configuring the modal for creating a new item.
     *
     * @param string $modalId The ID of the modal to be configured.
     * @return string Returns the HTML for the configured modal.
     */
    public static function configureCreationModal($modalId): string
    {
        return "";
    }

    /**
     * Method for setting the iCalc token cookie.
     */
    public static function setIcalcTokenCookie()
    {
        $maxWait = 10;
        $iter = 0;
        while (!isset($_COOKIE['icalc-token']) && $iter < $maxWait) {
            sleep(0.1);
            $iter++;
        }
    }

    /**
     * Method for populating iCalc JavaScript data.
     */
    public static function populateIcalcJSData()
    {
        $output = "populateIcalcSettings(\"" . wp_get_current_user()->ID . "\",\"" . wp_get_session_token() . "\");";
        callJSFunction($output);
    }

    /**
     * Method for calling a GET request on an endpoint with the iCalc authentication cookie.
     *
     * @param string $endpoint The endpoint to make the GET request on.
     * @return mixed|null Returns the data retrieved from the GET request or null if there was an error.
     */
    protected static function callGetOnEPWithAuthCookie($endpoint)
    {
        $endpointWithPrefix = ICALC_EP_PREFIX . $endpoint;
        $url = get_rest_url(null, $endpointWithPrefix);
        self::setIcalcTokenCookie();
        $headers = array(
            'Content-Type' => 'application/json',
            'user' => wp_get_current_user()->ID,
            'session' => wp_get_session_token(),
            'icalc-token' => $_COOKIE['icalc-token']
        );
        $args = array(
            'headers' => $headers
        );
        $response = wp_remote_get($url, $args);
        if (is_array($response)) {
            $body = wp_remote_retrieve_body($response);
            error_log("BODY: " . $body);
            $data = json_decode($body);
            return $data;
        }
        return null;
    }

}
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

use icalc\db\model\Icalculations;

class StatisticsAdminFrontend extends AbstractAdminFrontend
{

    public static function configuration()
    {
        $data = Icalculations::get_all();

        $interactionsByCalculationId = [];
        $interactionsInTime = [];

        foreach ($data as $record) {
            $interactionsByCalculationId[] = $record["calculationId"];
            $interactionsInTime[] = $record["created_at"];
        }

        $_SESSION["interactionsByCalculationId"] = self::groupByValue($interactionsByCalculationId);
        $_SESSION["interactionsInTime"] = self::groupInteractionsByTenMinutes($interactionsInTime);
        $_SESSION["interactionsData"] = $data;

        $byTime = plugins_url('graph_inTime.php', __FILE__);
        $byCalc = plugins_url('graph_byCalc.php', __FILE__);
        $makeCsvData = plugins_url('makeCsvData.php', __FILE__);

        $inTimeHasValues = true;
        $byCalcHasValues = true;

        if (empty($_SESSION["interactionsInTime"])) {
            $inTimeHasValues = false;
        }
        if (empty($_SESSION["interactionsByCalculationId"])) {
            $byCalcHasValues = false;
        }

        if ($byCalcHasValues && $inTimeHasValues) {
            echo "<a class='text-decoration-none' href='" . esc_url($makeCsvData) . "'><button  class='button btn-info position-relative mt-4 float-right'>" . __("Download Interactions CSV") . "</button></a>";
        }

        if ($inTimeHasValues) {
            echo '<img src="' . esc_url($byTime) . '" alt="Graph in time" class="mt-4">';
        }
        if ($inTimeHasValues) {
            echo '<img src="' . esc_url($byCalc) . '" alt="Graph by calculation" class="mt-4">';
        }

        if (!$byCalcHasValues && !$inTimeHasValues) {
            echo "<div><p>" . __("There are no statistics to be shown yet. Once there will be interactions with your calculations, come back.") . "</p></div>";
            return;
        }
    }

    /**
     * Groups an array of timestamps into 10-minute intervals and counts the number of occurrences in each interval.
     *
     * @param array $interactionArray The array of timestamps to group.
     *
     * @return array An array of arrays, where each inner array represents a 10-minute interval and contains the timestamp
     *               of the interval and the count of occurrences in that interval.
     */
    private static function groupInteractionsByTenMinutes($interactionArray): array
    {
        $counts = array();
        foreach ($interactionArray as $timestamp) {
            $roundedTimestamp = strtotime($timestamp) - (strtotime($timestamp) % 600);
            $tenMinuteInterval = date('Y-m-d H:i:00', $roundedTimestamp);
            if (!isset($counts[$tenMinuteInterval])) {
                $counts[$tenMinuteInterval] = 1;
            } else {
                $counts[$tenMinuteInterval]++;
            }
        }
        $result = array();
        foreach ($counts as $tenMinuteInterval => $count) {
            $result[] = array(strtotime($tenMinuteInterval), $count);
        }

        // Sort the result array by timestamp (ascending order)
        usort($result, function ($a, $b) {
            return $a[0] - $b[0];
        });

        return $result;
    }

    /**
     * Groups an array by the integer value of its elements.
     *
     * @param array $groupByValue An array to group.
     *
     * @return array An associative array with keys being the integer value of each element in the original array,
     * and values being the count of the elements with that value.
     */
    private static function groupByValue($groupByValue): array
    {
        $counts = array();
        foreach ($groupByValue as $value) {
            if (!isset($counts[intval($value)])) {
                $counts[intval($value)] = 1;
            } else {
                $counts[intval($value)]++;
            }
        }
        return $counts;
    }

}
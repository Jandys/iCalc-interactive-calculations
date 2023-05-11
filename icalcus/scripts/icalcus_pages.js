/*
 *
 *   This file is part of the 'iCalcus - Interactive Calculations' project.
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

let icalcus_pages_calculations = [[]];
let icalcus_pages_preCalculations = [];

function icalcus_evaluate_calculation(calculationId, method) {
    console.log("evaluate: " + icalcus_pages_calculations[calculationId][method]);
    let result = eval(icalcus_make_string_viable_for_eval(icalcus_pages_calculations[calculationId][method]));
    if (typeof result === 'number' || typeof result === 'string') {
        return result.toFixed(2);
    } else if (typeof result === 'object') {
        return 0;
    }
}

function icalcus_update_pre_and_calculation(domId, calculationId, calculation, method = 'sum') {
    if (!icalcus_pages_preCalculations[calculationId]) {
        icalcus_pages_preCalculations[calculationId] = [];
    }

    icalcus_pages_preCalculations[calculationId][domId] = calculation;

    let updatedCalculation;
    if (method === 'product') {
        updatedCalculation = "1";
    } else {
        updatedCalculation = "";
    }
    for (const domCalculation in icalcus_pages_preCalculations[calculationId]) {


        if (updatedCalculation) {
            updatedCalculation = updatedCalculation + icalcus_get_calculation_type(method) + icalcus_parse_toValid(icalcus_pages_preCalculations[calculationId][domCalculation], method);
        } else {
            updatedCalculation = icalcus_get_calculation_type(method) + icalcus_parse_toValid(icalcus_pages_preCalculations[calculationId][domCalculation], method);
        }

    }


    icalcus_pages_calculations[calculationId] ||= {};
    icalcus_pages_calculations[calculationId][method] = updatedCalculation;
}

let icalcus_complexCalculations = {};

function icalcus_update_complex_calculation(complexCalcId, component, value) {
    let resultInput = document.getElementById(complexCalcId);
    icalcus_complexCalculations[complexCalcId + "-" + component] = value;

    let calculation = icalcus_complexCalculations[complexCalcId];
    let matches = calculation.match(/\[(.*?)\]/g);
    for (const match of matches) {
        const componentId = match.replaceAll(/[\[\]]/g, "");
        let lastValue = icalcus_complexCalculations[complexCalcId + '-' + componentId];
        if (typeof lastValue === "undefined") {
            calculation = calculation.replaceAll(match.toString(), "");
        } else {
            calculation = calculation.replaceAll(match.toString(), lastValue);
        }
    }
    resultInput.value = resultInput.dataset.prefix + eval(icalcus_make_string_viable_for_eval(calculation)).toString() + resultInput.dataset.sufix;
}

function icalcus_parse_toValid(number, method) {
    if (parseFloat(number) < 0 && method === "subtract") {
        return '(' + number + ')';
    }
    return number;
}


function icalcus_get_calculation_type(method) {
    switch (method) {
        case "sum":
            return '+';
        case "subtract":
            return '-';
        case "product":
            return '*';
        case "complex":
            return "+";
    }

}


function getOrCreateUUID() {
    let storedUUID = localStorage.getItem('userUUID');
    if (storedUUID === null) {
        storedUUID = generateUUID();
        localStorage.setItem('userUUID', storedUUID);
    }
    return storedUUID;
}

function generateUUID() {
    return 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx'.replace(/x/g, function (c) {
        var r = Math.random() * 16 | 0,
            v = c === 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
}


let icalcus_timeouts = {};

function icalcus_register_interactions(wrappingDiv, calcId) {
    wrappingDiv.addEventListener('change', function () {
        let UUID = getOrCreateUUID();
        if (icalcus_timeouts[UUID]) {
            if (icalcus_timeouts[UUID] != null) {
                clearTimeout(icalcus_timeouts[UUID]);
                icalcus_timeouts[UUID] = null;
            }
        }

        let querySelectorAll = wrappingDiv.querySelectorAll('input');
        let body = {};
        for (const input of querySelectorAll) {
            let ins = {};
            ins["type"] = input.type;
            ins["value"] = input.value;
            ins["checked"] = input.checked;
            ins["checked"] = input.checked;
            body[input.id] = ins;
        }
        body["calculationId"] = calcId;
        icalcus_timeouts[UUID] = setTimeout(() => {
            const xhr = new XMLHttpRequest();
            const url = '/wp-json/icalcus/v1/icalcusulations/interactions';
            const data = JSON.stringify({
                calculationId: calcId,
                body: body,
                userId: UUID
            });

            xhr.open('POST', url);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                    } else {
                        console.log('Error registering interaction:', xhr.status);
                    }
                }
            };
            xhr.send(data);
        }, 6500);

    })
}

function icalcus_make_string_viable_for_eval(evalString) {
    while (["+", "-", "*", "/", " ", "\s"].includes(evalString.slice(-1))) {
        evalString = evalString.slice(0, -1);
    }
    evalString = evalString.replace(/--/g, "+");
    evalString = evalString.replace(/\+\+/g, "+");
    return evalString;
}

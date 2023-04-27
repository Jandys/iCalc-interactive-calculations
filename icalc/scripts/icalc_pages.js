let icalc_pages_calculations = [[]];
let icalc_pages_preCalculations = [];

function icalc_evaluate_calculation(calculationId, method) {
    console.log("evaluate: " + icalc_pages_calculations[calculationId][method]);
    let result = eval(icalc_pages_calculations[calculationId][method]);
    if (typeof result === 'number' || typeof result === 'string') {
        return result;
    } else if (typeof result === 'object') {
        return 0;
    }
}

function icalc_update_pre_and_calculation(domId, calculationId, calculation, method = 'sum') {
    if (!icalc_pages_preCalculations[calculationId]) {
        icalc_pages_preCalculations[calculationId] = [];
    }


    icalc_pages_preCalculations[calculationId][domId] = calculation;

    let updatedCalculation = "";
    for (const domCalculation in icalc_pages_preCalculations[calculationId]) {


        if (updatedCalculation) {
            updatedCalculation = '(' + updatedCalculation + ')' + icalc_get_calculation_type(method) + '(' + icalc_pages_preCalculations[calculationId][domCalculation] + ')';
        } else {
            updatedCalculation = icalc_get_calculation_type(method) + icalc_pages_preCalculations[calculationId][domCalculation];
        }

    }


    icalc_pages_calculations[calculationId] ||= {};
    icalc_pages_calculations[calculationId][method] = updatedCalculation;
}


function icalc_get_calculation_type(method) {
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


let icalc_timeouts = {};

function icalc_register_interactions(wrappingDiv, calcId) {
    wrappingDiv.addEventListener('change', function () {
        let UUID = getOrCreateUUID();
        if (icalc_timeouts[UUID]) {
            if (icalc_timeouts[UUID] != null) {
                clearTimeout(icalc_timeouts[UUID]);
                icalc_timeouts[UUID] = null;
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
        icalc_timeouts[UUID] = setTimeout(() => {
            const xhr = new XMLHttpRequest();
            const url = '/wp-json/icalc/v1/icalculations/interactions';
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


function icalc_process_product_creation(modalId) {

    let nameElement = document.getElementById(modalId + '_name_form');
    let descriptionElement = document.getElementById(modalId + '_desc_form');
    let priceElement = document.getElementById(modalId + '_price_form');
    let unitElement = document.getElementById(modalId + '_unit_form');
    let minQualityElement = document.getElementById(modalId + '_min_quantity_form');
    let displayTypeElement = document.getElementById(modalId + '_display_type_form');


}
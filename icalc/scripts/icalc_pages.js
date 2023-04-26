let icalc_pages_calculations = [];
let icalc_pages_preCalculations = [];

function icalc_evaluate_calculation(calculationId) {
    let result = eval(icalc_pages_calculations[calculationId]);
    if (typeof result === 'number' || typeof result === 'string') {
        return result;
    } else if (typeof result === 'object') {
        return 0;
    }
}

function icalc_update_pre_and_calculation(domId, calculationId, calculation, type = '+') {
    if (!icalc_pages_preCalculations[calculationId]) {
        icalc_pages_preCalculations[calculationId] = [];
    }
    icalc_pages_preCalculations[calculationId][domId] = calculation;

    let updatedCalculation = "";
    for (const domCalculation in icalc_pages_preCalculations[calculationId]) {

        if (updatedCalculation) {
            updatedCalculation = updatedCalculation + type + icalc_pages_preCalculations[calculationId][domCalculation];
        } else {
            updatedCalculation = icalc_pages_preCalculations[calculationId][domCalculation];
        }
    }
    icalc_pages_calculations[calculationId] = updatedCalculation;
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
        console.log("on change");
        let UUID = getOrCreateUUID();
        if(icalc_timeouts[UUID]){
            if (icalc_timeouts[UUID] != null) {
                clearTimeout(icalc_timeouts[UUID]);
                icalc_timeouts[UUID] = null;
            }
        }

        let querySelectorAll = wrappingDiv.querySelectorAll('input');
        let body = {};
        for (const input of querySelectorAll) {
            console.log(input);
            let ins = {};
            ins["type"] = input.type;
            ins["value"] = input.value;
            ins["checked"] = input.checked;
            ins["checked"] = input.checked;
            body[input.id] = ins;
        }
        body["calculationId"] = calcId;
        icalc_timeouts[UUID] = setTimeout(() => {
            console.log("Interaction!");
            console.log(body);
        }, 6500);

    })


}
let icalc_pages_calculations = [];
let icalc_pages_preCalculations = [];

function icalc_evaluate_calculation(calculationId) {
    let result = eval(icalc_pages_calculations[calculationId]);
    if (typeof result === 'number' || typeof result === 'string'){
        return result;
    }else if(typeof result === 'object'){
        return 0;
    }
}

function icalc_update_pre_and_calculation(domId,calculationId, calculation, type = '+'){
    if(!icalc_pages_preCalculations[calculationId]){
        icalc_pages_preCalculations[calculationId] = [];
    }
    icalc_pages_preCalculations[calculationId][domId] = calculation;

    let updatedCalculation = "";
    for(const domCalculation in  icalc_pages_preCalculations[calculationId]){

        if(updatedCalculation){
            updatedCalculation = updatedCalculation + type + icalc_pages_preCalculations[calculationId][domCalculation];
        }else {
            updatedCalculation = icalc_pages_preCalculations[calculationId][domCalculation];
        }
    }
    icalc_pages_calculations[calculationId] = updatedCalculation;
}
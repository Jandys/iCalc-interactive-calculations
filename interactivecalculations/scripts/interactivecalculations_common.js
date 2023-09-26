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

class interactivecalculations_ObservableArray {
    constructor(array) {
        this.array = array;
        this.listeners = new Set();
    }

    get(id) {
        return this.array[id];
    }

    getFrom(id, from) {
        return this.array[id][from];
    }

    set(id, val) {
        return this.array[id] = val;
    }

    setFrom(id, from, val) {
        this.array[id][from] = val;
        this.notify('setFrom', val, id);
    }

    push(...args) {
        const result = this.array.push(...args);
        this.notify('push', args);
        return result;
    }


    pushToFrom(id, from, ...args) {
        const result = this.array[id][from].push(...args);
        this.notify('pushToFrom', args, id);
        return result;
    }

    pop() {
        const result = this.array.pop();
        this.notify('pop');
        return result;
    }

    addListener(listener) {
        this.listeners.add(listener);
    }

    removeListener(listener) {
        this.listeners.delete(listener);
    }

    notify(action, args, calculationId) {
        for (const listener of this.listeners) {
            listener(action, args, calculationId);
        }
    }
}


let interactivecalculations_calculations = new interactivecalculations_ObservableArray([]);
let interactivecalculations_calculationElements = [];
let interactivecalculations_calculationElementConfigurations = [];
interactivecalculations_calculations.addListener((action, args, calculationId) => {
    if (interactivecalculations_calculationElements[calculationId]) {
        for (const element of interactivecalculations_calculationElements[calculationId]) {
            let prefix = interactivecalculations_calculationElementConfigurations[calculationId]['sum-prefix']
            let postfix = interactivecalculations_calculationElementConfigurations[calculationId]['sum-postfix']
            element.value = prefix + interactivecalculations_calculate(calculationId, element).toString() + postfix;
        }
    }
});


function interactivecalculations_getProductById(id) {
    const xhr = new XMLHttpRequest();
    const url = `/wp-json/interactivecalculations/v1/products/${id}`;
    xhr.open('GET', url);
    xhr.setRequestHeader('Content-Type', 'application/json');
    return xhr;
}


function interactivecalculations_getServiceById(id) {
    const xhr = new XMLHttpRequest();
    const url = `/wp-json/interactivecalculations/v1/services/${id}`;
    xhr.open('GET', url);
    xhr.setRequestHeader('Content-Type', 'application/json');
    return xhr;
}


function interactivecalculations_getCalculationDescriptionById(id) {
    const xhr = new XMLHttpRequest();
    const url = `/wp-json/interactivecalculations/v1/interactivecalculationsulation-descriptions/${id}`;
    xhr.open('GET', url);
    xhr.setRequestHeader('Content-Type', 'application/json');
    return xhr;
}


function interactivecalculations_displayComponent(component, calculationId, components) {
    switch (component.type) {
        case "product":
            return interactivecalculations_displayProduct(component, calculationId);
        case "service":
            return interactivecalculations_displayService(component, calculationId);
        case "genericComponent":
        case "calculationComponent":
            return interactivecalculations_displayGenericComponent(component, calculationId, components);
        default:
            return document.createElement("div");
    }

}

function interactivecalculations_displayProduct(product, calculationId) {
    const productDiv = document.createElement("div");
    productDiv.classList.add("form-group");
    productDiv.classList.add("row");

    let productXHR = interactivecalculations_getProductById(product.id);

    productXHR.onreadystatechange = function () {
        if (productXHR.readyState === XMLHttpRequest.DONE) {
            if (productXHR.status === 200) {
                let productData = JSON.parse(productXHR.responseText);

                productDiv.appendChild(interactivecalculations_getDisplayType(product, productData, calculationId));

            } else {
                console.log('Error fetching prodcut data');
            }
        }
    };
    productXHR.send();

    return productDiv;
}

function interactivecalculations_displayService(service, calculationId) {
    const serviceDiv = document.createElement("div");
    serviceDiv.classList.add("form-group");
    serviceDiv.classList.add("row");

    let serviceXHR = interactivecalculations_getServiceById(service.id);

    serviceXHR.onreadystatechange = function () {
        if (serviceXHR.readyState === XMLHttpRequest.DONE) {
            if (serviceXHR.status === 200) {
                let productData = JSON.parse(serviceXHR.responseText);

                serviceDiv.appendChild(interactivecalculations_getDisplayType(service, productData, calculationId));

            } else {
                console.log('Error fetching prodcut data');
            }
        }
    };
    serviceXHR.send();

    return serviceDiv;
}

function interactivecalculations_displayGenericComponent(genericComponent, calculationId, components) {
    return interactivecalculations_getDisplayType(genericComponent, {}, calculationId, components);

}


function interactivecalculations_createCustomStyle(customStyles) {
    if (customStyles.trim().length !== 0) {
        const styles = document.createElement("style");
        styles.innerText = customStyles;
        return styles;
    }
    return false;
}


function interactivecalculations_getDisplayType(component, componentData, calculationId, components) {
    switch (component.displayType.toLowerCase()) {
        case "number":
        case "number input":
            return interactivecalculations_getNumberDisplayType(component, componentData, calculationId);

        case "slider":
        case "range":
            return interactivecalculations_getSliderDisplayType(component, componentData, calculationId);

        case "label":
            return interactivecalculations_getLabelDisplayType(component, componentData, calculationId);

        case "horizontal rule":
        case "hr":
            return interactivecalculations_getHorizontalRule(component);

        case "sum":
            return interactivecalculations_getSumDisplayType(component, calculationId);

        case "subtract calculation":
        case "subtract-calculation":
            return interactivecalculations_getSubtractCalculation(component, calculationId);

        case "product calculation":
        case "product-calculation":
            return interactivecalculations_getProductCalculationDisplayType(component, calculationId);

        case "text":
            return interactivecalculations_getTextDisplayType(component, componentData, calculationId);

        case "checkbox":
            return interactivecalculations_getCheckboxDisplayType(component, componentData, calculationId);

        case "list":
            return interactivecalculations_getListDisplayType(component, componentData, calculationId);

        case "spacer":
            return interactivecalculations_getSpacerDisplayType(component);

        case "complex calculation":
            return interactivecalculations_getComplexCalculationDisplayType(component, componentData, components);

        default:
            return document.createElement("div");


    }
}

function interactivecalculations_getSumDisplayType(component, calculationId) {
    const wrapper = document.createElement("div");
    const colLabel = document.createElement("div");
    colLabel.classList.add("col");
    colLabel.classList.add("form-label");
    const label = document.createElement("label");
    label.innerText = component.conf.configuration['custom-label'];
    label.setAttribute('for', `${component.type}-${component.id}-sum-calculation`);
    if (component.conf.configuration["label-class"]) {
        let labelClasses = component.conf.configuration["label-class"].split(";");
        for (const labelClass of labelClasses) {
            label.classList.add(labelClass);
        }
    }
    colLabel.appendChild(label);
    wrapper.appendChild(colLabel);


    const colResult = document.createElement("div");
    colResult.classList.add("col");
    const inputElement = document.createElement('input');
    inputElement.type = 'text';
    inputElement.id = `${component.type}-${component.id}-sum-calculation`;
    inputElement.disabled = true
    if (component.conf.configuration["input-classes"]) {
        let inputClasses = component.conf.configuration["input-classes"].split(";");
        for (const inputClass of inputClasses) {
            inputElement.classList.add(inputClass);
        }
    }

    interactivecalculations_calculationElementConfigurations[calculationId] = component.conf.configuration;

    if (!interactivecalculations_calculationElements[calculationId]) {
        interactivecalculations_calculationElements[calculationId] = [];
    }
    interactivecalculations_calculationElements[calculationId].push(inputElement);

    colResult.appendChild(inputElement);
    wrapper.appendChild(colResult);
    return wrapper;
}

function interactivecalculations_getProductCalculationDisplayType(component, calculationId) {
    const wrapper = document.createElement("div");
    const colLabel = document.createElement("div");
    colLabel.classList.add("col");
    colLabel.classList.add("form-label");
    const label = document.createElement("label");
    label.innerText = component.conf.configuration['custom-label'];
    label.setAttribute('for', `${component.type}-${component.id}-product-calculation`);
    if (component.conf.configuration["label-class"]) {
        let labelClasses = component.conf.configuration["label-class"].split(";");
        for (const labelClass of labelClasses) {
            label.classList.add(labelClass);
        }
    }
    colLabel.appendChild(label);
    wrapper.appendChild(colLabel);


    const colResult = document.createElement("div");
    colResult.classList.add("col");
    const inputElement = document.createElement('input');
    inputElement.type = 'text';
    inputElement.id = `${component.type}-${component.id}-product-calculation`;
    inputElement.disabled = true
    if (component.conf.configuration["input-classes"]) {
        let inputClasses = component.conf.configuration["input-classes"].split(";");
        for (const inputClass of inputClasses) {
            inputElement.classList.add(inputClass);
        }
    }

    interactivecalculations_calculationElementConfigurations[calculationId] = component.conf.configuration;

    if (!interactivecalculations_calculationElements[calculationId]) {
        interactivecalculations_calculationElements[calculationId] = [];
    }
    interactivecalculations_calculationElements[calculationId].push(inputElement);

    colResult.appendChild(inputElement);
    wrapper.appendChild(colResult);
    return wrapper;
}

function interactivecalculations_getSubtractCalculation(component, calculationId) {
    const wrapper = document.createElement("div");
    const colLabel = document.createElement("div");
    colLabel.classList.add("col");
    colLabel.classList.add("form-label");
    const label = document.createElement("label");
    label.innerText = component.conf.configuration['custom-label'];
    label.setAttribute('for', `${component.type}-${component.id}-subtract-calculation`);
    if (component.conf.configuration["label-class"]) {
        let labelClasses = component.conf.configuration["label-class"].split(";");
        for (const labelClass of labelClasses) {
            label.classList.add(labelClass);
        }
    }
    colLabel.appendChild(label);
    wrapper.appendChild(colLabel);


    const colResult = document.createElement("div");
    colResult.classList.add("col");
    const inputElement = document.createElement('input');
    inputElement.id = `${component.type}-${component.id}-subtract-calculation`
    inputElement.type = 'text';
    inputElement.disabled = true
    inputElement.dataset.subtractValue = component.conf.configuration["subtract-value"];
    if (component.conf.configuration["input-classes"]) {
        let inputClasses = component.conf.configuration["input-classes"].split(";");
        for (const inputClass of inputClasses) {
            inputElement.classList.add(inputClass);
        }
    }

    interactivecalculations_calculationElementConfigurations[calculationId] = component.conf.configuration;

    if (!interactivecalculations_calculationElements[calculationId]) {
        interactivecalculations_calculationElements[calculationId] = [];
    }
    interactivecalculations_calculationElements[calculationId].push(inputElement);

    colResult.appendChild(inputElement);
    wrapper.appendChild(colResult);
    return wrapper;
}


function interactivecalculations_getComplexCalculationDisplayType(component, componentData, components) {
    const wrapper = document.createElement("div");
    const colLabel = document.createElement("div");
    colLabel.classList.add("col");
    colLabel.classList.add("form-label");
    const label = document.createElement("label");
    label.innerText = component.conf.configuration['custom-label'];
    label.setAttribute('for', `${component.type}-${component.id}-complex-calculation`);
    if (component.conf.configuration["label-class"]) {
        let labelClasses = component.conf.configuration["label-class"].split(";");
        for (const labelClass of labelClasses) {
            label.classList.add(labelClass);
        }
    }
    colLabel.appendChild(label);
    wrapper.appendChild(colLabel);

    const colResult = document.createElement("div");
    colResult.classList.add("col");
    const inputElement = document.createElement('input');
    inputElement.id = `${component.type}-${component.id}-complex-calculation`
    inputElement.type = 'text';
    inputElement.disabled = true
    inputElement.dataset.prefix = component.conf.configuration["sum-prefix"];
    inputElement.dataset.sufix = component.conf.configuration["sum-postfix"];
    if (component.conf.configuration["input-classes"]) {
        let inputClasses = component.conf.configuration["input-classes"].split(";");
        for (const inputClass of inputClasses) {
            inputElement.classList.add(inputClass);
        }
    }

    let calculationDescription = component.conf.configuration["complex-calculation"];
    let regex = /\[([^\]]+)\/[^\]]+\]/g;
    calculationDescription = calculationDescription.replace(regex, "[$1]");
    complexCalculations[`${component.type}-${component.id}-complex-calculation`] = calculationDescription;


    setTimeout(() => {
        for (const replaceableComponent of components) {
            if (!replaceableComponent.domId.includes("calculation") && listenableDisplayTypes.includes(replaceableComponent.displayType.toLowerCase())) {

                if (calculationDescription.includes('[' + replaceableComponent.parentComponent + ']')) {

                    const inputElement = document.getElementById(`${replaceableComponent.domId}-` + replaceableComponent.displayType.toLowerCase().split(" ")[0]);
                    inputElement.addEventListener('change', function () {
                        let value;
                        if (replaceableComponent.displayType.toLowerCase().split(" ")[0] === "checkbox") {
                            value = inputElement.checked ? replaceableComponent.conf.configuration["base-value"] : replaceableComponent.conf.configuration["unchecked-value"];
                        } else if (replaceableComponent.displayType.toLowerCase().split(" ")[0] === "list") {
                            value = replaceableComponent.conf.configuration["list-value" + Number(inputElement.selectedIndex - 1)];
                        } else {
                            value = inputElement.value;
                        }

                        interactivecalculations_update_complex_calculation(`${component.type}-${component.id}-complex-calculation`, replaceableComponent.parentComponent, value);

                    });
                }
            }
        }
    }, 125);


    colResult.appendChild(inputElement);
    wrapper.appendChild(colResult);
    return wrapper;
}


function interactivecalculations_update_complex_calculation(complexCalcId, component, value) {
    let resultInput = document.getElementById(complexCalcId);
    complexCalculations[complexCalcId + "-" + component] = value;

    let calculation = complexCalculations[complexCalcId];
    let matches = calculation.match(/\[(.*?)\]/g);
    for (const match of matches) {
        const componentId = match.replaceAll(/[\[\]]/g, "");
        let lastValue = complexCalculations[complexCalcId + '-' + componentId];
        if (typeof lastValue === "undefined") {
            calculation = calculation.replaceAll(match.toString(), "");
        } else {
            calculation = calculation.replaceAll(match.toString(), lastValue);
        }
    }
    resultInput.value = resultInput.dataset.prefix + eval(interactivecalculations_make_string_viable_for_eval(calculation)) + resultInput.dataset.sufix;
}

let complexCalculations = {};
let listenableDisplayTypes = ['list', "number", "number input", "slider", "checkbox"]

function interactivecalculations_calculate(idOfCalculation, calculationElement) {
    let result = 0;
    if (calculationElement.id.includes("subtract")) {
        result = calculationElement.dataset.subtractValue;
    }
    if (calculationElement.id.includes("product")) {
        result = 1;
    }
    const calculationObject = interactivecalculations_calculations.get(idOfCalculation);
    for (const calcPart in calculationObject) {
        switch (true) {
            case calculationElement.id.includes("sum"):
                result += interactivecalculations_simpleCalculation(calculationObject[calcPart]);
                break;
            case calculationElement.id.includes("product"):
                result *= interactivecalculations_simpleCalculation(calculationObject[calcPart]);
                break;
            case calculationElement.id.includes("subtract"):
                result -= interactivecalculations_simpleCalculation(calculationObject[calcPart]);
                break;
        }
    }
    return result;
}


function interactivecalculations_make_string_viable_for_eval(evalString) {
    while (["+", "-", "*", "/", "\s", " "].includes(evalString.slice(-1))) {
        evalString = evalString.slice(0, -1);
    }
    evalString = evalString.replace(/--/g, "+");
    evalString = evalString.replace(/\+\+/g, "+");
    return evalString;
}

/**
 *
 * @param calculationPart {"baseValue":15.4, "times": 5, "negative": false}
 */
function interactivecalculations_simpleCalculation(calculationPart) {
    let preCalc = eval(calculationPart["baseValue"] * calculationPart["times"]);
    return calculationPart["negative"] ? eval(preCalc * -1) : preCalc;
}


function interactivecalculations_getNumberDisplayType(component, componentData, calculationId) {
    const showLabel = component.conf.configuration["show-label"];
    const wrapper = document.createElement("div");
    if (showLabel === "true") {
        const colLabel = document.createElement("div");
        colLabel.classList.add("col");
        colLabel.classList.add("form-label");
        const label = document.createElement("label");
        if (componentData.name) {
            label.innerText = componentData.name;
        } else {
            label.innerText = component.conf.configuration['custom-label'];
        }
        label.setAttribute('for', `${component.domId}-number`);
        if (component.conf.configuration["label-class"]) {
            let labelClasses = component.conf.configuration["label-class"].split(";");
            for (const labelClass of labelClasses) {
                label.classList.add(labelClass);
            }
        }
        colLabel.appendChild(label);
        wrapper.appendChild(colLabel);
    }

    const colInput = document.createElement("div");
    colInput.classList.add("col");
    const inputElement = document.createElement('input');
    inputElement.type = 'number';
    inputElement.classList.add("form-control");

    inputElement.setAttribute('id', `${component.domId}-number`);
    inputElement.setAttribute('name', `${component.domId}-number`);
    inputElement.setAttribute('min', '0');
    if (componentData["min_quantity"]) {
        inputElement.setAttribute('step', componentData["min_quantity"]);
        inputElement.setAttribute('value', componentData["min_quantity"]);
    } else if (component.conf.configuration["step"]) {
        inputElement.setAttribute('step', component.conf.configuration["step"]);
        inputElement.setAttribute('value', component.conf.configuration["step"]);
    }

    colInput.onchange = () => {
        let baseValue;
        if (componentData["price"]) {
            baseValue = componentData["price"];
        } else {
            baseValue = component.conf.configuration["base-value"];
        }

        const inputCalculation = {
            "baseValue": Number(baseValue), "times": Number(inputElement.value), "negative": false
        }
        interactivecalculations_calculations.setFrom(calculationId, `${component.domId}-number`, inputCalculation);
    }

    colInput.appendChild(inputElement);
    wrapper.appendChild(colInput);
    return wrapper;
}


function interactivecalculations_getSliderDisplayType(component, componentData, calculationId) {
    const showLabel = component.conf.configuration["show-label"];
    const wrapper = document.createElement("div");
    if (showLabel === "true") {
        const colLabel = document.createElement("div");
        colLabel.classList.add("col");
        colLabel.classList.add("form-label");
        const label = document.createElement("label");
        if (componentData.name) {
            label.innerText = componentData.name;
        } else {
            label.innerText = component.conf.configuration['custom-label'];
        }
        label.setAttribute('for', `${component.domId}-slider`);
        if (component.conf.configuration["label-class"]) {
            let labelClasses = component.conf.configuration["label-class"].split(";");
            for (const labelClass of labelClasses) {
                label.classList.add(labelClass);
            }
        }
        colLabel.appendChild(label);
        wrapper.appendChild(colLabel);
    }

    const colInput = document.createElement("div");
    colInput.classList.add("col");
    const inputElement = document.createElement('input');
    inputElement.type = 'range';
    inputElement.classList.add("form-range");
    if (component.conf.configuration["input-classes"]) {
        let inputClasses = component.conf.configuration["input-classes"].split(";");
        for (const inputClass of inputClasses) {
            inputElement.classList.add(inputClass);
        }
    }

    inputElement.setAttribute('id', `${component.domId}-slider`);
    inputElement.setAttribute('name', `${component.domId}-slider`);
    inputElement.setAttribute('min', '0');
    if (componentData["min_quantity"]) {
        inputElement.setAttribute('step', componentData["min_quantity"]);
        inputElement.setAttribute('value', componentData["min_quantity"]);
    }

    if (component.conf.configuration["slider-step"]) {
        inputElement.setAttribute('step', component.conf.configuration["slider-step"]);
    }

    if (component.conf.configuration["slider-max"]) {
        inputElement.setAttribute('max', component.conf.configuration["slider-max"]);
    }

    const displayValue = document.createElement("div");
    displayValue.classList.add("interactivecalculations-display-slider-value");

    colInput.onchange = () => {
        const inputCalculation = {
            "baseValue": Number(componentData["price"]), "times": Number(inputElement.value), "negative": false
        }

        if (component.conf.configuration["slider-show-value"]) {
            let unit = '';
            if (componentData["unit"]) {
                unit = componentData["unit"];
            }

            displayValue.textContent = inputElement.value + " " + unit;
        } else {
            displayValue.innerHTML = ""
        }

        interactivecalculations_calculations.setFrom(calculationId, `${component.domId}-slider`, inputCalculation);
    }

    colInput.appendChild(inputElement);
    colInput.appendChild(displayValue);
    wrapper.appendChild(colInput);
    return wrapper;
}

function interactivecalculations_getCheckboxDisplayType(component, componentData, calculationId) {
    const showLabel = component.conf.configuration["show-label"];
    const wrapper = document.createElement("div");
    if (showLabel === "true") {
        const colLabel = document.createElement("div");
        colLabel.classList.add("col");
        colLabel.classList.add("form-label");
        const label = document.createElement("label");
        if (componentData.name) {
            label.innerText = componentData.name;
        } else {
            label.innerText = component.conf.configuration['custom-label'];
        }
        label.setAttribute('for', `${component.domId}-checkbox`);
        if (component.conf.configuration["label-class"]) {
            let labelClasses = component.conf.configuration["label-class"].split(";");
            for (const labelClass of labelClasses) {
                label.classList.add(labelClass);
            }
        }
        colLabel.appendChild(label);
        wrapper.appendChild(colLabel);
    }

    const colInput = document.createElement("div");
    colInput.classList.add("col");
    const inputElement = document.createElement('input');
    inputElement.type = 'checkbox';
    inputElement.classList.add("form-control");
    if (component.conf.configuration["input-classes"]) {
        let inputClasses = component.conf.configuration["input-classes"].split(";");
        for (const inputClass of inputClasses) {
            inputElement.classList.add(inputClass);
        }
    }

    colInput.onchange = () => {
        let value;
        if (componentData["price"]) {
            value = componentData["price"];
        } else {
            value = component.conf.configuration["base-value"];
        }

        const times = inputElement.checked ? 1 : 0;
        const inputCalculation = {
            "baseValue": Number(value), "times": Number(times), "negative": false
        }

        interactivecalculations_calculations.setFrom(calculationId, `${component.domId}-checkbox`, inputCalculation);
    }


    inputElement.setAttribute('id', `${component.domId}-checkbox`);
    inputElement.setAttribute('name', `${component.domId}-checkbox`);

    colInput.appendChild(inputElement);
    wrapper.appendChild(colInput);
    return wrapper;
}

function interactivecalculations_getTextDisplayType(component, componentData, calculationId) {
    const showLabel = component.conf.configuration["show-label"];
    const wrapper = document.createElement("div");
    if (showLabel === "true") {
        const colLabel = document.createElement("div");
        colLabel.classList.add("col");
        colLabel.classList.add("form-label");
        const label = document.createElement("label");
        if (componentData.name) {
            label.innerText = componentData.name;
        } else {
            label.innerText = component.conf.configuration['custom-label'];
        }

        label.setAttribute('for', `${component.domId}-text`);
        if (component.conf.configuration["label-class"]) {
            let labelClasses = component.conf.configuration["label-class"].split(";");
            for (const labelClass of labelClasses) {
                label.classList.add(labelClass);
            }
        }
        colLabel.appendChild(label);
        wrapper.appendChild(colLabel);
    }

    const colInput = document.createElement("div");
    colInput.classList.add("col");
    const inputElement = document.createElement('input');
    inputElement.type = 'text';
    inputElement.classList.add("form-text");
    if (component.conf.configuration["input-classes"]) {
        let inputClasses = component.conf.configuration["input-classes"].split(";");
        for (const inputClass of inputClasses) {
            inputElement.classList.add(inputClass);
        }
    }

    inputElement.setAttribute('id', `${component.domId}-text`);
    inputElement.setAttribute('name', `${component.domId}-text`);


    colInput.appendChild(inputElement);
    wrapper.appendChild(colInput);
    return wrapper;
}


function interactivecalculations_getLabelDisplayType(component) {
    const label = document.createElement("label")
    label.textContent = component.conf.configuration["custom-label"];
    return label;
}

function interactivecalculations_getHorizontalRule(component) {
    const hr = document.createElement("hr");
    if (component.conf.configuration["input-classes"]) {
        let inputClasses = component.conf.configuration["input-classes"].split(";");
        for (const inputClass of inputClasses) {
            hr.classList.add(inputClass);
        }
    }
    return hr;

}

function interactivecalculations_getSpacerDisplayType(component) {
    const spacer = document.createElement("div");
    if (component.conf.configuration["input-classes"]) {
        let inputClasses = component.conf.configuration["input-classes"].split(";");
        for (const inputClass of inputClasses) {
            spacer.classList.add(inputClass);
        }
    }
    return spacer;
}


function interactivecalculations_getListDisplayType(component, componentData, calculationId) {
    const showLabel = component.conf.configuration["show-label"];
    const wrapper = document.createElement("div");
    if (showLabel === "true") {
        const colLabel = document.createElement("div");
        colLabel.classList.add("col");
        colLabel.classList.add("form-label");
        const label = document.createElement("label");
        if (componentData.name) {
            label.innerText = componentData.name;
        } else {
            label.innerText = component.conf.configuration['custom-label'];
        }
        label.setAttribute('for', `${component.domId}-list`);
        if (component.conf.configuration["label-class"]) {
            let labelClasses = component.conf.configuration["label-class"].split(";");
            for (const labelClass of labelClasses) {
                label.classList.add(labelClass);
            }
        }
        colLabel.appendChild(label);
        wrapper.appendChild(colLabel);
    }


    const colInput = document.createElement("div");
    colInput.classList.add("col");
    const select = document.createElement('select');
    select.classList.add("form-control")
    select.id = `${component.domId}-list`;

    if (component.conf.configuration["input-classes"]) {
        let inputClasses = component.conf.configuration["input-classes"].split(";");
        for (const inputClass of inputClasses) {
            select.classList.add(inputClass);
        }
    }

    let currentOption = null;
    let currentValue = null;
    for (const attribute in component.conf.configuration) {
        if (currentOption && currentValue) {
            const option = document.createElement("option");
            option.value = currentValue;
            option.innerText = currentOption;

            select.appendChild(option);
            currentValue = null;
            currentOption = null;
        }
        if (attribute.startsWith('list-option')) {
            if (component.conf.configuration[attribute]) {
                currentOption = component.conf.configuration[attribute];
            } else {
                currentValue = null;
                currentOption = null;
            }
        } else if (attribute.startsWith('list-value')) {
            if (component.conf.configuration[attribute]) {
                currentValue = component.conf.configuration[attribute];
            } else {
                currentValue = null;
                currentOption = null;
            }
        }
    }


    colInput.appendChild(select);
    wrapper.appendChild(colInput);
    return wrapper;
}

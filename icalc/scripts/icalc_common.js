class ICalc_ObservableArray {
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


let icalc_calculations = new ICalc_ObservableArray([]);
let icalc_calculationElements = [];
let icalc_calculationElementConfigurations = [];
icalc_calculations.addListener((action, args, calculationId) => {
    if (icalc_calculationElements[calculationId]) {
        for (const element of icalc_calculationElements[calculationId]) {
            console.log("CALCULATION ELEMENT");
            console.log(element);
            console.log("args");
            console.log(args);
            let prefix = icalc_calculationElementConfigurations[calculationId]['sum-prefix']
            let postfix = icalc_calculationElementConfigurations[calculationId]['sum-postfix']
            element.value = prefix + icalc_calculate(calculationId).toString() + postfix;
        }
    }
});


function icalc_getProductById(id) {
    const xhr = new XMLHttpRequest();
    const url = `/wp-json/icalc/v1/products/${id}`;
    xhr.open('GET', url);
    xhr.setRequestHeader('Content-Type', 'application/json');
    return xhr;
}


function icalc_getServiceById(id) {
    const xhr = new XMLHttpRequest();
    const url = `/wp-json/icalc/v1/services/${id}`;
    xhr.open('GET', url);
    xhr.setRequestHeader('Content-Type', 'application/json');
    return xhr;
}


function icalc_getCalculationDescriptionById(id) {
    const xhr = new XMLHttpRequest();
    const url = `/wp-json/icalc/v1/icalculation-descriptions/${id}`;
    xhr.open('GET', url);
    xhr.setRequestHeader('Content-Type', 'application/json');
    return xhr;
}


function icalc_displayComponent(component, calculationId) {
    switch (component.type) {
        case "product":
            return icalc_displayProduct(component, calculationId);
        case "service":
            return icalc_displayService(component, calculationId);
        case "genericComponent":
            return icalc_displayGenericComponent(component, calculationId);

        default:
            return document.createElement("div");
    }

}

function icalc_displayProduct(product, calculationId) {
    const productDiv = document.createElement("div");
    productDiv.classList.add("form-group");
    productDiv.classList.add("row");

    let productXHR = icalc_getProductById(product.id);

    productXHR.onreadystatechange = function () {
        if (productXHR.readyState === XMLHttpRequest.DONE) {
            if (productXHR.status === 200) {
                let productData = JSON.parse(productXHR.responseText);

                productDiv.appendChild(icalc_getDisplayType(product, productData, calculationId));

            } else {
                console.log('Error fetching prodcut data');
            }
        }
    };
    productXHR.send();

    return productDiv;
}

function icalc_displayService(service, calculationId) {
    const serviceDiv = document.createElement("div");
    serviceDiv.classList.add("form-group");
    serviceDiv.classList.add("row");

    let serviceXHR = icalc_getServiceById(service.id);

    serviceXHR.onreadystatechange = function () {
        if (serviceXHR.readyState === XMLHttpRequest.DONE) {
            if (serviceXHR.status === 200) {
                let productData = JSON.parse(serviceXHR.responseText);

                serviceDiv.appendChild(icalc_getDisplayType(service, productData, calculationId));

            } else {
                console.log('Error fetching prodcut data');
            }
        }
    };
    serviceXHR.send();

    return serviceDiv;
}

function icalc_displayGenericComponent(genericComponent, calculationId) {
    return icalc_getDisplayType(genericComponent, {}, calculationId);

}


function icalc_createCustomStyle(customStyles) {
    if (customStyles.trim().length !== 0) {
        const styles = document.createElement("style");
        styles.innerText = customStyles;
        return styles;
    }
    return false;
}


function icalc_getDisplayType(component, componentData, calculationId) {
    switch (component.displayType.toLowerCase()) {
        case "number":
        case "number input":
            return icalc_getNumberDisplayType(component, componentData, calculationId);

        case "slider":
        case "range":
            return icalc_getSliderDisplayType(component, componentData, calculationId);

        case "label":
            return icalc_getLabelDisplayType(component, componentData, calculationId);

        case "horizontal rule":
        case "hr":
            return icalc_getHorizontalRule(component, componentData, calculationId);

        case "sum":
            return icalc_getSumDisplayType(component, calculationId);

        case "list":
            return icalc_getListDisplayType(component, componentData, calculationId);


    }
}

function icalc_getSumDisplayType(component, calculationId) {
    const wrapper = document.createElement("div");
    const colLabel = document.createElement("div");
    colLabel.classList.add("col");
    colLabel.classList.add("form-label");
    const label = document.createElement("label");
    label.innerText = "Result:"
    label.setAttribute('for', `${component.type}-${component.id}-numberInput`);
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
    inputElement.disabled = true
    inputElement.classList.add("form-control");
    if (component.conf.configuration["input-class"]) {
        let inputClasses = component.conf.configuration["input-class"].split(";");
        for (const inputClass of inputClasses) {
            inputElement.classList.add(inputClass);
        }
    }

    icalc_calculationElementConfigurations[calculationId] = component.conf.configuration;

    if (!icalc_calculationElements[calculationId]) {
        icalc_calculationElements[calculationId] = [];
    }
    icalc_calculationElements[calculationId].push(inputElement);

    colResult.appendChild(inputElement);
    wrapper.appendChild(colResult);
    return wrapper;
}


function icalc_calculate(idOfCalculation, method = "sum") {
    let result = 0;
    const calculationObject = icalc_calculations.get(idOfCalculation);
    for (const calcPart in calculationObject) {
        switch (method) {
            case "sum":
                result += icalc_simpleCalculation(calculationObject[calcPart]);
                break;
        }
    }
    return result;
}

/**
 *
 * @param calculationPart {"baseValue":15.4, "times": 5, "negative": false}
 */
function icalc_simpleCalculation(calculationPart) {
    let preCalc = calculationPart["baseValue"] * calculationPart["times"];
    return calculationPart["negative"] ? preCalc * -1 : preCalc;
}


function icalc_getNumberDisplayType(component, componentData, calculationId) {
    const showLabel = component.conf.configuration["show-label"];
    const wrapper = document.createElement("div");
    if (showLabel === "true") {
        const colLabel = document.createElement("div");
        colLabel.classList.add("col");
        colLabel.classList.add("form-label");
        const label = document.createElement("label");
        label.innerText = componentData.name
        label.setAttribute('for', `${component.domId}-numberInput`);
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

    inputElement.setAttribute('id', `${component.domId}-numberInput`);
    inputElement.setAttribute('name', `${component.domId}-numberInput`);
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
            "baseValue": Number(baseValue),
            "times": Number(inputElement.value),
            "negative": false
        }
        icalc_calculations.setFrom(calculationId, `${component.domId}-numberInput`, inputCalculation);
    }

    colInput.appendChild(inputElement);
    wrapper.appendChild(colInput);
    return wrapper;
}


function icalc_getSliderDisplayType(component, componentData, calculationId) {
    const showLabel = component.conf.configuration["show-label"];
    const wrapper = document.createElement("div");
    if (showLabel === "true") {
        const colLabel = document.createElement("div");
        colLabel.classList.add("col");
        colLabel.classList.add("form-label");
        const label = document.createElement("label");
        label.innerText = componentData.name
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
    if (component.conf.configuration["input-class"]) {
        let inputClasses = component.conf.configuration["input-class"].split(";");
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
    displayValue.classList.add("icalc-display-slider-value");

    colInput.onchange = () => {
        const inputCalculation = {
            "baseValue": Number(componentData["price"]),
            "times": Number(inputElement.value),
            "negative": false
        }

        if (component.conf.configuration["slider-show-value"]) {
            displayValue.textContent = inputElement.value + " " + componentData["unit"];
        } else {
            displayValue.innerHTML = ""
        }

        icalc_calculations.setFrom(calculationId, `${component.domId}-slider`, inputCalculation);
    }

    colInput.appendChild(inputElement);
    colInput.appendChild(displayValue);
    wrapper.appendChild(colInput);
    return wrapper;
}


function icalc_getLabelDisplayType(component) {
    const label = document.createElement("label")
    label.textContent = component.conf.configuration["custom-label"];
    return label;
}

function icalc_getHorizontalRule() {
    return document.createElement("hr");
}


function icalc_getListDisplayType(component, componentData, calculationId) {
    const showLabel = component.conf.configuration["show-label"];
    const wrapper = document.createElement("div");
    if (showLabel === "true") {
        const colLabel = document.createElement("div");
        colLabel.classList.add("col");
        colLabel.classList.add("form-label");
        const label = document.createElement("label");
        label.innerText = componentData.name
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

    if (component.conf.configuration["input-class"]) {
        let inputClasses = component.conf.configuration["input-class"].split(";");
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

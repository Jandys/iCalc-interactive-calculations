const draggableComponents = document.querySelectorAll('.icalc-draggable');
const dashboard = document.getElementById('icalc-dashboard');
const dashboardEdit = document.getElementById('icalc-dashboard-edit');

dashboard.onchange = () => {
    dashboard_content_change();
}
dashboardEdit.onchange = () => {
    dashboard_edit_content_change();
}


const body = document.querySelector('body');
let draggedDashboardItem = null;
draggableComponents.forEach(component => {
    component.addEventListener('dragstart', e => {
        e.dataTransfer.setData('text/plain', e.target.id);
    });
});


dashboard.addEventListener('dragstart', e => {
    draggedDashboardItem = e.target.closest('.icalc-dashboard-item');
});
dashboardEdit.addEventListener('dragstart', e => {
    draggedDashboardItem = e.target.closest('.icalc-dashboard-item');
});


dashboard.addEventListener('dragover', e => {
    e.preventDefault();
});
dashboardEdit.addEventListener('dragover', e => {
    e.preventDefault();
});

function addMovableButtons(cloneComponent, dashboardItem) {
    let configurationBar = cloneComponent.querySelector(".icalc-configuration-bar");

    const moveUpButton = document.createElement('button');
    moveUpButton.innerHTML = '<i class="dashicons dashicons-arrow-up-alt"></i>';
    moveUpButton.classList.add("icalc-config-btn");
    moveUpButton.classList.add("icalc-btn-gray");
    moveUpButton.classList.add("button");
    moveUpButton.addEventListener('click', () => moveComponent(dashboardItem, 'up'));
    const moveDownButton = document.createElement('button');
    moveDownButton.innerHTML = '<i class="dashicons dashicons-arrow-down-alt"></i>';
    moveDownButton.classList.add("icalc-config-btn");
    moveDownButton.classList.add("icalc-btn-gray");
    moveDownButton.classList.add("button");
    moveDownButton.addEventListener('click', () => moveComponent(dashboardItem, 'down'));

    configurationBar.appendChild(moveDownButton);
    configurationBar.appendChild(moveUpButton);
}

dashboard.addEventListener('drop', e => {
    e.preventDefault();
    const id = e.dataTransfer.getData('text/plain');
    const draggedComponent = document.getElementById(id);
    const viableComponentId = draggedComponent.getAttribute('data-component');
    const viableComponent = document.getElementById(viableComponentId);

    const cloneComponent = viableComponent.cloneNode(true);
    cloneComponent.classList.remove("hidden");
    let nextId = Number(viableComponent.getAttribute("data-next-id"));
    cloneComponent.id = cloneComponent.id + nextId;
    cloneComponent.classList.add("icalc-configurable-draggable-option");

    setNewClonedComponent(cloneComponent);

    nextId++;
    viableComponent.setAttribute("data-next-id", nextId);

    const dashboardItem = document.createElement('div');
    dashboardItem.classList.add('icalc-dashboard-item');
    dashboardItem.appendChild(cloneComponent);

    addMovableButtons(cloneComponent, dashboardItem);

    dashboard.appendChild(dashboardItem);

    if (viableComponent) {
        dashboard_content_change();
    }
});


dashboardEdit.addEventListener('drop', e => {
    e.preventDefault();
    const id = e.dataTransfer.getData('text/plain');
    const draggedComponent = document.getElementById(id);
    const viableComponentId = draggedComponent.getAttribute('data-component');
    const viableComponent = document.getElementById(viableComponentId);

    const cloneComponent = viableComponent.cloneNode(true);
    cloneComponent.classList.remove("hidden");
    let nextId = Number(viableComponent.getAttribute("data-next-id"));
    cloneComponent.id = cloneComponent.id + nextId;
    cloneComponent.classList.add("icalc-configurable-draggable-option");

    setNewClonedComponent(cloneComponent);

    nextId++;
    viableComponent.setAttribute("data-next-id", nextId);

    const dashboardItem = document.createElement('div');
    dashboardItem.classList.add('icalc-dashboard-item');
    dashboardItem.appendChild(cloneComponent);

    addMovableButtons(cloneComponent, dashboardItem);

    dashboardEdit.appendChild(dashboardItem);

    if (viableComponent) {
        dashboard_edit_content_change();
    }
});

body.addEventListener('dragover', e => {
    e.preventDefault();
});

body.addEventListener('drop', e => {
    e.preventDefault();
    if (draggedDashboardItem && !dashboard.contains(e.target) && !draggedDashboardItem.parentElement.id.includes("edit")) {
        dashboard.removeChild(draggedDashboardItem);
        draggedDashboardItem = null;
        dashboard_content_change();
    }
    if (draggedDashboardItem && !dashboardEdit.contains(e.target) && draggedDashboardItem.parentElement.id.includes("edit")) {
        dashboardEdit.removeChild(draggedDashboardItem);
        draggedDashboardItem = null;
        dashboard_edit_content_change();
    }
});

function moveComponent(dashboardItem, direction) {
    if (direction === 'up') {
        if (dashboardItem.previousElementSibling) {
            if (dashboardItem.parentElement.id.includes("edit")) {
                dashboardEdit.insertBefore(dashboardItem, dashboardItem.previousElementSibling);
                dashboard_edit_content_change();
            } else {
                dashboard.insertBefore(dashboardItem, dashboardItem.previousElementSibling);
                dashboard_content_change();
            }
        }
    } else if (direction === 'down') {
        if (dashboardItem.nextElementSibling) {
            if (dashboardItem.parentElement.id.includes("edit")) {
                dashboardEdit.insertBefore(dashboardItem.nextElementSibling, dashboardItem);
                dashboard_edit_content_change();
            } else {
                dashboard.insertBefore(dashboardItem.nextElementSibling, dashboardItem);
                dashboard_content_change();
            }

        }
    }
}

// loaders
window.addEventListener('load', () => {
    const dashboardProducts = document.getElementById('icalc-dashboard-products');

    const productSelectList = document.createElement('select');
    productSelectList.type = "text";
    productSelectList.id = "productSelect";
    productSelectList.name = "products";

    const productDiv = document.createElement('div');
    productDiv.id = 'productDiv';
    productDiv.classList.add("icalc-product-div");

    let productsXHR = icalc_getAllProducts();
    productsXHR.onreadystatechange = function () {
        if (productsXHR.readyState === XMLHttpRequest.DONE) {
            if (productsXHR.status === 200) {
                let products = JSON.parse(productsXHR.responseText);

                if (products.length > 0) {
                    let noneSelected = document.createElement('option');
                    noneSelected.value = "";
                    noneSelected.innerText = "-- None --";
                    productSelectList.appendChild(noneSelected);
                }

                products.forEach(product => {
                    let htmlOptionElement = document.createElement('option');
                    htmlOptionElement.value = product.id;
                    htmlOptionElement.innerText = product.name;
                    productSelectList.appendChild(htmlOptionElement);


                    const span = document.createElement('span');
                    span.innerHTML = getProductInHtml(product);
                    span.id = 'product' + product.id + '-'
                    span.classList.add("icalc-selected-span-item");
                    span.classList.add("hidden");
                    productDiv.appendChild(span);
                });

            } else {
                console.log('Error fetching products data:', productsXHR.status);
            }
        }
    };
    productsXHR.send();

    dashboardProducts.appendChild(productSelectList);
    dashboardProducts.appendChild(productDiv);
})

window.addEventListener('load', () => {
    const dashboardServices = document.getElementById('icalc-dashboard-services');

    const serviceSelectList = document.createElement('select');
    serviceSelectList.type = "text";
    serviceSelectList.id = "serviceSelect";
    serviceSelectList.name = "services";

    const serviceDiv = document.createElement('div');
    serviceDiv.id = 'serviceDiv';
    serviceDiv.classList.add("icalc-service-div");

    let servicesXHR = icalc_getAllServices();
    servicesXHR.onreadystatechange = function () {
        if (servicesXHR.readyState === XMLHttpRequest.DONE) {
            if (servicesXHR.status === 200) {
                let services = JSON.parse(servicesXHR.responseText);

                if (services.length > 0) {
                    let noneSelected = document.createElement('option');
                    noneSelected.value = "";
                    noneSelected.innerText = "-- None --";
                    serviceSelectList.appendChild(noneSelected);
                }

                services.forEach(service => {
                    let htmlOptionElement = document.createElement('option');
                    htmlOptionElement.value = service.id;
                    htmlOptionElement.innerText = service.name;
                    serviceSelectList.appendChild(htmlOptionElement);


                    const span = document.createElement('span');
                    span.innerHTML = getProductInHtml(service);
                    span.id = 'service' + service.id + '-'
                    span.classList.add("icalc-selected-span-item");
                    span.classList.add("hidden");
                    serviceDiv.appendChild(span);
                });

            } else {
                console.log('Error fetched service data:', servicesXHR.status);
            }
        }
    };
    servicesXHR.send();

    dashboardServices.appendChild(serviceSelectList);
    dashboardServices.appendChild(serviceDiv)
})

const genericTypes = new Map(
    [[0, "-- None --"],
        [1, "Label"],
        [2, "Text"],
        [3, "List"],
        [4, "Number input"],
        [5, "Slider"],
        [5, "Checkbox"],
        [7, "Spacer"],
        [8, "Horizontal Rule"]
    ]);


const calculationTypes = new Map(
    [[0, "-- None --"],
        [1, "Sum"],
        [2, "Product Calculation"],
        [3, "Subtract Calculation"],
        [4, "Complex Calculation"]
    ]);

function genericTypesGetKeyForValue(lookupKey){
    let returnValue = 0;
    genericTypes.forEach(
        (key, value) => {
            if (key.toLowerCase().includes(lookupKey.toLowerCase())){
                returnValue = value;
            }
        }
    )
    return returnValue;
}


function calculationTypesGetKeyForValue(lookupKey){
    let returnValue = 0;
    calculationTypes.forEach(
        (key, value) => {
            if (key.toLowerCase().includes(lookupKey.toLowerCase())){
                returnValue = value;
            }
        }
    )
    return returnValue;
}


window.addEventListener('load', () => {
    const dashboardComponents = document.getElementById('icalc-dashboard-components');

    const componentSelectList = document.createElement('select');
    componentSelectList.type = "text";
    componentSelectList.id = "componentSelect";
    componentSelectList.name = "components";

    const componentDiv = document.createElement('div');
    componentDiv.id = 'componentDiv';
    componentDiv.classList.add("icalc-component-div");

    for (const genericType of genericTypes) {
        const option = document.createElement('option');
        option.value = genericType[0].toString();
        option.innerText = genericType[1];

        componentSelectList.appendChild(option);
    }


    dashboardComponents.appendChild(componentSelectList);
    dashboardComponents.appendChild(componentDiv)
})



window.addEventListener('load', () => {
    const dashboardCalculations = document.getElementById('icalc-dashboard-calculations');

    const calculationSelectList = document.createElement('select');
    calculationSelectList.type = "text";
    calculationSelectList.id = "calculationSelect";
    calculationSelectList.name = "calculations";

    const calculationDiv = document.createElement('div');
    calculationDiv.id = 'calculationDiv';
    calculationDiv.classList.add("icalc-calculation-div");

    for (const genericType of calculationTypes) {
        const option = document.createElement('option');
        option.value = genericType[0].toString();
        option.innerText = genericType[1];

        calculationSelectList.appendChild(option);
    }


    dashboardCalculations.appendChild(calculationSelectList);
    dashboardCalculations.appendChild(calculationDiv)
})



function setNewClonedComponent(component) {
    let id = component.id;

    switch (true) {
        case id.startsWith("product-component"):
            return setNewProductComponent(component);
        case id.startsWith("service-component"):
            return setNewServiceComponent(component);
        case id.startsWith("component-component"):
            return setNewGenericComponent(component);
        case id.startsWith("calculation-component"):
            return setNewCalculationComponent(component);

    }
}

function setNewServiceComponent(serviceComponent) {
    const id = getSuffixIdFromElement(serviceComponent);
    let dashboard;
    let select;
    let serviceDiv;
    for (const child of serviceComponent.children) {
        modifyChildIdsWithSuffix(child, id);
        if (child.id.startsWith("icalc-dashboard-services")) {
            dashboard = child;
        }
    }

    for (const child of dashboard.children) {
        modifyChildIdsWithSuffix(child, id);
        if (child.id.startsWith("serviceSelect")) {
            select = child;
        }
        if (child.id.startsWith("serviceDiv")) {
            serviceDiv = child;
        }
    }

    for (const child of serviceDiv.children) {
        modifyChildIdsWithSuffix(child, id);
    }


    select.onchange = (event) => {
        for (const product of serviceDiv.children) {
            product.classList.add("hidden");
        }

        selectedElement = document.getElementById('service' + event.target.value + '-' + id);
        if (selectedElement) {
            selectedElement.classList.remove("hidden");
        }


        const modalConfigure = document.getElementById('modal-conf-' + serviceComponent.id);
        const headers = Array.from(selectedElement.querySelector("thead").querySelectorAll("th")).map(th => th.innerText);
        const row = selectedElement.querySelector("tbody tr");
        const values = Array.from(row.querySelectorAll("td")).map(td => td.innerText);
        const keyValuePairs = {};
        headers.forEach((header, index) => {
            keyValuePairs[header] = values[index];
        });

        modalConfigure.dataset.type = keyValuePairs["Display Type"];

    };

    if (select.children.length === 0) {
        dashboard.removeChild(select);
        dashboard.appendChild(noComponentFoundError("No Service found"));
    } else {
        appendConfigButton(dashboard);
    }
    return serviceComponent;
}

function setNewCalculationComponent(calculationComponent){
    const id = getSuffixIdFromElement(calculationComponent);
    let dashboard;
    let select;
    let calculationDiv;

    for (const child of calculationComponent.children) {
        modifyChildIdsWithSuffix(child, id);
        if (child.id.startsWith("icalc-dashboard-calculations")) {
            dashboard = child;
        }
    }

    for (const child of dashboard.children) {
        modifyChildIdsWithSuffix(child, id);
        if (child.id.startsWith("calculationSelect")) {
            select = child;
        }
        if (child.id.startsWith("calculationDiv")) {
            calculationDiv = child;
        }
    }

    for (const child of calculationDiv.children) {
        modifyChildIdsWithSuffix(child, id);
    }


    select.onchange = (event) => {
        select.dataset.selected = event.target.value;

        const modalConfigure = document.getElementById('modal-conf-' + calculationComponent.id);
        modalConfigure.dataset.type = calculationTypes.get(Number(event.target.value)).trim().toLowerCase();
    }

    select.dataset.selected = 0;

    if (select.children.length === 1) {
        dashboard.removeChild(select);
        dashboard.appendChild(noComponentFoundError("No Calculation Component found"));
    } else {
        appendConfigButton(dashboard);
    }
    return calculationComponent;
}

function setNewGenericComponent(genericComponent) {
    const id = getSuffixIdFromElement(genericComponent);
    let dashboard;
    let select;
    let componentDiv;

    for (const child of genericComponent.children) {
        modifyChildIdsWithSuffix(child, id);
        if (child.id.startsWith("icalc-dashboard-components")) {
            dashboard = child;
        }
    }

    for (const child of dashboard.children) {
        modifyChildIdsWithSuffix(child, id);
        if (child.id.startsWith("componentSelect")) {
            select = child;
        }
        if (child.id.startsWith("componentDiv")) {
            componentDiv = child;
        }
    }

    for (const child of componentDiv.children) {
        modifyChildIdsWithSuffix(child, id);
    }


    select.onchange = (event) => {
        select.dataset.selected = event.target.value

        const modalConfigure = document.getElementById('modal-conf-' + genericComponent.id);
        modalConfigure.dataset.type = genericTypes.get(Number(event.target.value)).trim().toLowerCase();
    }

    select.dataset.selected = 0;

    if (select.children.length === 1) {
        dashboard.removeChild(select);
        dashboard.appendChild(noComponentFoundError("No Generic Component found"));
    } else {
        appendConfigButton(dashboard);
    }
    return genericComponent;
}

function setNewProductComponent(productComponent) {
    const id = getSuffixIdFromElement(productComponent);
    let dashboard;
    let select;
    let productDiv;
    for (const child of productComponent.children) {
        modifyChildIdsWithSuffix(child, id);
        if (child.id.startsWith("icalc-dashboard-products")) {
            dashboard = child;
        }
    }

    for (const child of dashboard.children) {
        modifyChildIdsWithSuffix(child, id);
        if (child.id.startsWith("productSelect")) {
            select = child;
        }
        if (child.id.startsWith("productDiv")) {
            productDiv = child;
        }
    }

    for (const child of productDiv.children) {
        modifyChildIdsWithSuffix(child, id);
    }


    select.onchange = (event) => {
        for (const product of productDiv.children) {
            product.classList.add("hidden");
        }

        selectedElement = document.getElementById('product' + event.target.value + '-' + id);
        if (selectedElement) {
            selectedElement.classList.remove("hidden");
        }

        const modalConfigure = document.getElementById('modal-conf-' + productComponent.id);
        const headers = Array.from(selectedElement.querySelector("thead").querySelectorAll("th")).map(th => th.innerText);
        const row = selectedElement.querySelector("tbody tr");
        const values = Array.from(row.querySelectorAll("td")).map(td => td.innerText);
        const keyValuePairs = {};
        headers.forEach((header, index) => {
            keyValuePairs[header] = values[index];
        });

        modalConfigure.dataset.type = keyValuePairs["Display Type"];
    };

    if (select.children.length === 0) {
        dashboard.removeChild(select);
        dashboard.appendChild(noComponentFoundError("No Product found"));
    } else {
        appendConfigButton(dashboard);
    }
    return productComponent;
}

function getSuffixIdFromElement(elementId) {
    return parseInt(elementId.id.match(/\d+/)[0], 10);
}

function modifyChildIdsWithSuffix(children, suffix) {
    if (children.id !== "") {
        children.id = children.id + suffix;
    }
}

let prod1 = null;
let editingCalculation = -1;

function editUpdateObjectOfNewCalculation() {
    let children = dashboardEdit.children;

    let calculationTitleObject = document.getElementById('icalc-calulation-edit-name');
    let calculationTitle = calculationTitleObject.value ? calculationTitleObject.value : "New Calculation title";

    let updateObject = {}
    updateObject.title = calculationTitle;
    updateObject.components = [];
    updateObject.customStyles = "";
    updateObject.configuration = currentCalculationConfiguration;
    updateObject.id = editingCalculation;

    for (const child of children) {
        const component = getComponentToJSONObject(child.children[0]);
        if (component != null) {
            updateObject.components.push(component);
            updateObject.customStyles = appendStyles(updateObject.customStyles, component);
        }
    }

    return updateObject;
}

async function createUpdateObjectOfNewCalculation() {
    let children = dashboard.children;

    let calculationTitleObject = document.getElementById('icalc-calulation-new-name');
    let calculationTitle = calculationTitleObject.value ? calculationTitleObject.value : "New Calculation title";

    let updateObject = {}
    updateObject.title = calculationTitle;
    updateObject.components = [];
    updateObject.customStyles = "";
    updateObject.configuration = currentCalculationConfiguration;
    await icalc_getNextCalculationDescriptionId().then((value) => {
        updateObject.id = value;
    }, (error) => {
        console.log(error);
    });

    for (const child of children) {
        const component = getComponentToJSONObject(child.children[0]);
        if (component != null) {
            updateObject.components.push(component);
            updateObject.customStyles = appendStyles(updateObject.customStyles, component);
        }
    }

    return updateObject;
}


function getProductInHtml(product) {
    if (prod1 == null) {
        prod1 = product;
    }
    return "<table class='table table-bordered table-striped col-5 m-1'>" +
        "<thead>" +
        "<tr>" +
        "<th>ID</th>" +
        "<th>Name</th>" +
        "<th>Description</th>" +
        "<th>Price per Unit</th>" +
        "<th>Unit</th>" +
        "<th>Tag</th>" +
        "<th>Min Quantity</th>" +
        "<th>Display Type</th>" +
        "</tr>" +
        "</thead>" +
        "<tbody>" +
        "<tr>" +
        "<td>" + product["id"] + "</td>" +
        "<td>" + product["name"] + "</td>" +
        "<td>" + product["description"] + "</td>" +
        "<td>" + product["price"] + "</td>" +
        "<td>" + product["unit"] + "</td>" +
        "<td>" + product["tag"] + "</td>" +
        "<td>" + product["min_quantity"] + "</td>" +
        "<td>" + product["display_type"] + "</td>" +
        "</tr>" +
        "</tbody>" +
        "</table>";
}

async function dashboard_content_change() {
    try {
        let updateObjectJson = await createUpdateObjectOfNewCalculation();
        updatePreview(updateObjectJson);
    } catch (error) {
        console.error(error);
    }
}

async function dashboard_edit_content_change() {
    try {
        let updateObjectJson = editUpdateObjectOfNewCalculation();
        updateEditPreview(updateObjectJson);
    } catch (error) {
        console.error(error);
    }
}

function getComponentToJSONObject(component) {
    const id = component.id
    switch (true) {
        case id.startsWith("product-component"):
            return getProductToJSONObject(component);
        case id.startsWith("service-component"):
            return getServiceToJSONObject(component);
        case id.startsWith("component-component"):
            return getGenericComponentToJSONObject(component);
        case id.startsWith("calculation-component"):
            return getCalculationComponentToJSONObject(component);
    }
}

function getProductToJSONObject(productComponent) {
    const children = productComponent.children;
    let dashboard;
    let productDiv;
    let validProduct;
    for (const child of children) {
        if (child.id.startsWith("icalc-dashboard")) {
            dashboard = child;
        }
    }

    for (const dashItem of dashboard.children) {
        if (dashItem.id.startsWith("productDiv")) {
            productDiv = dashItem;
        }
    }

    if (!productDiv) {
        return;
    }

    for (const item of productDiv.children) {
        if (!item.classList.contains("hidden")) {
            validProduct = item;
        }
    }

    if (validProduct !== undefined) {
        const modalConfId = "modal-conf-" + validProduct.parentNode.parentNode.parentNode.id
        let conf = {};

        let modalConf = document.getElementById(modalConfId);
        let elementNodeListOf = modalConf.querySelectorAll('.icalc-custom-input');
        for (const customInput of elementNodeListOf) {
            conf[customInput.name] = customInput.dataset.previous;
        }

        const headers = Array.from(validProduct.querySelector("thead").querySelectorAll("th")).map(th => th.innerText);
        const row = validProduct.querySelector("tbody tr");
        const values = Array.from(row.querySelectorAll("td")).map(td => td.innerText);
        const keyValuePairs = {};
        headers.forEach((header, index) => {
            keyValuePairs[header] = values[index];
        });

        return {
            "domId": validProduct.id,
            "parentComponent": productComponent.id,
            "type": "product",
            "id": parseInt(validProduct.id.split("-")[0].match(/\d+/)[0], 10),
            "conf": {
                "confId": modalConfId,
                "configuration": conf
            },
            "displayType": keyValuePairs['Display Type']
        }
    }

}


function getServiceToJSONObject(serviceComponent) {
    const children = serviceComponent.children;
    let dashboard;
    let serviceDiv;
    let validService;
    for (const child of children) {
        if (child.id.startsWith("icalc-dashboard")) {
            dashboard = child;
        }
    }

    for (const dashItem of dashboard.children) {
        if (dashItem.id.startsWith("serviceDiv")) {
            serviceDiv = dashItem;
        }
    }

    if (!serviceDiv) {
        return;
    }

    for (const item of serviceDiv.children) {
        if (!item.classList.contains("hidden")) {
            validService = item;
        }
    }

    if (validService !== undefined) {
        const modalConfId = "modal-conf-" + validService.parentNode.parentNode.parentNode.id;
        let conf = {};

        let modalConf = document.getElementById(modalConfId);
        let elementNodeListOf = modalConf.querySelectorAll('.icalc-custom-input');
        for (const customInput of elementNodeListOf) {
            conf[customInput.name] = customInput.dataset.previous;
        }

        const headers = Array.from(validService.querySelector("thead").querySelectorAll("th")).map(th => th.innerText);
        const row = validService.querySelector("tbody tr");
        const values = Array.from(row.querySelectorAll("td")).map(td => td.innerText);
        const keyValuePairs = {};
        headers.forEach((header, index) => {
            keyValuePairs[header] = values[index];
        });


        return {
            "domId": validService.id,
            "parentComponent": serviceComponent.id,
            "type": "service",
            "id": parseInt(validService.id.split("-")[0].match(/\d+/)[0], 10),
            "conf": {
                "confId": modalConfId,
                "configuration": conf
            },
            "displayType": keyValuePairs['Display Type']
        }
    }
}

function getGenericComponentToJSONObject(genericComponent) {
    const children = genericComponent.children;
    let dashboard;
    let option;
    let select;
    for (const child of children) {
        if (child.id.startsWith("icalc-dashboard")) {
            dashboard = child;
        }
    }

    for (const dashItem of dashboard.children) {
        if (dashItem.id.startsWith("componentSelect")) {
            select = dashItem;
        }
    }


    const modalConfId = "modal-conf-" + genericComponent.id
    let conf = {};

    let modalConf = document.getElementById(modalConfId);
    let elementNodeListOf = modalConf.querySelectorAll('.icalc-custom-input');
    for (const customInput of elementNodeListOf) {
        conf[customInput.name] = customInput.dataset.previous;
    }


    return {
        "domId": genericComponent.id,
        "parentComponent": genericComponent.id,
        "type": "genericComponent",
        "id": select.dataset.selected,
        "conf": {
            "confId": modalConfId,
            "configuration": conf
        },
        "displayType": genericTypes.get(Number(select.dataset.selected)),
    };
}
function getCalculationComponentToJSONObject(calculationComponent) {
    const children = calculationComponent.children;
    let dashboard;
    let option;
    let select;
    for (const child of children) {
        if (child.id.startsWith("icalc-dashboard")) {
            dashboard = child;
        }
    }

    for (const dashItem of dashboard.children) {
        if (dashItem.id.startsWith("calculationSelect")) {
            select = dashItem;
        }
    }


    const modalConfId = "modal-conf-" + calculationComponent.id
    let conf = {};

    let modalConf = document.getElementById(modalConfId);
    let elementNodeListOf = modalConf.querySelectorAll('.icalc-custom-input');
    for (const customInput of elementNodeListOf) {
        conf[customInput.name] = customInput.dataset.previous;
    }


    return {
        "domId": calculationComponent.id,
        "parentComponent": calculationComponent.id,
        "type": "calculationComponent",
        "id": select.dataset.selected,
        "conf": {
            "confId": modalConfId,
            "configuration": conf
        },
        "displayType": calculationTypes.get(Number(select.dataset.selected)),
    };
}

function getConfigureModal(id, displayType) {

    return `
    <div class="icalc-modal-wrapper hidden icalc-component-configuration-modal">
        <div id="modal-${id}" class="icalc-config-modal">
        <div class="modal-content p-3">
        <span>
          <h2>Personal Customization</h2>
           <button class="icalc-config-btn btn-danger mt-2 close-btn icalc-float-right"><i class="dashicons dashicons-no"></i></button>
        </span>
          
        <span id="show-label-configuration" class="row m-0 align-items-center">
              <label class="col-2" for="show-label">Show label:</label>
              <input type="checkbox" id="show-label" name="show-label" class="icalc-custom-input form-check form-switch mb-2 ml-2 mr-4" data-previous=""/> 
        </span>
        
        <span class="display-none row m-0 align-items-center" id="custom-label-configuration">
              <label class="col-2" for="custom-label">Custom label:</label>
              <input type="text" id="custom-label" name="custom-label" class="icalc-custom-input form-check form-switch mb-2 ml-2 mr-4" data-previous=""/> 
        </span>
         
         <span class="display-none row m-0 align-items-center" id="label-configuration" >
               <label for="label-classes">Label classes:</label>
               <input type="text" id="label-classes" name="label-classes" class="icalc-custom-input mt-0 mb-2 ml-4 mr-4" data-previous=""/> 
        </span>
        
         <span class="display-none row m-0 align-items-center" id="base-value-configuration" >
               <label for="base-value" class="col-2">Base Value:</label>
               <input type="number" id="base-value" name="base-value" class="icalc-custom-input mt-0 mb-2 ml-4 mr-4" data-previous="" value="1"/> 
        </span>
        
        <span class="display-none row m-0 align-items-center" id="slider-configuration">
            <label class="col-2" for="slider-max">Slider max:</label>
            <input type="number" id="slider-max" name="slider-max" class="icalc-custom-input mt-0 mb-2 ml-4 mr-4" data-previous=""/>
            <label for="slider-show-value">Show Value:</label>
            <input type="checkbox" id="slider-show-value" name="slider-show-value" class="icalc-custom-input form-check form-switch mb-2 ml-2 mr-4" data-previous=""/>  
        </span>
        
        <span class="display-none" id="list-configuration" data-option="1">
            <span class="row m-0 align-items-center">
                <label class="col-2" for="list-option1">Option:</label>
                <input type="text" id="list-option1" name="list-option1" class="icalc-custom-input mt-0 mb-2 ml-4 mr-4 col-3" data-previous=""/>
                <label class="col-2" for="list-value1">Value:</label>
                <input type="text" id="list-value1" name="list-value1" class="icalc-custom-input mt-0 mb-2 ml-4 mr-4 col-3" data-previous=""/>
            </span>    
            
            <button id="list-add-option" class="icalc-config-btn btn-info mt-2 close-btn icalc-float-right"><i class="dashicons dashicons-plus-alt"></i></button>
        </span>
        
            <span class="display-none" id="sum-configuration">
            <span class="row m-0 align-items-center">
                <label class="col-2" for="list-option1">Prefix:</label>
                <input type="text" id="sum-prefix" name="sum-prefix" class="icalc-custom-input mt-0 mb-2 ml-4 mr-4 col-3" data-previous=""/>
                <label class="col-2" for="sum-postfix">Postfix:</label>
                <input type="text" id="sum-postfix" name="sum-postfix" class="icalc-custom-input mt-0 mb-2 ml-4 mr-4 col-3" data-previous=""/>
            </span>    
        </span>
         
          <span id="input-classes-configuration" class="row m-0 align-items-center">
               <label class="col-2" for="input-classes">Input classes:</label>
               <input type="text" id="input-classes" name="input-classes" class="icalc-custom-input mt-0 mb-2 ml-4 mr-4" data-previous=""/> 
          </span>
            
           
          <p class="font-italic font-weight-light text-info">To add multiple classes separate them by using semicolon: ';' </p>
                
          <span id="custom-css-configuration">
             <label class="col-2"  for="custom-css">Custom CSS:</label>
             <textarea class="icalc-custom-input icalc-custom-styler mt-0 mb-4 ml-4 mr-4"  id="custom-css" name="custom-css" rows="8" cols="50" placeholder=".myStyle{color:red}" data-previous=""></textarea>
          </span>
       
          <button class="icalc-config-btn btn-success mt-2 save-btn icalc-float-right"><i class="dashicons dashicons-saved"></i></button>
        </div>
      </div>
    </div>
  
`;

}

function icalc_getNewRowOfOptionsToConfigure(currentId) {
    const span = document.createElement('span');
    span.classList.add("row");
    const labelOption = document.createElement("label");
    const inputOption = document.createElement('input');
    const labelValue = document.createElement("label");
    const inputValue = document.createElement('input');
    const nextId = currentId + 1;

    labelOption.classList.add("col-2");
    labelOption.htmlFor = "list-option" + nextId;
    labelOption.textContent = `Option ${nextId}:`;
    span.appendChild(labelOption);
    inputOption.type = "text";
    inputOption.id = "list-option" + nextId;
    inputOption.name = "list-option" + nextId;
    inputOption.className = "icalc-custom-input mt-0 mb-2 ml-4 mr-4 col-3"
    inputOption.dataset.previous = "";
    span.appendChild(inputOption);
    labelValue.classList.add("col-2");
    labelValue.htmlFor = "list-value" + nextId;
    labelValue.textContent = `Value ${nextId}:`;
    span.appendChild(labelValue);
    inputValue.type = "text";
    inputValue.id = "list-value" + nextId;
    inputValue.name = "list-value" + nextId;
    inputValue.className = "icalc-custom-input mt-0 mb-2 ml-4 mr-4 col-3"
    inputValue.dataset.previous = "";
    span.appendChild(inputValue);
    return {span, nextId};
}

function appendConfigButton(div) {
    // Create a new button element
    const button = document.createElement('button');
    button.innerHTML = '<i class="dashicons dashicons-admin-generic"></i>';
    button.className = 'icalc-config-btn button';
    const componentType = icalc_getComponentType(div.id.split("-")[2]);

    const id = "conf-" + div.parentNode.id;

    // Append the button to the div
    let configureDiv = div.parentNode.querySelector(".icalc-configuration-bar");
    configureDiv.appendChild(button);

    // Append the modal to the body
    const modalContainer = document.createElement('div');
    modalContainer.innerHTML = getConfigureModal(id, componentType);
    document.body.appendChild(modalContainer);

    // Get the modal and close button elements
    const modal = document.getElementById('modal-' + id);
    modal.dataset.type = "generic"
    const closeBtn = modal.querySelector('.close-btn');
    const saveBtn = modal.querySelector('.save-btn');
    const showLabel = modal.querySelector('#show-label');

    const addListOptionBtn = modal.querySelector('#list-add-option');
    addListOptionBtn.onclick = () => {
        let elementNodeListOf = addListOptionBtn.previousElementSibling.querySelectorAll(".icalc-custom-input");
        for (const prevInput of elementNodeListOf) {
            if (!prevInput.value) {
                alert("Please fill previous option and value");
                return;
            }
        }
        let currentId = Number(addListOptionBtn.parentNode.dataset.option);
        const {span, nextId} = icalc_getNewRowOfOptionsToConfigure(currentId);
        addListOptionBtn.parentNode.dataset.option = nextId;

        addListOptionBtn.parentNode.insertBefore(span, addListOptionBtn);
    }


    // Function to open the modal
    function openModal() {
        modal.parentNode.classList.remove("hidden");
    }

    function saveModal() {
        modal.parentNode.classList.add("hidden");
        let customInputs = modal.querySelectorAll('.icalc-custom-input');
        for (const input of customInputs) {
            if (input.type === "checkbox") {
                input.dataset.previous = input.checked;
            } else {
                input.dataset.previous = input.value;
            }
        }
        const previewId = div.parentElement.parentElement.parentElement.id;

        if (!previewId.includes("edit")) {
            dashboard_content_change();
        } else {
            dashboard_edit_content_change();
        }

    }

    // Function to close the modal
    function closeModal() {
        modal.parentNode.classList.add("hidden");
        let customInputs = modal.querySelectorAll('.icalc-custom-input');
        for (const input of customInputs) {
            input.value = input.dataset.previous;
        }
    }

    function changeLabel() {
        let wrappingSpan = modal.querySelector("#custom-label-configuration");
        if (showLabel.checked === true) {
            wrappingSpan.classList.remove("display-none");
        } else {
            wrappingSpan.classList.add("display-none");

            let labelConfigurations = wrappingSpan.querySelectorAll('.icalc-custom-input');
            labelConfigurations.forEach((input) => {
                input.value = ""
            });
        }
    }

    function resetState(displayType, previousType) {
        console.log("resetState");
        let showLabelSpan = modal.querySelector("#show-label-configuration");
        showLabelSpan.classList.remove("display-none");

        let labelSpan = modal.querySelector("#custom-label-configuration");
        labelSpan.classList.add("display-none");

        let sliderConfSpan = modal.querySelector("#slider-configuration");
        sliderConfSpan.classList.add("display-none");


        let baseValueConfiguration = modal.querySelector("#base-value-configuration");
        baseValueConfiguration.classList.add("display-none");
        console.log("baseValueConfiguration");
        console.log(baseValueConfiguration);

        let listConfiguration = modal.querySelector("#list-configuration");
        listConfiguration.classList.add("display-none");

        let sumConfiguration = modal.querySelector("#sum-configuration");
        sumConfiguration.classList.add("display-none");


        if (previousType) {
            if (displayType !== previousType) {
                let labelInputs = labelSpan.querySelectorAll('.icalc-custom-input');
                labelInputs.forEach((input) => {
                    input.value = ""
                });

                let sliderConfigurations = sliderConfSpan.querySelectorAll('.icalc-custom-input');
                sliderConfigurations.forEach((input) => {
                    if (input.type === "checkbox") {
                        input.checked = false
                    } else {
                        input.value = ""
                    }
                });

                let baseValueInputs = baseValueConfiguration.querySelectorAll('.icalc-custom-input');
                baseValueInputs.forEach((input) => {
                    input.value = ""
                });

                let listInputs = listConfiguration.querySelectorAll('.icalc-custom-input');
                listInputs.forEach((input) => {
                    input.value = ""
                });

                let labelConfigurations = showLabelSpan.querySelectorAll('.icalc-custom-input');
                labelConfigurations.forEach((input) => {
                    input.value = ""
                });
            }
        }else {
            modal.dataset.previousType = displayType;
        }
    }

    function processDisplayTypeChanges() {
        const displayType = modal.dataset.type;


        console.log("PRocess display type change")
        console.log(displayType.toLowerCase());

        resetState(displayType,modal.dataset.previousType);



        switch (displayType.toLowerCase()) {
            case "label":
                modal.querySelector("#show-label-configuration").classList.add("display-none");
                modal.querySelector("#input-classes-configuration").classList.add("display-none");
                modal.querySelector("#custom-label-configuration").classList.remove("display-none");
                break;

            case "checkbox":
            case "number input":
                modal.querySelector("#base-value-configuration").classList.remove("display-none");
                break;

            case "hr":
            case "horizontal rule":
                modal.querySelector('#show-label-configuration').classList.add("display-none")

            case "list":
            case "choose list":
                modal.querySelector("#list-configuration").classList.remove("display-none");
                break;

            case "sum":
                modal.querySelector("#sum-configuration").classList.remove("display-none");
                break;

            case "range":
            case "slider":
                modal.querySelector("#slider-configuration").classList.remove("display-none");
                break;
        }
        switch (true){
            case modal.id.includes("component-component"):
                modal.querySelector("#custom-label-configuration").classList.remove("display-none");
                break
        }
    }


    // Event listeners
    button.addEventListener('click', () => {
        processDisplayTypeChanges();
        openModal();
    });
    closeBtn.addEventListener('click', closeModal);
    saveBtn.addEventListener('click', saveModal);
    showLabel.addEventListener('change', changeLabel);
    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });
}


function updateEditPreview(jsonBodyToUpdate) {
    masterUpdatePreview(jsonBodyToUpdate, 'icalc-preview-edit')
}

function updatePreview(jsonBodyToUpdate) {
    masterUpdatePreview(jsonBodyToUpdate, 'icalc-preview')
}

function masterUpdatePreview(jsonBodyToUpdate, previewId) {
    if (jsonBodyToUpdate === undefined) {
        return;
    }

    const preview = document.getElementById(previewId);
    preview.innerHTML = '';

    let updateObject;
    if (typeof jsonBodyToUpdate === 'string') {
        updateObject = JSON.parse(jsonBodyToUpdate);
    } else if (typeof jsonBodyToUpdate === 'object') {
        updateObject = jsonBodyToUpdate;
    }

    const wrapperDiv = document.createElement("div")
    wrapperDiv.id = 'icalc-preview-wrapper';

    if (updateObject.configuration["wrapper-classes"]) {
        let classes = updateObject.configuration["wrapper-classes"].split(";");
        for (const customClass of classes) {
            wrapperDiv.classList.add(customClass);
        }
    }


    if (updateObject.configuration["show-title"]) {
        const title = document.createElement('h3');
        title.innerText = updateObject["title"];
        wrapperDiv.appendChild(title);
    }

    const form = document.createElement("form");

    icalc_calculations.set(updateObject["id"], []);


    for (const component of updateObject["components"]) {
        console.log("component");
        console.log(component);

        if (component["displayType"].trim().replaceAll(" ", "").replaceAll("-", "").toLowerCase() === "none") {
            continue;
        }
        form.appendChild(icalc_displayComponent(component, updateObject["id"]));
    }


    let customStyles = icalc_createCustomStyle(updateObject["customStyles"])
    if (customStyles !== false) {
        preview.appendChild(customStyles);
    }

    wrapperDiv.appendChild(form);
    preview.appendChild(wrapperDiv);
}


function appendStyles(styles, component) {
    let componentCustomCss = component.conf.configuration["custom-css"];
    return styles.concat(componentCustomCss);
}

// DAHSBOARD LOGIC END /////////

document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('toggleBtn');
    const firstDiv = document.getElementById('firstDiv');
    const secondDiv = document.getElementById('secondDiv');
    const thirdDiv = document.getElementById('thirdDiv');
    const saveCalculation = document.getElementById('saveCalculation');
    const editCalculation = document.getElementById('editCalculation');
    const editConfiguration = document.getElementById('editConfiguration');

    toggleBtn.addEventListener('click', () => {
        if (thirdDiv.classList.contains('visible')) {
            firstDiv.classList.remove('icalc-hidden-slow');
            firstDiv.classList.add('visible');
            secondDiv.classList.remove('visible');
            thirdDiv.classList.remove('visible');
            secondDiv.classList.add('icalc-hidden-slow');
            thirdDiv.classList.add('icalc-hidden-slow');
            firstDiv.classList.remove("display-none")
            setTimeout(() => secondDiv.classList.add("display-none"), 300);
            setTimeout(() => thirdDiv.classList.add("display-none"), 300);
            saveCalculation.classList.add("hidden");
            editCalculation.classList.add("hidden");
            editConfiguration.classList.add("hidden");

            let toggleText = toggleBtn.getAttribute('data-toggled-text');
            let innerText = toggleBtn.innerText;
            toggleBtn.setAttribute('data-toggled-text', innerText);
            toggleBtn.innerText = toggleText;
            toggleBtn.classList.remove('icalc-reappear');
            toggleBtn.classList.add('icalc-reappear');

            //clear possible components/modals left from editing
            icalc_clear_edit_calculation_data();

            return;
        }

        icalc_clear_edit_calculation_data();

        if (firstDiv.classList.contains('visible')) {
            firstDiv.classList.remove('visible');
            firstDiv.classList.add('icalc-hidden-slow');
            thirdDiv.classList.remove('visible');
            thirdDiv.classList.add('icalc-hidden-slow');
            secondDiv.classList.remove('icalc-hidden-slow');
            secondDiv.classList.add('visible');
            secondDiv.classList.remove("display-none");
            setTimeout(() => firstDiv.classList.add("display-none"), 300);
            setTimeout(() => thirdDiv.classList.add("display-none"), 300);
            saveCalculation.classList.remove("hidden");
            editCalculation.classList.add("hidden");
            editConfiguration.classList.remove("hidden");
        } else {
            firstDiv.classList.remove('icalc-hidden-slow');
            firstDiv.classList.add('visible');
            secondDiv.classList.remove('visible');
            thirdDiv.classList.remove('visible');
            secondDiv.classList.add('icalc-hidden-slow');
            thirdDiv.classList.add('icalc-hidden-slow');
            firstDiv.classList.remove("display-none")
            setTimeout(() => secondDiv.classList.add("display-none"), 300);
            setTimeout(() => thirdDiv.classList.add("display-none"), 300);
            saveCalculation.classList.add("hidden");
            editCalculation.classList.add("hidden");
            editConfiguration.classList.add("hidden");
        }
        let toggleText = toggleBtn.getAttribute('data-toggled-text');
        let innerText = toggleBtn.innerText;
        toggleBtn.setAttribute('data-toggled-text', innerText);
        toggleBtn.innerText = toggleText;
        toggleBtn.classList.remove('icalc-reappear');
        toggleBtn.classList.add('icalc-reappear');
    });


    saveCalculation.onclick = async () => {
        let updateObjectOfNewCalculationJSON = await createUpdateObjectOfNewCalculation();

        let updateObjectOfNewCalculation;
        if (typeof updateObjectOfNewCalculationJSON === 'string') {
            updateObjectOfNewCalculation = JSON.parse(updateObjectOfNewCalculationJSON);
        } else if (typeof updateObjectOfNewCalculationJSON === 'object') {
            updateObjectOfNewCalculation = updateObjectOfNewCalculationJSON;
            updateObjectOfNewCalculationJSON = JSON.stringify(updateObjectOfNewCalculationJSON);
        }

        if (updateObjectOfNewCalculation['components'].length > 0) {
            let calcCreationXHR = icalc_process_calculation_description_creation();

            calcCreationXHR.onreadystatechange = function () {
                if (calcCreationXHR.readyState === XMLHttpRequest.DONE) {
                    if (calcCreationXHR.status === 200) {
                        window.location.reload();
                    } else {
                        console.log('Error updating data:', calcCreationXHR.status);
                    }
                }
            };

            calcCreationXHR.send(updateObjectOfNewCalculationJSON);
        } else {
            alert("There are no valid components to be saved to calculation");
        }
    }

    editCalculation.onclick = () => {
        let updateObjectOfNewCalculationJSON =  editUpdateObjectOfNewCalculation();

        let updateObjectOfNewCalculation;
        if (typeof updateObjectOfNewCalculationJSON === 'string') {
            updateObjectOfNewCalculation = JSON.parse(updateObjectOfNewCalculationJSON);
        } else if (typeof updateObjectOfNewCalculationJSON === 'object') {
            updateObjectOfNewCalculation = updateObjectOfNewCalculationJSON;
            updateObjectOfNewCalculationJSON = JSON.stringify(updateObjectOfNewCalculationJSON);
        }

        if (updateObjectOfNewCalculation['components'].length > 0) {
            let calcCreationXHR = icalc_process_calculation_description_edit();

            calcCreationXHR.onreadystatechange = function () {
                if (calcCreationXHR.readyState === XMLHttpRequest.DONE) {
                    if (calcCreationXHR.status === 200) {
                        window.location.reload();
                    } else {
                        console.log('Error updating data:', calcCreationXHR.status);
                    }
                }
            };

            let data = {
                "id": updateObjectOfNewCalculationJSON.id,
                "body": updateObjectOfNewCalculationJSON
            }

            calcCreationXHR.send(JSON.stringify(data));
        } else {
            alert("There are no valid components to be saved to calculation");
        }
    }

    editConfiguration.onclick = () => {
        const modal = document.getElementById('configure-calculation-modal').parentNode;
        modal.classList.remove('hidden');

        const closeBtn = modal.querySelector('.close-btn');
        const saveBtn = modal.querySelector('.save-btn');

        function saveModal() {
            modal.classList.add("hidden");
            let customInputs = modal.querySelectorAll('.icalc-custom-input');
            for (const input of customInputs) {
                if (input.type === "checkbox") {
                    input.dataset.previous = input.checked;
                } else {
                    input.dataset.previous = input.value;
                }
            }
            let thirdDiv = document.getElementById("thirdDiv");
            if (thirdDiv.classList.contains("visible")) {
                dashboard_edit_content_change();
            } else {
                dashboard_content_change();
            }
        }

        // Function to close the modal
        function closeModal() {
            modal.classList.add("hidden");
            let customInputs = modal.querySelectorAll('.icalc-custom-input');
            for (const input of customInputs) {
                input.value = input.dataset.previous;
            }
        }

        closeBtn.addEventListener('click', closeModal);
        saveBtn.addEventListener('click', saveModal);

        modal.onchange = () => {
            icalc_process_calculation_configuration_change(modal);
        };

        window.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });

    }
});

function noComponentFoundError(error) {
    const span = document.createElement('span');
    span.classList.add("text-danger");
    span.classList.add("text-font-weight-bold");
    span.innerText = error;
    return span;
}

function icalc_getComponentType(idPart) {
    switch (true) {
        case idPart.startsWith("product"):
            return "product";

        case idPart.startsWith("service"):
            return "service";

        case idPart.startsWith("component"):
            return "component"
    }
}

let currentCalculationConfiguration = {
    "show-title": true,
    "calculation-description": "simpleCalculation",
    "wrapper-classes": "",
}

function icalc_set_calculation_configuration_values(calculateConfiguration) {
    const modal = document.getElementById('configure-calculation-modal')
    const showTitleCheckBox = modal.querySelector('#show-title');
    if (calculateConfiguration["show-title"]) {
        showTitleCheckBox.checked = true;
        showTitleCheckBox.dataset.previous = "true";
    } else {
        showTitleCheckBox.checked = false;
        showTitleCheckBox.dataset.previous = "false";
    }


    const calcDescription = modal.querySelector('#calculation-description');
    calcDescription.value = currentCalculationConfiguration["calculation-description"]
    calcDescription.dataset.previous = currentCalculationConfiguration["calculation-description"]


    const wrapperClasses = modal.querySelector('#wrapper-classes');
    wrapperClasses.value = currentCalculationConfiguration["wrapper-classes"];
    wrapperClasses.dataset.previous = currentCalculationConfiguration["wrapper-classes"];

}

function icalc_process_calculation_configuration_change(modal) {
    const showTitleCheckBox = modal.querySelector('#show-title');
    if (showTitleCheckBox.checked) {
        currentCalculationConfiguration["show-title"] = true;
    } else {
        currentCalculationConfiguration["show-title"] = false;
    }

    const calcDescription = modal.querySelector('#calculation-description');
    if (calcDescription.value) {
        currentCalculationConfiguration["calculation-description"] = calcDescription.value;
    }

    const wrapperClasses = modal.querySelector('#wrapper-classes');
    if (wrapperClasses.value) {
        currentCalculationConfiguration["wrapper-classes"] = wrapperClasses.value;
    }
}

function icalc_handle_button_and_div_swap() {
    const toggleBtn = document.getElementById('toggleBtn');
    const firstDiv = document.getElementById('firstDiv');
    const secondDiv = document.getElementById('secondDiv');
    const thirdDiv = document.getElementById('thirdDiv');
    const saveCalculation = document.getElementById('saveCalculation');
    const editCalculation = document.getElementById('editCalculation');
    const editConfiguration = document.getElementById('editConfiguration');

    setTimeout(() => firstDiv.classList.add("display-none"), 300);
    setTimeout(() => secondDiv.classList.add("display-none"), 300);
    firstDiv.classList.add("icalc-hidden-slow");
    secondDiv.classList.add("icalc-hidden-slow");
    thirdDiv.classList.remove("display-none");
    thirdDiv.classList.remove("icalc-hidden-slow");
    thirdDiv.classList.add("visible");


    editConfiguration.classList.remove("hidden");
    saveCalculation.classList.add("hidden");
    editCalculation.classList.remove("hidden");

    let toggleText = toggleBtn.getAttribute('data-toggled-text');
    let innerText = toggleBtn.innerText;
    toggleBtn.setAttribute('data-toggled-text', innerText);
    toggleBtn.innerText = toggleText;
    toggleBtn.classList.remove('icalc-reappear');
    toggleBtn.classList.add('icalc-reappear');
}

function icalc_set_edit_calculation_name(configuredName) {
    const input = document.getElementById("icalc-calulation-edit-name");
    input.value = configuredName;
}

function icalc_add_custom_styles_edit_calculation(customStyles) {
    let thirdDiv = document.getElementById('thirdDiv');
    let styles = document.createElement('style');
    styles.innerHTML = customStyles;
    thirdDiv.appendChild(styles);
}

function icalc_fill_components_of_edit_calculation(components) {
    const nextIds = {
        "product-component": 1,
        "service-component": 1,
        "component-component": 1,
        "calculation-component": 1
    };
    for (const component of components) {


        const domId = component["parentComponent"];
        const domText = domId.replace(/\d+/g, '');
        const id = domId.match(/\d+/g)?.[0];

        if (Number(id) >= Number(nextIds[domText])) {
            nextIds[domText] = Number(id) + 1;
        }

        const draggableComponent = document.getElementById(domText);
        let clonedElement = draggableComponent.cloneNode(true);
        clonedElement.id = domId;
        let insertedComponent = setNewClonedComponent(clonedElement);

        if(domText === 'component-component'){
            let select = insertedComponent.querySelector('select');

            const selectedOption = genericTypesGetKeyForValue(component.displayType);
             select.dataset.selected= selectedOption.toString();
        }
        if(domText === 'calculation-component'){
            let select = insertedComponent.querySelector('select');

            const selectedOption = calculationTypesGetKeyForValue(component.displayType);
            select.dataset.selected= selectedOption.toString();
        }

        const dashboardItem = document.createElement('div');
        dashboardItem.classList.add('icalc-dashboard-item');
        dashboardItem.appendChild(insertedComponent);

        addMovableButtons(insertedComponent, dashboardItem);
        dashboardEdit.appendChild(dashboardItem);

        icalc_insertDataToComponent(insertedComponent, component, id);
    }

    const thirdDiv = document.getElementById('thirdDiv')
    const draggableProduct = thirdDiv.querySelector('#draggableProduct');
    draggableProduct.setAttribute("data-next-id", nextIds["product-component"]);
    const draggableService = thirdDiv.querySelector('#draggableService');
    draggableService.setAttribute("data-next-id", nextIds["service-component"]);
    const draggableComponent = thirdDiv.querySelector('#draggableComponent');
    draggableComponent.setAttribute("data-next-id", nextIds["component-component"]);
}


function icalc_insertDataToComponent(insertedComponent, jsonData) {
    insertedComponent.classList.remove("hidden");

    // const id = jsonData.parentComponent.


    const possibleItem = insertedComponent.querySelector("#" + jsonData["domId"]);
    if (possibleItem !== null) {
        possibleItem.classList.remove("hidden");
    }

    const possibleSelect = insertedComponent.querySelector("select");
    possibleSelect.value = jsonData["id"];


    icalc_insertDataToComponentModal(insertedComponent, jsonData);
}

//"conf":{"confId":"modal-conf-component-component0","configuration":{"show-label":"","custom-label":"","label-class":"","base-value":"","slider-max":"","slider-show-value":"","list-option1":"","list-value1":"","input-class":"","custom-css":""}},
function icalc_insertDataToComponentModal(insertedComponent, jsonData) {

    const modal = document.getElementById(jsonData.conf.confId);
    modal.dataset.type = jsonData.displayType.toLowerCase()
    modal.dataset.previousType = jsonData.displayType.toLowerCase()

    const configuration = jsonData.conf.configuration;
    for (const attribute in configuration) {
        const selector = "#" + attribute;
        if (jsonData.displayType.toLowerCase() === 'list') {
            if (attribute.startsWith('list-')) {
                let listConfiguration = modal.querySelector("#list-configuration");
                let possibleElement = listConfiguration.querySelector('#' + attribute);
                if (!possibleElement) {
                    const id = parseInt(attribute.match(/\d+/)[0], 10);
                    let {span, nextId} = icalc_getNewRowOfOptionsToConfigure(id);
                    let button = listConfiguration.querySelector('#list-add-option');
                    listConfiguration.dataset.option = nextId;
                    listConfiguration.insertBefore(span, button);
                }
            }
        }

        const inputValue = modal.querySelector(selector);

        if (inputValue === undefined || inputValue === null) {
            continue;
        }

        inputValue.value = configuration[attribute];
        inputValue.dataset.previous = configuration[attribute];
        if (inputValue.type === "checkbox") {
            inputValue.checked = configuration[attribute];
        }
    }
}


function icalc_process_calculation_edit_action(id) {
    const calculationXHR = icalc_getCalculationDescriptionById(id);

    calculationXHR.onreadystatechange = function () {
        if (calculationXHR.readyState === XMLHttpRequest.DONE) {
            if (calculationXHR.status === 200) {
                const databaseObject = JSON.parse(calculationXHR.responseText);
                const calculationObject = JSON.parse(databaseObject.body);

                icalc_clear_create_calculation_data();

                icalc_set_calculation_configuration_values(calculationObject.configuration);
                icalc_handle_button_and_div_swap();

                icalc_set_edit_calculation_name(calculationObject.title);
                icalc_add_custom_styles_edit_calculation(calculationObject.customStyles);

                icalc_fill_components_of_edit_calculation(calculationObject.components);

                editingCalculation=id;

                dashboard_edit_content_change();

            } else {
                console.log('Error fetching calculation data:', calculationXHR.status);
            }
        }
    };
    calculationXHR.send();
}


function icalc_clear_edit_calculation_data() {
    const dashboard = document.getElementById('icalc-dashboard-edit');
    dashboard.innerHTML = "";
    const preview = document.getElementById('icalc-preview-edit');
    preview.innerHTML = "";
    icalc_clear_configure_modals();
}


function icalc_clear_create_calculation_data() {
    const dashboard = document.getElementById('icalc-dashboard');
    dashboard.innerHTML = "";
    const preview = document.getElementById('icalc-preview');
    preview.innerHTML = "";

    icalc_clear_configure_modals();
}

function icalc_clear_configure_modals() {
    let modals = document.body.querySelectorAll(".icalc-component-configuration-modal");
    for (const modal of modals) {
        document.body.removeChild(modal.parentNode);
    }
}
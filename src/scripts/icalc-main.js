const draggableComponents = document.querySelectorAll('.icalc-draggable');
const dashboard = document.getElementById('icalc-dashboard');

dashboard.onchange = () => {
    dashboard_content_change();
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


dashboard.addEventListener('dragover', e => {
    e.preventDefault();
});

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

    setNewClonedComponent(cloneComponent);

    nextId++;
    viableComponent.setAttribute("data-next-id", nextId);

    const dashboardItem = document.createElement('div');
    dashboardItem.classList.add('icalc-dashboard-item');
    dashboardItem.appendChild(cloneComponent);

    const moveControls = document.createElement('div');
    moveControls.classList.add('icalc-move-controls');
    const moveUpButton = document.createElement('button');
    moveUpButton.innerHTML = '↑';
    moveUpButton.addEventListener('click', () => moveComponent(dashboardItem, 'up'));
    const moveDownButton = document.createElement('button');
    moveDownButton.innerHTML = '↓';
    moveDownButton.addEventListener('click', () => moveComponent(dashboardItem, 'down'));

    moveControls.appendChild(moveUpButton);
    moveControls.appendChild(moveDownButton);
    dashboardItem.appendChild(moveControls);

    dashboard.appendChild(dashboardItem);

    if(viableComponent){
        dashboard_content_change();
    }
});

body.addEventListener('dragover', e => {
    e.preventDefault();
});

body.addEventListener('drop', e => {
    e.preventDefault();
    if (draggedDashboardItem && !dashboard.contains(e.target)) {
        dashboard.removeChild(draggedDashboardItem);
        draggedDashboardItem = null;
        dashboard_content_change();
    }
});

function moveComponent(dashboardItem, direction) {
    if (direction === 'up') {
        if (dashboardItem.previousElementSibling) {
            dashboard.insertBefore(dashboardItem, dashboardItem.previousElementSibling);
            dashboard_content_change();
        }
    } else if (direction === 'down') {
        if (dashboardItem.nextElementSibling) {
            dashboard.insertBefore(dashboardItem.nextElementSibling, dashboardItem);
            dashboard_content_change();
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
                console.log('Data successfully loaded:', products);

                if(products.length>0){
                    let noneSelected = document.createElement('option');
                    noneSelected.value="";
                    noneSelected.innerText="-- None --";
                    productSelectList.appendChild(noneSelected);
                }

                products.forEach(product => {
                    let htmlOptionElement = document.createElement('option');
                    htmlOptionElement.value = product.id;
                    htmlOptionElement.innerText = product.name;
                    productSelectList.appendChild(htmlOptionElement);


                    const span = document.createElement('span');
                    console.log(product);
                    span.innerHTML = getProductInHtml(product);
                    span.id = 'product' + product.id + '-'
                    span.classList.add("icalc-selected-span-item");
                    span.classList.add("hidden");
                    productDiv.appendChild(span);
                });

            } else {
                console.log('Error updating data:', productsXHR.status);
            }
        }
    };
    productsXHR.send();

    dashboardProducts.appendChild(productSelectList);
    dashboardProducts.appendChild(productDiv)
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
                console.log('Data successfully loaded:', services);

                if(services.length>0){
                    let noneSelected = document.createElement('option');
                    noneSelected.value="";
                    noneSelected.innerText="-- None --";
                    serviceSelectList.appendChild(noneSelected);
                }

                services.forEach(service => {
                    let htmlOptionElement = document.createElement('option');
                    htmlOptionElement.value = service.id;
                    htmlOptionElement.innerText = service.name;
                    serviceSelectList.appendChild(htmlOptionElement);


                    const span = document.createElement('span');
                    console.log(service);
                    span.innerHTML = getProductInHtml(service);
                    span.id = 'service' + service.id + '-'
                    span.classList.add("icalc-selected-span-item");
                    span.classList.add("hidden");
                    serviceDiv.appendChild(span);
                });

            } else {
                console.log('Error updating data:', servicesXHR.status);
            }
        }
    };
    servicesXHR.send();


    console.log("Services List: ");
    console.log(serviceSelectList);
    console.log("List childrens: ");
    console.log(serviceSelectList.children.length);


    dashboardServices.appendChild(serviceSelectList);
    dashboardServices.appendChild(serviceDiv)
})

const genericTypes = new Map(
    [[1, "label"],
    [2, "Number input"]]);


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


function setNewClonedComponent(component) {
    console.log(component);
    let id = component.id;

    switch (true) {
        case id.startsWith("product-component"):
            setNewProductComponent(component);
            break;
        case id.startsWith("service-component"):
            setNewServiceComponent(component);
            break;
        case id.startsWith("component-component"):
            startNewGenericComponent(component);
            break

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

        console.log('service' + event.target.value + '-' + id);
        selectedElement = document.getElementById('service' + event.target.value + '-' + id);
        if(selectedElement){
            selectedElement.classList.remove("hidden");
        }
    };

    if (select.children.length === 0) {
        dashboard.removeChild(select);
        dashboard.appendChild(noComponentFoundError("No Service found"));
    }
}

function startNewGenericComponent(genericComponent) {
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
        console.log('component' + event.target.value + '-' + id);
    }

    if (select.children.length === 0) {
        dashboard.removeChild(select);
        dashboard.appendChild(noComponentFoundError("No Generic Component found"));
    }
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

        console.log('product' + event.target.value + '-' + id);
        selectedElement = document.getElementById('product' + event.target.value + '-' + id);
        if(selectedElement){
            selectedElement.classList.remove("hidden");
        }
    };

    if (select.children.length === 0) {
        dashboard.removeChild(select);
        dashboard.appendChild(noComponentFoundError("No Product found"));
    }
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
        "<th>Price</th>" +
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


function dashboard_content_change() {
    let children = dashboard.children;

    let calculationTitleObject = document.getElementById('icalc-calulation-new-name');
    let calculationTitle = calculationTitleObject.value ? calculationTitleObject.value : "New Calculation title";

    let updateObject = {}
    updateObject.title = calculationTitle;
    updateObject.components = [];

    for (const child of children) {
        console.log("Child: ");
        console.log(child);
        const component = getComponentToJSONObject(child.children[0]);
        if (component != null) {
            updateObject.components.push(component);
        }
    }


    updatePreview(JSON.stringify(updateObject));
}

function getComponentToJSONObject(component) {
    const id = component.id

    console.log("id:" + id);

    switch (true) {
        case id.startsWith("product-component"):
            return getProductToJSONObject(component);
        case id.startsWith("service-component"):
            break;
        case id.startsWith("component-component"):
            break
    }
}

function getProductToJSONObject(productComponent) {
    const children = productComponent.children;
    let dashboard;
    let productDiv;
    let validProduct;
    console.log(productComponent);
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

    if(validProduct !== undefined){

        console.log("Valid product:");
        console.log(validProduct);
        return {"name": "sušenka"};

    }

}


function updatePreview(jsonBodyToUpdate) {
    console.log(jsonBodyToUpdate);


    const preview = document.getElementById('icalc-preview');
    preview.innerHTML = '';

    const updateObject = JSON.parse(jsonBodyToUpdate);
    const wrapperDiv = document.createElement("div")
    wrapperDiv.id = 'icalc-preview-wrapper';

    const title = document.createElement('h3');
    title.innerText = updateObject["title"];
    wrapperDiv.appendChild(title);


    preview.appendChild(wrapperDiv);
}


// DAHSBOARD LOGIC END /////////

document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('toggleBtn');
    const firstDiv = document.getElementById('firstDiv');
    const secondDiv = document.getElementById('secondDiv');

    toggleBtn.addEventListener('click', () => {
        if (firstDiv.classList.contains('visible')) {
            firstDiv.classList.remove('visible');
            firstDiv.classList.add('icalc-hidden-slow');
            secondDiv.classList.remove('icalc-hidden-slow');
            secondDiv.classList.add('visible');
            secondDiv.classList.remove("display-none")
            setTimeout(() => firstDiv.classList.add("display-none"), 300);
        } else {
            firstDiv.classList.remove('icalc-hidden-slow');
            firstDiv.classList.add('visible');
            secondDiv.classList.remove('visible');
            secondDiv.classList.add('icalc-hidden-slow');
            firstDiv.classList.remove("display-none")
            setTimeout(() => secondDiv.classList.add("display-none"), 300);
        }
        let toggleText = toggleBtn.getAttribute('data-toggled-text');
        let innerText = toggleBtn.innerText;
        toggleBtn.setAttribute('data-toggled-text', innerText);
        toggleBtn.innerText = toggleText;
        toggleBtn.classList.remove('icalc-reappear');
        toggleBtn.classList.add('icalc-reappear');

    });
});

function noComponentFoundError(error) {
    const span = document.createElement('span');
    span.classList.add("text-danger");
    span.classList.add("text-font-weight-bold");
    span.innerText = error;
    return span;
}
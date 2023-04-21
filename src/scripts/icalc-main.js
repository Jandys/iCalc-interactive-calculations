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
    cloneComponent.classList.add("icalc-configurable-draggable-option");

    setNewClonedComponent(cloneComponent);

    nextId++;
    viableComponent.setAttribute("data-next-id", nextId);

    const dashboardItem = document.createElement('div');
    dashboardItem.classList.add('icalc-dashboard-item');
    dashboardItem.appendChild(cloneComponent);

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

    dashboard.appendChild(dashboardItem);

    if (viableComponent) {
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
    [[0, "-- None --"],
        [1, "Label"],
        [2, "Number input"],
        [3, "Sum"]]);


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
            setNewGenericComponent(component);
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
    } else {
        appendConfigButton(dashboard);
    }
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
    }

    select.dataset.selected = 0;

    if (select.children.length === 1) {
        dashboard.removeChild(select);
        dashboard.appendChild(noComponentFoundError("No Generic Component found"));
    } else {
        appendConfigButton(dashboard);
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
    } else {
        appendConfigButton(dashboard);
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

let nextCalculationId;

function dashboard_content_change() {
    let children = dashboard.children;


    let calculationTitleObject = document.getElementById('icalc-calulation-new-name');
    let calculationTitle = calculationTitleObject.value ? calculationTitleObject.value : "New Calculation title";

    let updateObject = {}
    updateObject.title = calculationTitle;
    updateObject.components = [];
    updateObject.customStyles = "";


    for (const child of children) {
        console.log("Child: ");
        console.log(child);
        const component = getComponentToJSONObject(child.children[0]);
        if (component != null) {
            updateObject.components.push(component);
            updateObject.customStyles = appendStyles(updateObject.customStyles, component);
        }
    }

    if (nextCalculationId) {
        updateObject.id = nextCalculationId;
        updatePreview(JSON.stringify(updateObject));

    } else {
        let nextIdXHR = icalc_getNextCalculationDescriptionId();

        nextIdXHR.onreadystatechange = function () {
            if (nextIdXHR.readyState === XMLHttpRequest.DONE) {
                if (nextIdXHR.status === 200) {
                    let nextId = JSON.parse(nextIdXHR.responseText);
                    updateObject.id = nextId;
                    nextCalculationId = nextId;
                    updatePreview(JSON.stringify(updateObject));
                } else {
                    console.log('Error fetching data:', nextIdXHR.status);
                }
            }
        };
        nextIdXHR.send();
    }
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
            return getGenericComponentToJSONObject(component);
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

    if (validProduct !== undefined) {

        console.log("Valid product:");
        console.log(validProduct);


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
        "type": "genericComponent",
        "id": parseInt(select.id.match(/\d+/)[0], 10),
        "conf": {
            "confId": modalConfId,
            "configuration": conf
        },
        "displayType": genericTypes.get(Number(select.dataset.selected)),
    };
}

function getConfigureModal(id) {

    return `
    <div class="icalc-modal-wrapper hidden">
        <div id="modal-${id}" class="icalc-config-modal">
        <div class="modal-content p-3">
           <button class="icalc-config-btn btn-danger mt-2 close-btn"><i class="dashicons dashicons-no"></i></button>
          <h2>Personal Customization</h2>
          
        <span>
              <label for="show-label">Show label:</label>
              <input type="checkbox" id="show-label" name="show-label" class="icalc-custom-input form-check form-switch mb-2 ml-2 mr-4" data-previous=""/> 
        </span>
         
         <span class="hidden" id="label-configuration">
               <label for="label-classes">Label classes:</label>
               <input type="text" id="label-classes" name="label-class" class="icalc-custom-input mt-0 mb-2 ml-4 mr-4" data-previous=""/> 
        </span>
         
      <span>
           <label for="classes">Input classes:</label>
           <input type="text" id="classes" name="input-class" class="icalc-custom-input mt-0 mb-2 ml-4 mr-4" data-previous=""/> 
        </span>
        
           <p class="font-italic font-weight-light text-info">To add multiple classes separate them by using semicolon: ';' </p>
            
          <label for="text-area">Custom CSS:</label>
          <textarea class="icalc-custom-input icalc-custom-styler mt-0 mb-4 ml-4 mr-4"  id="text-area" name="custom-css" rows="8" cols="50" placeholder=".myStyle{color:red}" data-previous=""></textarea>
        
          <button class="icalc-config-btn btn-success mt-2 save-btn"><i class="dashicons dashicons-saved"></i></button>
        
        </div>
      </div>
    </div>
  
`;

}

function appendConfigButton(div) {
    // Create a new button element
    const button = document.createElement('button');
    button.innerHTML = '<i class="dashicons dashicons-admin-generic"></i>'; // Using Font Awesome for the cog icon
    button.className = 'icalc-config-btn button';

    const id = "conf-" + div.parentNode.id;

    // Append the button to the div
    let configureDiv = div.parentNode.querySelector(".icalc-configuration-bar");
    configureDiv.appendChild(button);

    // Append the modal to the body
    const modalContainer = document.createElement('div');
    modalContainer.innerHTML = getConfigureModal(id);
    document.body.appendChild(modalContainer);

    // Get the modal and close button elements
    const modal = document.getElementById('modal-' + id);
    const closeBtn = modal.querySelector('.close-btn');
    const saveBtn = modal.querySelector('.save-btn');
    const showLabel = modal.querySelector('#show-label');


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
        dashboard_content_change();
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
        let wrappingSpan = modal.querySelector("#label-configuration");
        console.log(showLabel.value);
        if (showLabel.checked === true) {
            wrappingSpan.classList.remove("hidden");
        } else {
            wrappingSpan.classList.add("hidden");

            let labelConfigurations = wrappingSpan.querySelectorAll('.icalc-custom-input');
            labelConfigurations.forEach((input) => {
                input.value = ""
            });
        }
    }

    // Event listeners
    button.addEventListener('click', () => {
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

    const form = document.createElement("form");

    icalc_calculations.set(updateObject["id"], []);;

    for (const component of updateObject["components"]) {
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
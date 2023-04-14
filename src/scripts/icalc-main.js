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


// Attach an event listener for the change event on the select element

    productSelectList.setAttribute("onchange", "icalc_product_select_change(this)");

    dashboardProducts.appendChild(productSelectList);
    dashboardProducts.appendChild(productDiv)
})


function setNewClonedComponent(component) {
    console.log(component);
    let id = component.id;

    switch (true) {
        case id.startsWith("product-component"):
            setNewProductComponent(component);
            break;

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
        selectedElement.classList.remove("hidden");
    };


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






function dashboard_content_change(){
    let content = "";
    let children = dashboard.children;
    for(const child of children){
        console.log(child.children[0]);
        content += child.children[0].id + ", ";
    }

    console.log(content);
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


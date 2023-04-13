const draggableComponents = document.querySelectorAll('.icalc-draggable');
const dashboard = document.getElementById('icalc-dashboard');
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
    console.log("Data drop:");
    const id = e.dataTransfer.getData('text/plain');
    console.log("dropped: " + id);
    const draggedComponent = document.getElementById(id);
    console.log("Element = " + draggedComponent);
    const viableComponentId = draggedComponent.getAttribute('data-component');
    const viableComponent = document.getElementById(viableComponentId);

    const cloneComponent = viableComponent.cloneNode(true);
    cloneComponent.classList.remove("hidden");
    let nextId = Number(viableComponent.getAttribute("data-next-id"));
    cloneComponent.id = cloneComponent.id + nextId;

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
});

body.addEventListener('dragover', e => {
    e.preventDefault();
});

body.addEventListener('drop', e => {
    e.preventDefault();
    if (draggedDashboardItem && !dashboard.contains(e.target)) {
        dashboard.removeChild(draggedDashboardItem);
        draggedDashboardItem = null;
    }
});

function moveComponent(dashboardItem, direction) {
    if (direction === 'up') {
        if (dashboardItem.previousElementSibling) {
            dashboard.insertBefore(dashboardItem, dashboardItem.previousElementSibling);
        }
    } else if (direction === 'down') {
        if (dashboardItem.nextElementSibling) {
            dashboard.insertBefore(dashboardItem.nextElementSibling, dashboardItem);
        }
    }
}

// loaders
document.addEventListener('DOMContentLoaded', () => {
    const dashboradProducts = document.getElementById('icalc-dashboard-products');

    const input = document.createElement('input');
    const datalist = document.createElement('datalist');
    datalist.id = "productDatalist";

    console.log('GETTING PRODUCTS');

    let products = icalc_getAllProducts();
    console.log(products)


    input.type = "text";
    input.id = "productsInput";
    input.name = "products";
    input.list = datalist;


})


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
            setTimeout(()=> firstDiv.classList.add("display-none"),300);
        } else {
            firstDiv.classList.remove('icalc-hidden-slow');
            firstDiv.classList.add('visible');
            secondDiv.classList.remove('visible');
            secondDiv.classList.add('icalc-hidden-slow');
            firstDiv.classList.remove("display-none")
            setTimeout(()=> secondDiv.classList.add("display-none"),300);
        }
        let toggleText = toggleBtn.getAttribute('data-toggled-text');
        let innerText = toggleBtn.innerText;
        toggleBtn.setAttribute('data-toggled-text',innerText);
        toggleBtn.innerText=toggleText;
        toggleBtn.classList.remove('icalc-reappear');
        toggleBtn.classList.add('icalc-reappear');

    });
});



function icalc_process_tag_edition(id, name, description){
    let nameElement = document.getElementById(name);
    let descriptionElement = document.getElementById(description);

    let nameVal = nameElement.value;
    let descVal = descriptionElement.value;

    const xhr = new XMLHttpRequest();
    const url = '/wp-json/icalc/v1/tags';
    const data = JSON.stringify({
        id: id,
        name: nameVal,
        description: descVal
    });

    xhr.open('PUT', url);
    xhr.setRequestHeader('Content-Type', 'application/json');

    xhr.onreadystatechange = function() {
        if(xhr.readyState === XMLHttpRequest.DONE) {
            if(xhr.status === 200) {
                console.log('Data successfully updated:', xhr.responseText);
                location.reload();
            } else {
                console.log('Error updating data:', xhr.status);
            }
        }
    };

    xhr.send(data);
}

function icalc_process_tag_creation( name, description){
    let nameElement = document.getElementById(name);
    let descriptionElement = document.getElementById(description);

    let nameVal = nameElement.value;
    let descVal = descriptionElement.value;

    const xhr = new XMLHttpRequest();
    const url = '/wp-json/icalc/v1/tags';
    const data = JSON.stringify({
        name: nameVal,
        description: descVal
    });

    xhr.open('POST', url);
    xhr.setRequestHeader('Content-Type', 'application/json');

    xhr.onreadystatechange = function() {
        if(xhr.readyState === XMLHttpRequest.DONE) {
            if(xhr.status === 200) {
                console.log('Data successfully updated:', xhr.responseText);
                location.reload();
            } else {
                console.log('Error updating data:', xhr.status);
            }
        }
    };

    xhr.send(data);
}

function icalc_process_tag_deletion(id,name){

    const result = confirm("Opravdu chcete odstranit Tag: " + name + "?" );

    if (result) {
        const xhr = new XMLHttpRequest();
        const url = '/wp-json/icalc/v1/tags';
        const data = JSON.stringify({
            id: id
        });

        xhr.open('DELETE', url);
        xhr.setRequestHeader('Content-Type', 'application/json');

        xhr.onreadystatechange = function() {
            if(xhr.readyState === XMLHttpRequest.DONE) {
                if(xhr.status === 200) {
                    console.log('Data successfully updated:', xhr.responseText);
                    location.reload();
                } else {
                    console.log('Error updating data:', xhr.status);
                }
            }
        };

        xhr.send(data);
    } else {
        // Code to execute if "No" button is clicked
        // Do nothing
    }


}


function icalc_process_service_edition(id, modalId){

    let nameElement = document.getElementById(modalId+'_name_form');
    let descriptionElement = document.getElementById(modalId+'_desc_form');
    let priceElement = document.getElementById(modalId+'_price_form');
    let unitElement = document.getElementById(modalId+'_unit_form');
    let minQualityElement = document.getElementById(modalId+'_min_quantity_form');
    let tagElement = document.getElementById(modalId+'_tag_form');
    let displayTypeElement = document.getElementById(modalId+'_display_type_form');

    const xhr = new XMLHttpRequest();
    const url = '/wp-json/icalc/v1/services';
    const data = JSON.stringify({
        id: id,
        name: nameElement.value,
        description: descriptionElement.value,
        price: priceElement.value,
        unit: unitElement.value,
        minQuality: minQualityElement.value,
        tag:tagElement.value,
        displayType:displayTypeElement.value
    });

    xhr.open('PUT', url);
    xhr.setRequestHeader('Content-Type', 'application/json');

    xhr.onreadystatechange = function() {
        if(xhr.readyState === XMLHttpRequest.DONE) {
            if(xhr.status === 200) {
                console.log('Data successfully updated:', xhr.responseText);
                location.reload();
            } else {
                console.log('Error updating data:', xhr.status);
            }
        }
    };

    xhr.send(data);
}

function icalc_process_service_creation(modalId){

    let nameElement = document.getElementById(modalId+'_name_form');
    let descriptionElement = document.getElementById(modalId+'_desc_form');
    let priceElement = document.getElementById(modalId+'_price_form');
    let unitElement = document.getElementById(modalId+'_unit_form');
    let minQualityElement = document.getElementById(modalId+'_min_quantity_form');
    let tagElement = document.getElementById(modalId+'_tag_form');
    let displayTypeElement = document.getElementById(modalId+'_display_type_form');

    const xhr = new XMLHttpRequest();
    const url = '/wp-json/icalc/v1/services';
    const data = JSON.stringify({
        name: nameElement.value,
        description: descriptionElement.value,
        price: priceElement.value,
        unit: unitElement.value,
        minQuality: minQualityElement.value,
        tag:tagElement.value,
        displayType:displayTypeElement.value
    });

    xhr.open('POST', url);
    xhr.setRequestHeader('Content-Type', 'application/json');

    xhr.onreadystatechange = function() {
        if(xhr.readyState === XMLHttpRequest.DONE) {
            if(xhr.status === 200) {
                console.log('Data successfully updated:', xhr.responseText);
                location.reload();
            } else {
                console.log('Error updating data:', xhr.status);
            }
        }
    };

    xhr.send(data);
}

function icalc_process_service_deletion(id,name){

    const result = confirm("Opravdu chcete odstranit Službu: " + name + "?" );

    if (result) {
        const xhr = new XMLHttpRequest();
        const url = '/wp-json/icalc/v1/services';
        const data = JSON.stringify({
            id: id
        });

        xhr.open('DELETE', url);
        xhr.setRequestHeader('Content-Type', 'application/json');

        xhr.onreadystatechange = function() {
            if(xhr.readyState === XMLHttpRequest.DONE) {
                if(xhr.status === 200) {
                    console.log('Data successfully updated:', xhr.responseText);
                    location.reload();
                } else {
                    console.log('Error updating data:', xhr.status);
                }
            }
        };

        xhr.send(data);
    } else {
        // Code to execute if "No" button is clicked
        // Do nothing
    }


}

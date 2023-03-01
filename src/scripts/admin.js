
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



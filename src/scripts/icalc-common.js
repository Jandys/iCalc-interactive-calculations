

function icalc_getProductById(id){
    const xhr = new XMLHttpRequest();
    const url = `/wp-json/icalc/v1/products/${id}`;
    xhr.open('GET', url);
    xhr.setRequestHeader('Content-Type', 'application/json');
    return xhr;
}
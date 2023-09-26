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

function interactivecalculations_process_service_edition(id, modalId) {
    let nameElement = document.getElementById(modalId + '_name_form');
    let descriptionElement = document.getElementById(modalId + '_desc_form');
    let priceElement = document.getElementById(modalId + '_price_form');
    let unitElement = document.getElementById(modalId + '_unit_form');
    let minQualityElement = document.getElementById(modalId + '_min_quantity_form');
    let displayTypeElement = document.getElementById(modalId + '_display_type_form');

    const xhr = new XMLHttpRequest();
    const url = '/wp-json/interactivecalculations/v1/services';
    const data = JSON.stringify({
        id: id,
        name: nameElement.value,
        description: descriptionElement.value,
        price: priceElement.value,
        unit: unitElement.value,
        minQuality: minQualityElement.value,
        displayType: displayTypeElement.value
    });

    xhr.open('PUT', url);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.setRequestHeader('user', interactivecalculationsApiSettings.user);
    xhr.setRequestHeader('session', interactivecalculationsApiSettings.session);

    xhr.withCredentials = true;

    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                console.log('Data successfully updated:', xhr.responseText);
                location.reload();
            } else {
                console.log('Error updating data:', xhr.status);
            }
        }
    };

    xhr.send(data);
}

function interactivecalculations_process_service_creation(modalId) {

    let nameElement = document.getElementById(modalId + '_name_form');
    let descriptionElement = document.getElementById(modalId + '_desc_form');
    let priceElement = document.getElementById(modalId + '_price_form');
    let unitElement = document.getElementById(modalId + '_unit_form');
    let minQualityElement = document.getElementById(modalId + '_min_quantity_form');
    let displayTypeElement = document.getElementById(modalId + '_display_type_form');

    const xhr = new XMLHttpRequest();
    const url = '/wp-json/interactivecalculations/v1/services';
    const data = JSON.stringify({
        name: nameElement.value,
        description: descriptionElement.value,
        price: priceElement.value,
        unit: unitElement.value,
        minQuality: minQualityElement.value,
        displayType: displayTypeElement.value
    });

    xhr.open('POST', url);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.setRequestHeader('user', interactivecalculationsApiSettings.user);
    xhr.setRequestHeader('session', interactivecalculationsApiSettings.session);

    xhr.withCredentials = true;

    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                console.log('Data successfully updated:', xhr.responseText);
                location.reload();
            } else {
                console.log('Error updating data:', xhr.status);
            }
        }
    };

    xhr.send(data);
}

function interactivecalculations_process_service_deletion(id, name) {

    const result = confirm("Opravdu chcete odstranit Službu: " + name + "?");

    if (result) {
        const xhr = new XMLHttpRequest();
        const url = '/wp-json/interactivecalculations/v1/services';
        const data = JSON.stringify({
            id: id
        });

        xhr.open('DELETE', url);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('user', interactivecalculationsApiSettings.user);
        xhr.setRequestHeader('session', interactivecalculationsApiSettings.session);

        xhr.withCredentials = true;

        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
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


function interactivecalculations_process_product_edition(id, modalId) {

    let nameElement = document.getElementById(modalId + '_name_form');
    let descriptionElement = document.getElementById(modalId + '_desc_form');
    let priceElement = document.getElementById(modalId + '_price_form');
    let unitElement = document.getElementById(modalId + '_unit_form');
    let minQualityElement = document.getElementById(modalId + '_min_quantity_form');
    let displayTypeElement = document.getElementById(modalId + '_display_type_form');

    const xhr = new XMLHttpRequest();
    const url = '/wp-json/interactivecalculations/v1/products';
    const data = JSON.stringify({
        id: id,
        name: nameElement.value,
        description: descriptionElement.value,
        price: priceElement.value,
        unit: unitElement.value,
        minQuality: minQualityElement.value,
        displayType: displayTypeElement.value
    });

    xhr.open('PUT', url);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.setRequestHeader('user', interactivecalculationsApiSettings.user);
    xhr.setRequestHeader('session', interactivecalculationsApiSettings.session);

    xhr.withCredentials = true;

    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                console.log('Data successfully updated:', xhr.responseText);
                location.reload();
            } else {
                console.log('Error updating data:', xhr.status);
            }
        }
    };

    xhr.send(data);
}

function interactivecalculations_process_product_creation(modalId) {

    let nameElement = document.getElementById(modalId + '_name_form');
    let descriptionElement = document.getElementById(modalId + '_desc_form');
    let priceElement = document.getElementById(modalId + '_price_form');
    let unitElement = document.getElementById(modalId + '_unit_form');
    let minQualityElement = document.getElementById(modalId + '_min_quantity_form');
    let displayTypeElement = document.getElementById(modalId + '_display_type_form');

    const xhr = new XMLHttpRequest();
    const url = '/wp-json/interactivecalculations/v1/products';
    const data = JSON.stringify({
        name: nameElement.value,
        description: descriptionElement.value,
        price: priceElement.value,
        unit: unitElement.value,
        minQuality: minQualityElement.value,
        displayType: displayTypeElement.value
    });

    xhr.open('POST', url);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.setRequestHeader('user', interactivecalculationsApiSettings.user);
    xhr.setRequestHeader('session', interactivecalculationsApiSettings.session);

    xhr.withCredentials = true;

    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                console.log('Data successfully updated:', xhr.responseText);
                location.reload();
            } else {
                console.log('Error updating data:', xhr.status);
            }
        }
    };

    xhr.send(data);
}

function interactivecalculations_process_product_deletion(id, name) {

    const result = confirm("Opravdu chcete odstranit Službu: " + name + "?");

    if (result) {
        const xhr = new XMLHttpRequest();
        const url = '/wp-json/interactivecalculations/v1/products';
        const data = JSON.stringify({
            id: id
        });

        xhr.open('DELETE', url);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('user', interactivecalculationsApiSettings.user);
        xhr.setRequestHeader('session', interactivecalculationsApiSettings.session);

        xhr.withCredentials = true;

        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
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

var interactivecalculationsApiSettings = {};

function populateinteractivecalculationsSettings(user, session) {
    interactivecalculationsApiSettings.user = user;
    interactivecalculationsApiSettings.session = session;
}


function interactivecalculations_getAllProducts() {
    const xhr = new XMLHttpRequest();
    const url = '/wp-json/interactivecalculations/v1/products';


    xhr.open('GET', url);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.setRequestHeader('user', interactivecalculationsApiSettings.user);
    xhr.setRequestHeader('session', interactivecalculationsApiSettings.session);

    xhr.withCredentials = true;

    return xhr;
}


async function interactivecalculations_getNextCalculationDescriptionId() {
    return new Promise(function (resolve, reject) {
        const xhr = new XMLHttpRequest();
        const url = `/wp-json/interactivecalculations/v1/interactivecalculationsulation-descriptions/next`;
        xhr.open('GET', url);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('user', interactivecalculationsApiSettings.user);
        xhr.setRequestHeader('session', interactivecalculationsApiSettings.session);

        xhr.withCredentials = true;

        xhr.onload = function () {
            if (xhr.status === 200) {
                resolve(JSON.parse(xhr.responseText));
            } else {
                reject(xhr.statusText);
            }
        };

        xhr.onerror = function () {
            reject("Network error");
        };
        xhr.send();
    });
}


function interactivecalculations_getAllServices() {
    const xhr = new XMLHttpRequest();
    const url = '/wp-json/interactivecalculations/v1/services';


    xhr.open('GET', url);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.setRequestHeader('user', interactivecalculationsApiSettings.user);
    xhr.setRequestHeader('session', interactivecalculationsApiSettings.session);

    xhr.withCredentials = true;

    return xhr;
}

function interactivecalculations_process_calculation_description_creation() {
    const xhr = new XMLHttpRequest();
    const url = '/wp-json/interactivecalculations/v1/interactivecalculationsulation-descriptions';

    xhr.open('POST', url);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.setRequestHeader('user', interactivecalculationsApiSettings.user);
    xhr.setRequestHeader('session', interactivecalculationsApiSettings.session);

    xhr.withCredentials = true;

    return xhr;
}

function interactivecalculations_process_calculation_description_edit() {
    const xhr = new XMLHttpRequest();
    const url = '/wp-json/interactivecalculations/v1/interactivecalculationsulation-descriptions';

    xhr.open('PUT', url);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.setRequestHeader('user', interactivecalculationsApiSettings.user);
    xhr.setRequestHeader('session', interactivecalculationsApiSettings.session);

    xhr.withCredentials = true;

    return xhr;
}

function interactivecalculations_process_calculation_delete_action(id, name) {

    const result = confirm("Opravdu chcete odstranit Kalkulaci: " + name + "?");

    if (result) {
        const xhr = new XMLHttpRequest();
        const url = '/wp-json/interactivecalculations/v1/interactivecalculationsulation-descriptions';
        const data = JSON.stringify({
            id: id
        });

        xhr.open('DELETE', url);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('user', interactivecalculationsApiSettings.user);
        xhr.setRequestHeader('session', interactivecalculationsApiSettings.session);

        xhr.withCredentials = true;

        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
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

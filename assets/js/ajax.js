//////////////////////////////////////////
//              VARIABLES               //
//////////////////////////////////////////

// General variables
// TODo: Fix 'url' path
const currentPageName = window.location.pathname.split('/').pop().split('.')[0];
const url = `/tmrank/shells/ajax/${currentPageName}.php`;

// HTML elements
const formId = $('#loginForm');
const loginId = $('#login');

const loginInput = $('#login');
const errorIcon = $('.icon .is-small .is-right');
const errorSpan = $('#loginForm .help');


// Validation variables
const maxLoginLength = 25;
const minLoginLength = 3;
const loginRegex =  new RegExp(/^[a-zA-Z0-9_]*$/);

//////////////////////////////////////////
//                 MAIN                 //
//////////////////////////////////////////


// Returns general tables
document.addEventListener('DOMContentLoaded', function() {
    // Ignores 'players' since it does not have a general table
    if(!currentPageName.includes('players')) getGeneralTable(url, extraOptions);
}, false);



// Makes a AJAX request
$(document).ready(function() {

    $(formId).on('reset', function() {
        removeErrorStyles();
    });

    $(formId).submit(function(event) {
        event.preventDefault(); 

        // Player login
        let login = $(loginId).val();

        if(isLoginValid(login)){
            submitForm(url, login, extraOptions);
        }
    });
});


//////////////////////////////////////////
//              FUNCTIONS               //
//////////////////////////////////////////

// AJAX request via form submit
function submitForm(url, login, extraOptions) {
    $.ajax({
        method: "GET",
        url: url,
        data: {
            login: login
        },
        extraOptions,
        success: function(response) {
            if(!response.includes('{')) {
                showError(response);
            } 
            else {
                console.log(JSON.parse(response));
            }
        },
        error:  function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR.status);
        }
    });
}

// Automatic AJAX request for general data
function getGeneralTable(url, extraOptions) {
    $.ajax({
        method: "GET",
        url: url,
        data: {
            login: ''
        },
        cache: true,
        //extraOption,
        success: function(response) {
            if(!response.includes('{')) {
                console.log(response);
            } 
            else {
                let objResponse = JSON.parse(response)

                // Zones
                if(objResponse.hasOwnProperty('ladder')){
                    $('#general').append(objResponse.ladder[0].name + '<br>');
                    $('#general').append(objResponse.ladder[1].name + '<br>');
                    $('#general').append(objResponse.ladder[2].name + '<br>');
                    $('#general').append(objResponse.ladder[3].name + '<br>');
                    $('#general').append(objResponse.ladder[4].name + '<br>');
                }
                // World
                else {
                    $('#general').append(objResponse.stadium[0].nickname + '<br>');
                    $('#general').append(objResponse.stadium[1].nickname + '<br>');
                    $('#general').append(objResponse.stadium[2].nickname + '<br>');
                    $('#general').append(objResponse.stadium[3].nickname + '<br>');
                    $('#general').append(objResponse.stadium[4].nickname + '<br>');
                }

            }
        },
        error:  function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR.status);
        }
    });
}


// Add extra AJAX options for specific classes
// @author 1rives
const extraOptions = () => {
    switch (currentPageName) {
        case 'world':
            return 'contentType: application/json; charset=utf-8';

        default:
            break;
    }
}

// Converts to lower case and removes all spaces
function sanitizeString(string) {
    return string.replace(/\s+/g, "").toLowerCase();
}

// Original PHP function converted to Javascript
// Returns true for valid login
function isLoginValid(login) {

    const errorMessages = [
        'Login cannot be empty',
        `Login must be at least ${minLoginLength} characters long`,
        `Login cannot exceed ${maxLoginLength} characters`,
        'Login can only contain letters, numbers and underscores'
    ];

    login = sanitizeString(login);

    switch(true) {
        case !login: 
            showError(errorMessages[0]);
            return 0;

        case login.length < 3:
            showError(errorMessages[1]);
            return 0;

        case login.length > 25:
            showError(errorMessages[2]);
            return 0;

        case !loginRegex.test(login):
            showError(errorMessages[3]);
            return 0;

        default:
            showError(); // Removes all previous changes
            return 1;
    }

}

// Changes styles for form-related elements 
function showError(errorMessage) {
    (!errorMessage) ? removeErrorStyles() : addErrorStyles(errorMessage);
}

// Resets all error styles and remove error mesage
function removeErrorStyles() {
    loginInput.removeClass('is-danger');
    errorIcon.remove('i');
    errorSpan.empty();
}

// Adds error styles and the error message
function addErrorStyles(errorMessage) {
    loginInput.addClass('is-danger');
    errorIcon.append('<i class="fas fa-exclamation-triangle"></i>');
    errorSpan.text(errorMessage);
}


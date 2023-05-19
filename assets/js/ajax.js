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

const envList = [
    'Merge',
    'Stadium',
    'Desert',
    'Island',
    'Rally',
    'Coast',
    'Bay',
    'Snow'
];

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

    // Reset error styles on reset button
    $(formId).on('reset', function() {
        removeErrorStyles();
    });

    // AJAX request
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

                // TODO: Loading all data after making the AJAX request then passing
                // all the object is too heavy on the browser, try doing the request
                // on a DataTables initialization. (Or just find a better way)

                // Zones
                if(objResponse.hasOwnProperty('ladder')){
                    $('#general').append(objResponse.ladder[0].name + '<br>');
                    $('#general').append(objResponse.ladder[1].name + '<br>');
                    $('#general').append(objResponse.ladder[2].name + '<br>');
                    $('#general').append(objResponse.ladder[3].name + '<br>');
                    $('#general').append(objResponse.ladder[4].name + '<br>');

                    $('table[id="tableZones"]').DataTable({
                        paging: false,
                        ordering: false,
                        info: false,
                        searching: false,
        
                        deferLoading: true,
                        stateSave: true,
                        responsive: true,
                    });
                }
                // World
                else {
                    // $('.tabs').append(objResponse.stadium[0].rank + '<br>');
                    // $('.tabs').append(objResponse.stadium[0].nickname + '<br>');
                    // $('.tabs').append(objResponse.stadium[0].nation + '<br>');
                    // $('.tabs').append(objResponse.stadium[0].points + '<br>');
                    
                    const envListLength = envList.length;

                    for (let index = 0; index < envListLength; index++) {

                        var tableEnv = envList[index];
                        var objectEnv = tableEnv.toLowerCase();
                        
                        var dataArray = [];

                        // TODO: Save in db the contents of the table instead of the world Object

                        // Hardcoded test
                        // At least works . . .
                        for (let index = 0; index < 10; index++) {
                            dataArray.push([ objResponse[objectEnv][index].rank, objResponse[objectEnv][index].nickname, objResponse[objectEnv][index].nation, objResponse[objectEnv][index].points ],)
                            
                        }

                        $(`table[id="table${tableEnv}"]`).DataTable({
                            paging: false,
                            ordering: false,
                            info: false,
                            searching: false,
                            deferLoading: true,
                            stateSave: true,
                            responsive: true,
                            data: [
                                [ objResponse[objectEnv][0].rank, objResponse[objectEnv][0].nickname, objResponse[objectEnv][0].nation, objResponse[objectEnv][0].points ],
                                [ objResponse[objectEnv][1].rank, objResponse[objectEnv][1].nickname, objResponse[objectEnv][1].nation, objResponse[objectEnv][1].points ],
                                [ objResponse[objectEnv][2].rank, objResponse[objectEnv][2].nickname, objResponse[objectEnv][2].nation, objResponse[objectEnv][2].points ],
                                [ objResponse[objectEnv][3].rank, objResponse[objectEnv][3].nickname, objResponse[objectEnv][3].nation, objResponse[objectEnv][3].points ],
                                [ objResponse[objectEnv][4].rank, objResponse[objectEnv][4].nickname, objResponse[objectEnv][4].nation, objResponse[objectEnv][4].points ],
                                [ objResponse[objectEnv][5].rank, objResponse[objectEnv][5].nickname, objResponse[objectEnv][5].nation, objResponse[objectEnv][5].points ],
                                [ objResponse[objectEnv][6].rank, objResponse[objectEnv][6].nickname, objResponse[objectEnv][6].nation, objResponse[objectEnv][6].points ],
                                [ objResponse[objectEnv][7].rank, objResponse[objectEnv][7].nickname, objResponse[objectEnv][7].nation, objResponse[objectEnv][7].points ],
                                [ objResponse[objectEnv][8].rank, objResponse[objectEnv][8].nickname, objResponse[objectEnv][8].nation, objResponse[objectEnv][8].points ],
                                [ objResponse[objectEnv][9].rank, objResponse[objectEnv][9].nickname, objResponse[objectEnv][9].nation, objResponse[objectEnv][9].points ]
                            ]

                        });

                        
                    }
                        
                    
                }

            }
        },
        error:  function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR.status);
        }
    });
}


// Add extra AJAX options for specific classes
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


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
const envListLength = envList.length;

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
        //extraOption,
        success: function(response) {
            if(!response.includes('{')) {
                console.log(response);
            } 
            else {
                let data = JSON.parse(response);
                showTables(data, envList);
            }
        },
        error:  function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR.status);
        }
    });
}

// General tables initialization
function showTables(data, environmentList) {

    if(data.hasOwnProperty('ladder')) {
        initializeZonesTables(data);
    }

    if(data.hasOwnProperty('merge')) {
        initializeWorldTables(data, environmentList);
    }
}

// Processes all data for the World tables and initializes them
// through DataTables
function initializeWorldTables(worldData, environmentList) {

    for (let i = 0; i < envListLength; i++) {

        // Specific table name
        var tableName = environmentList[i];

        // Properties are in lower case
        var dataEnvironment = tableName.toLowerCase();

        $(`table[id="table${tableName}"]`).DataTable({
            paging: false,
            ordering: false,
            info: false,
            searching: false,

            deferLoading: true,
            stateSave: true,
            responsive: true,

            data: assignTableDataToArray(worldData, dataEnvironment),
            columns: [
                { title: "Rank" },
                { title: "Nickname" },
                { title: "Country" },
                { title: "Ladder Points" }
            ],
            
        });
    }
}

// Processes all data for the Zones table and initializes it
// through DataTables
// Only one table is used, tables for zones and subZones should 
// be implemented later
function initializeZonesTables(zonesData) {

    $(`table[id="tableZones"]`).DataTable({
        paging: true,
        pagingType: 'first_last_numbers',
        ordering: false,
        info: false,
        searching: true,

        deferLoading: true,
        stateSave: true,
        responsive: true,

        data: assignTableDataToArray(zonesData, null),
        columns: [
            { title: "Rank" },
            { title: "Name" },
            { title: "Ladder Points" }
        ]
    });

}


// Returns general data formatted for DataTables depending
// on the called class
function assignTableDataToArray(data, env) {
    
    let dataArray = [];

    // World 
    if(env) {
        for (let i = 0; i < 10; i++) {
            dataArray.push([data[env][i].rank, 
                            data[env][i].nickname, 
                            data[env][i].nation, 
                            data[env][i].points],);
        }
    }
    // Zones
    else {
        let zonesLength = data.ladder.length;
        for (let i = 0; i < zonesLength; i++) {
            dataArray.push([data.ladder[i].rank, 
                            data.ladder[i].name, 
                            data.ladder[i].points],);
        }
    }
    
    return dataArray;
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


//////////////////////////////////////////
//              VARIABLES               //
//////////////////////////////////////////

// General variables
const currentPageName = window.location.pathname.split('/').pop().split('.')[0];
const url = `/tmrank/shells/ajax/${currentPageName}.php`;

// HTML elements
const formId = $('#loginForm');
const loginId = $('#login');

const loginInput = $('#login');
const errorIcon = $('.icon .is-small .is-right');
const errorSpan = $('#loginForm .help');

const worldPlayerButton = $('li#tabPlayer');

var savedTab = '';
var savedTable = '';
var isPlayerTableHidden = 1;

// Validation variables
const maxLoginLength = 25;
const minLoginLength = 3;
const loginRegex =  new RegExp(/^[a-zA-Z0-9_]*$/);
var previousLogin = '';

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

    // Reset error styles on reset button
    $(worldPlayerButton).on('click', function() {

        // Doesn't works without this timeout
        setTimeout(() => {
            hideWorldPlayerTable();
          }, 10);

        isPlayerTableHidden++;
    });


    // AJAX request
    $(formId).submit(function(event) {
        event.preventDefault(); 

        // Player login
        let login = sanitizeString($(loginId).val());

        if(isLoginValid(login) && login !== previousLogin){
            // Saves current login to prevent the same AJAX request
            // for the username
            previousLogin = login;

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
        beforeSend: function() {
            // Shows loading and disables the button
            $('#submitButton').addClass('is-loading').prop( "disabled", true );

        },
        success: function(response) {
            // Reset button to default
            $('#submitButton').removeClass('is-loading').prop( "disabled", false );

            if(!response.includes('{')) {
                // Shows "Player not found" without double quotations
                showError(response.replaceAll('"', ''));
            } 
            else {
                console.log(response);
                let data = JSON.parse(response);

                // Player
                if(data.hasOwnProperty('accountType')) 
                    showPlayersData(data, envList);

                // World
                else {
                    
                    appendWorldPlayerData(data);
                    
                    if(isPlayerTableHidden) {
                        showWorldPlayerTable();
                        isPlayerTableHidden--;
                    }
                        
                } 
                    
            }
        },
        error:  function(jqXHR, textStatus, errorThrown) {
            // Reset button to default
            $('#submitButton').removeClass('is-loading').prop( "disabled", false );

            showError("An error has occurred, try later");
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
                console.log(response);
                let data = JSON.parse(response);
                
                showTables(data);
            }
        },
        error:  function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR.status);
        }
    });
}


// Add the obtained player data to the columns
function showPlayersData(data) {   

    // General player data
    $('#player-nickname').empty().append(data.nickname);
    $('#player-account-type').text(`Account type: ${data.accountType}`);
    $('#player-account-location').text(`Account location: ${data.nation}`);

    appendMultiPlayersData(data, envList);

    appendSoloPlayersData(data);

    // Data is set, now show the columns
    unhidePlayersData(data.accountType); 
}

// Hides the current active table and shows the player table
// Use once, then hide with the following function
function showWorldPlayerTable() {
    // Tables
    let currentTable = $('.table:visible');
    let playerTable = $('table[id=tablePlayer]');

    // Tabs containers
    let tabsContainer = $('div[id=tableTabs]');
    let playerTabContainer = $('div[id=tableTabPlayer]');

    // Specific tabs
    let currentTab = $('#tableTabs li.is-active');
    let playerTab = $('li[id=tabPlayer]');
    
    // Save user selected items
    setSelectedItems(currentTab, currentTable);
    
    tabsContainer.addClass('is-hidden');
    playerTabContainer.removeClass('is-hidden');

    currentTable.addClass('is-hidden');
    playerTable.show();

    currentTab.removeClass('is-active');
    playerTab.addClass('is-active');

}

// Hides the player table and shows the previous 
// selected World table/tab
function hideWorldPlayerTable() {
    // Tables
    let lastSelectedTable = savedTable;
    let playerTable = $('table[id=tablePlayer]');

    // Tabs containers
    let tabsContainer = $('div[id=tableTabs]');
    let playerTabContainer = $('div[id=tableTabPlayer]');

    // Previously selected tab
    let lastSelectedTab = savedTab;
    let playerTab = $('li[id=tabPlayer]');
    
    tabsContainer.removeClass('is-hidden');
    playerTabContainer.addClass('is-hidden');

    lastSelectedTable.removeClass('is-hidden');
    playerTable.hide();
    
    lastSelectedTab.addClass('is-active');
    playerTab.removeClass('is-active');

    // Enable search on same username
    previousLogin = '';
}

// Passes to a global variable the selected tab and table
function setSelectedItems(tab, table) {
    savedTab = tab;
    savedTable = table;
}

// By default, the data columns are hidden
// When the content is ready, show columns
function unhidePlayersData(accountType) {
    let content = $('#data-container');
    let unitedContent = $('#united-container');

    content.removeClass('is-hidden');

    // Hides the United content for the Forever account
    if(!accountType.includes('United')) {

        // Hides the United container
        unitedContent.toggleClass('is-unavailable is-unselectable is-hidden-mobile');

        // Resets current United values if previously submitted an
        // United account
        for (let index = 2; index < envListLength; index++) {

            // Obtain environment 
            let env = envList[index].toLowerCase();
    
            let worldRanking = $(`#${env}-world-ranking`);
            let nationRanking = $(`#${env}-nation-ranking`);
            let pointsRanking = $(`#${env}-points`);
        
            worldRanking.text('World ranking: -');
            nationRanking.text('Nation ranking: -');
            pointsRanking.text('Ladder Points: -');
    
        }

        $('#solo-world-ranking').text(`World ranking: -`);
        $('#solo-points').text(`Ladder Points: -`);

    } else {
        // Shows the United container if hidden
        unitedContent.removeClass('is-unavailable is-unselectable is-hidden-mobile');
    }
        
}

// Returns all multiplayer data from the player
function appendMultiPlayersData(data, envList) {
    
    // Available enviroments for player
    playerEnvs = getPlayerEnvironmentsCount(data.accountType);

    for (let index = 0; index < playerEnvs; index++) {

        // Obtain environment 
        let env = envList[index].toLowerCase();

        let worldRanking = $(`#${env}-world-ranking`);
        let nationRanking = $(`#${env}-nation-ranking`);
        let pointsRanking = $(`#${env}-points`);
    
        // ex.: World ranking: data.stadiumWorldRanking
        worldRanking.text(`World ranking: ${data[env + "WorldRanking"]}`);
        nationRanking.text(`Nation ranking: ${data[env + "ZoneRanking"]}`);
        pointsRanking.text(`Ladder Points: ${data[env + "Points"]}`);

    }

    
}

// Returns all solo data from the player
function appendSoloPlayersData(data) {

    // Only United accounts have solo ranking
    if(data.accountType.includes("United")) {
        $('#solo-world-ranking').text(`World ranking: ${data.soloWorldRanking}`);
        $('#solo-points').text(`Ladder Points: ${data.soloPoints}`);
    }

}

// Obtain all available environments for the current player
function getPlayerEnvironmentsCount(accountType) {

    if(accountType.includes("United")) {
        return envList.length
    } else {
        return 2; // Merge and Stadium
    }
}


// General tables initialization
function showTables(data) {

    if(data.hasOwnProperty('merge')) 
        initializeWorldTables(data, envList);

    if(data.hasOwnProperty('ladder')) 
        initializeZonesTables(data);
    
}


// Processes all data for the World tables and initializes them
// through DataTables
function initializeWorldTables(worldData, environmentList) {

    // Initializes all general tables
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

            "stateSave": true,
            "stateDuration": -1, // Saved in current session
            responsive: true,

            data: assignGeneralTablesDataToArray(worldData, dataEnvironment),
            columns: [
                { title: "Rank" },
                { title: "Nickname" },
                { title: "Country" },
                { title: "Ladder Points" }
            ],
            
        });

        // Only Merge tab should be shown
        if(!tableName.includes('Merge'))
            $(`table[id="table${tableName}"]`).toggle();    
    }

    initializeWorldPlayerTable();

}

// Initializes the hidden Player table
function initializeWorldPlayerTable() {
     
     $(`table[id="tablePlayer"]`).toggle().DataTable({
        paging: false,
        ordering: false,
        info: false,
        searching: false,

        "stateSave": true,
        "stateDuration": -1, // Saved in current session
        responsive: true,

        columns: [
            { title: "Rank" },
            { title: "Nickname" },
            { title: "Country" },
            { title: "Ladder Points" }
        ],
        
    });
}

// Processes the top 10 for the Player's Merge table and initializes them
// through DataTables
function appendWorldPlayerData(playerData) {

    var playerTable = $('table[id="tablePlayer"]').DataTable();

    // Converts data to an array for DataTables
    var processedPlayerData = assignPlayerTableDataToArray(playerData);

    // Removes all previous data
    playerTable.clear();

    for (let index = 0; index < 10; index++) {
        playerTable.row
            .add(processedPlayerData[index])
            .draw(); 
    }
    
}

// Processes all data for the Zones table and initializes it
// through DataTables
// Only one table is used atm, tables for zones and subZones should 
// be implemented later
function initializeZonesTables(zonesData) {

    $(`table[id="tableZones"]`).DataTable({
        paging: true,
        pagingType: 'first_last_numbers',
        ordering: false,
        info: false,
        searching: true,

        stateSave: true,
        stateDuration: -1, // Saved in current session
        responsive: true,

        data: assignGeneralTablesDataToArray(zonesData, null),
        columns: [
            { title: "Rank" },
            { title: "Name" },
            { title: "Ladder Points" }
        ]
    });

}


// Returns general data formatted for DataTables depending
// on the called class
function assignGeneralTablesDataToArray(data, env) {
    
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

// Returns player data formatted for DataTables depending
// on the called class
function assignPlayerTableDataToArray(data) {
    
    let dataArray = [];

    // World 
    for (let i = 0; i < 10; i++) {
        dataArray.push([data[i].rank, 
                        data[i].nickname, 
                        data[i].nation, 
                        data[i].points],);
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


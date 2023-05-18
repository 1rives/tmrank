$(document).ready(function () {

    // Get the clicked tab element
    $('li[id^="tab"]').on('click', function (event) {

        active = $('.is-active').attr('id');
        clicked = this.id;

        // Checks if the active tab is the same as the clicked tab
        if(active !== clicked) {

            let activeTab = $('.is-active');
            let clickedTab = $('#' + this.id);
            
            let activeTable = $(".table:not(.is-hidden)");
            let clickedTable = $('#table' + this.id.replace('tab', ''));

            changeActiveTable(activeTable, clickedTable);
            changeActiveTab(activeTab, clickedTab);
        }
    })
});

function changeActiveTable(active, clicked) {
    active.addClass('is-hidden');
    clicked.removeClass('is-hidden')
}

function changeActiveTab(active, clicked) {
    active.removeClass('is-active');
    clicked.addClass('is-active');
}
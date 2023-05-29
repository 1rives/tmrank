$(document).ready(function () {

    // Get the clicked tab element
    $('li[id^="tab"]').on('click', function (event) {

        active = $('.is-active').attr('id');
        clicked = this.id;

        // Checks if the active tab is the same as the clicked tab
        if(active !== clicked) {

            let activeTab = $('.is-active');
            let clickedTab = $('#' + this.id);
            
            let activeTable = $(".table:visible");
            let clickedTable = $('#table' + this.id.replace('tab', ''));

            clickedTable.toggle();
            activeTable.toggle();

            changeSelectedTab(activeTab, clickedTab);
        }
    })
});

function changeSelectedTab(active, clicked) {
    active.removeClass('is-active');
    clicked.addClass('is-active');
}
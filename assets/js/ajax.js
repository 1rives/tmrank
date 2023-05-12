$(document).ready(function() {
    $('#playerForm').submit(function(event) {
        event.preventDefault(); 
        // Default values
        var login = $('#loginTest').val();
        var url = '/tmrank/test.php';
        $.ajax({
            type: 'GET',
            url: url,
            contentType: "application/json; charset=utf-8",
            data: {
                login: login
            },
        success: function(response) {
            // Fix null at end error
            let nullFix = response.slice(0, -4);

            let data = JSON.parse(nullFix);

            console.log(data);
            alert(data.nickname);

        },
        error:  function( jqXHR, textStatus, errorThrown ) {
            console.log(textStatus);
        }
        });
    });

    $('#worldForm').submit(function(event) {
        event.preventDefault(); 

        // Default values
        var loginVar = $('#loginTest').val();
        var url = '/tmrank/test.php';
        
        $.ajax({
            type: 'GET',
            url: url,
            contentType: "application/json; charset=utf-8",
            data: {
                login: login
            },
        success: function(response) {
            // Fix null at end error
            let nullFix = response.slice(0, -4);

            let data = JSON.parse(nullFix);

            console.log(data);
            alert(data.nickname);

        },
        error:  function( jqXHR, textStatus, errorThrown ) {
            console.log(textStatus);
        }
        });
    });
});
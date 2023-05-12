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
            headers: {
                'Cache-Control': "max-age=" + 10,
                // For legacy browsers
            },
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
        var loginVar = $('#login').val();
        var url = '/tmrank/shells/ajax/ajax_requests.php';
        
        $.ajaxPreFilter({
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
        },
        beforeSend: function(xhr) {
          const secondsLeft = 10;
          xhr.setRequestHeader("Cache-Control", "max-age=" + secondsLeft);
        }

        });
    });
});
$(document).ready(function() {
    var url = '/tmrank/test.php';

    $('#playerForm').submit(function(event) {
        event.preventDefault(); 

        // Default values
        var login = $('#playerLogin').val();
        
        if(validateLogin(login)) return;

        $.ajax({
            method: "GET",
            url: url,
            contentType: "application/json; charset=utf-8",
            cache: false,
            data: {
                login: login
            },
        success: function(response) {
            if(!response.includes('{')) {
                alert(response);
            } 
            else {
                // null EOF fix with slice
                let data = JSON.parse(response.slice(0, -4));
                console.log(data);
            }
        },
        error:  function( jqXHR, textStatus, errorThrown ) {
            console.log(textStatus);
        }
        });
    });

    $('#worldForm').submit(function(event) {
        event.preventDefault(); 

        // Default values
        var login = $('#worldLogin').val();
        
        if(validateLogin(login)) return;

        $.ajax({
            method: "POST",
            url: url,
            //contentType: "application/json; charset=utf-8",
            data: {
                login: login
            },
        success: function(response) {
            if(!response.includes('{')) {
                alert(response);
            } 
            else {
                // null EOF fix with slice
                let data = JSON.parse(response.slice(0, -4));
                console.log(data);
            }
        },
        error:  function( jqXHR, textStatus, errorThrown ) {
            console.log(textStatus);
        }
        });
    });
});

// TODO: Implement encryption for identifiers

// PHP function converted to Javascript
// @author 1rives
function validateLogin(login) {
    let error = 0;
  
    if (!login) {
      error = 1;
      alert('Please enter a login');
      return error;
    }
  
    if (login.length > 20 || login.length < 3) {
      error = 2;
      alert('Length is not correct');
      return error;
    }
  
    if (!/^[a-z0-9_]*$/.test(login)) {
      error = 3;
      alert('Not a valid player login');
      return error;
    }
  
    return error;
  }
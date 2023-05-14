// TODO: Add an anti-spam measure
//       Investigate about Honey Pots

// TODO 2: Add and autoloading AJAX request to get data for Zones
// and World
//
// document.addEventListener('DOMContentLoaded', function() {
//     ADD FUNCTION HERE ! ! ! !
// }, false);



$(document).ready(function() {
    var url = `/tmrank/shells/ajax/${pageFileName}.php`;

    $('#loginForm').submit(function(event) {
        event.preventDefault(); 

        var login = $('#login').val();

        if(!validateLogin(login)){
            submitForm(url, login, extraOptions);
        }
    });
});



// Get the current name of the page without the extension
const pageFileName = window.location.pathname.split('/').pop().split('.')[0]

// Add extra AJAX options for specific classes
const extraOptions = () => {
    switch (pageFileName) {
        case 'world':
            return 'contentType: application/json; charset=utf-8';

        default:
            break;
    }
}



// Original PHP function converted to Javascript
// @author 1rives
function validateLogin(login) {
    let error = 0;
  
    if (!login) {
      error = 1;
      alert('Please enter a login');
      return error;
    }
  
    if (login.length > 25 || login.length < 3) {
      error = 2;
      alert('Length is not correct');
      return error;
    }
  
    if (!/^[a-zA-Z0-9_]*$/.test(login)) {
      error = 3;
      alert('Not a valid player login');
      return error;
    }
  
    return error;
}

// General use AJAX form
// @author 1rives
function submitForm(url, login, extraOption) {
    $.ajax({
        method: "GET",
        url: url,
        data: {
            login: login
        },
        extraOption,
        success: function(response) {
            if(!response.includes('{')) {
                alert(response);
            } 
            else {

                console.log(JSON.parse(response));
            }
        },
        error:  function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR.errorThrown);
        }
    });
}
// TODO: Add an anti-spam measure
//       Investigate about Honey Pots

// Get the current name of the page without the extension
const pageFileName = window.location.pathname.split('/').pop().split('.')[0]

const url = `/tmrank/shells/ajax/${pageFileName}.php`;

const formId = '#loginForm';

const loginId = '#login';

// jQuery AJAX function call
document.addEventListener('DOMContentLoaded', function() {
    if(!pageFileName.includes('players')) 
        getGeneralTable(url);
}, false);

// jQuery AJAX function call
$(document).ready(function() {
    
    $(formId).submit(function(event) {
        event.preventDefault(); 

        var login = $(loginId).val();

        if(!validateLogin(login)){
            submitForm(url, login, extraOptions);
        }
    });
});

// AJAX request via form submit
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

// AJAX request via form submit
function getGeneralTable(url, extraOption) {
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
                alert(response);
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
            console.log(jqXHR.errorThrown);
        }
    });
}

// Add extra AJAX options for specific classes
// @author 1rives
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


$(function () {
    $('#csvCalc').submit(function( event ) {
        validate();
        event.preventDefault();
        return false;
    });
});

function validate() {
    var validated = true;
    if (!$('#csvFile').val()) {
        validated = false;
        $('#fileError').text('please upload a csv file');
    }else{
        $('#fileError').text('');
    }

    if (!$('#currencies').val()) {//EUR:1,USD:0.987,GBP:0.878 = 25
        validated = false;
        $('#listError').text('please add a list of currencies');
    }else{
        $('#listError').text('');
    }

    if (!$('#outputCurrency').val()) {
        validated = false;
        $('#outputError').text('please add an output currency');
    }else{
        $('#outputError').text('');
    }

    if (validated) {
        $('#csvCalc').submit();
    } else {

        return false;
    }
}

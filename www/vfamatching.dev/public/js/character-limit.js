/**
* This jQuery plugin displays realtime feedback by 
* displaying the amount of characters remaining
* in an input field, and coloring the display
* when the user has entered either not enough
* or too many characters
*
* DOM elements having class "character-limit" will be validated
* when their content changes. Class "character-limit-max" is 
* required and class "character-limit-min" is optional
*/
$(document).ready(function() {  
    $('.character-limit').each(function(){
        var charactersLeft = calculateCharactersLeft($(this));
        $(this).after('<small class="pull-right"><span class="current-limit">' + charactersLeft + '</span></em></small>')
        $(this).next().children('.current-limit').html(charactersLeft);
        if(charactersLeft <= 10){
            $(this).next().children('.current-limit').css('color', 'red');
        } else {
            $(this).next().children('.current-limit').css('color', 'green');
        }
        if($(this).attr('character-limit-min') != undefined){
            if($(this).val().length < $(this).attr('character-limit-min')){
                $(this).next().children('.current-limit').css('color', 'red');
            }
        }
    });
    $(".character-limit").on('change keyup paste', function() {
        var charactersLeft = calculateCharactersLeft($(this));
        $(this).next().children('.current-limit').html(charactersLeft);
        if(charactersLeft <= 10){
            $(this).next().children('.current-limit').css('color', 'red');
        } else {
            $(this).next().children('.current-limit').css('color', 'green');
        }
        if($(this).attr('character-limit-min') != undefined){
            if($(this).val().length < $(this).attr('character-limit-min')){
                $(this).next().children('.current-limit').css('color', 'red');
            }
        }
    });
    function calculateCharactersLeft(input){
        return input.attr('character-limit-max') - input.val().replace(/(\r\n|\n|\r)/gm,"1234").length;
    }
});
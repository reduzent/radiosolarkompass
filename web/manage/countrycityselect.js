window.addEvent('domready', function(){

    var selCity = $$('select[name^=cities]'),
        newCity = document.getElement('input[name=new_city]'),
        lonCoor = document.getElement('input[name=lon]'),
        latCoor = document.getElement('input[name=lat]');
    
    document.id('country').addEvent('change', function(){
        
        newCity.removeClass('visible');
        selCity.removeClass('visible');
        lonCoor.removeClass('visible');
        latCoor.removeClass('visible');
        var countryId = this.getSelected()[0].value;
        
        var citySel = $$('select[name="cities-' + countryId + '"]');
        if (citySel.getElements('option')[0].length == 1){
            newCity.addClass('visible');
            lonCoor.addClass('visible');
            latCoor.addClass('visible');
        } else {
            citySel.addClass('visible');
        }
        //.addClass('visible');
    
    });

    selCity.addEvent('change', function(){
        if (this.getSelected()[0].value == 'new'){
            newCity.addClass('visible');
            lonCoor.addClass('visible');
            latCoor.addClass('visible');
        } else {
            newCity.removeClass('visible');
            lonCoor.removeClass('visible');
            latCoor.removeClass('visible');
        }
    });
    
});

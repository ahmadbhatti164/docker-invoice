jQuery(document).ready(function(){
    jQuery('.search-box input[type="text"]').on("keyup", function(){

        /* Get input value on change */
        var inputVal = jQuery(this).val();
        var resultDropdown = jQuery(this).siblings(".result");
        if(inputVal.length){
            var searchUrl = APP_URL+'/search'
            $.ajax({
                type : 'POST',
                url : searchUrl,
                data:{'search':inputVal},
                success:function(data){
                    resultDropdown.html(data);
                }
            });

        } else{
            resultDropdown.empty();
        }
    });

    /*// Set search input value on click of result item
    jQuery(document).on("click", ".result p", function(){
        jQuery(this).parents(".search-box").find('input[type="text"]').val(jQuery(this).text());
        jQuery(this).parent(".result").empty();
    });*/
});

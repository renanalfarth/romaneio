$(function () {
    "use strict";
   
    //Enable sidebar toggle
    $(".additem").click(function () {

        var $div;

        $div = $('.itens .item').first().clone();

        $div = '<li class="item">' + $div.html() + '</li>';

        $('.itens-new').append($div);

    });
    
    $(".get-lote").click(function () {
        var id = $('#form_LOTE').val();

        $.ajax({url: "find-itens/" + id, success: function (result) {
                $(".itens").html(result);
            }});
    });
        
    
    function totalBaixas() {   
            
        $('.baixas').each(function () {
        
            var item_id = $(this).attr('item');
            var data = $(this).attr('rel');
            var dataFormat = $(this).attr('dataFormat');
            
            $.ajax({
                type: "GET",
                url: window.rooth_path + "lote/quantidade?id=" + item_id + "&data=" + data, 
                async: false,
                cache: false,
                dataType: 'html',
                
                success: function (result) {
                    if(result > 0) {
                        $('.item'+item_id+dataFormat).text(result);
                        calculaTotais();
                        calculaSaldos();
                        calculaDia();
                    } else {
                        $('.item'+item_id+dataFormat).text(0);
                        calculaTotais();
                        calculaSaldos();
                        calculaDia();    
                    }
                }
                
            });
        });
        
        
    }
    
    function totalInacabadas() {
        
        $('.inacabadas').each(function () {
            
            $.blockUI();
            
            var item_id = $(this).attr('rel');
            
            $.ajax({
                type: "GET",
                url: window.rooth_path + "lote/inacabadas?id=" + item_id, 
                async: false,
                cache: false,
                dataType: 'html',
                success: function (result) {
                    if(result > 0)
                    $('.inacabada'+item_id).text(result);
                }
            });
        });
        
    }
    
    function calculaTotais() {      
        $('.totalItem').each(function() {
            var id    = $(this).attr('rel');
            var valor = 0;
            
            $('.item'+id).each(function() {
                valor += parseInt($(this).text());
            });
            
            if(valor > 0) { 
                $(this).text(valor);
            }
                
        }); 
    }
    
    function calculaSaldos() {
        $('.saldoItem').each(function() {
            var id    = $(this).attr('rel');
            var valor = parseInt($('.qtd'+id).text());
            
            $('.item'+id).each(function() {
                valor -= parseInt($(this).text());
            });
            
            if(valor > 0)
            $(this).text(valor);
        });
    }
    
    function calculaDia() {
        $('.totalSoma').each(function() {
            
            var data = $(this).attr('data');
            
            var total = 0;
            
            $('.data'+data).each(function(){
               total += parseInt($(this).text()); 
            });
            
            $(this).text(total);
            
        });
    }
    
    totalBaixas();    
    totalInacabadas();    
            
})(window.jQuery || window.Zepto);
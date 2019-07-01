<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Pagamento</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- BOOTSTRAP -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    </head>
    <body>
   
        <div class="principal">
            <h2 style="text-align:center"> Pagamento</h2>
            <form class="pagamento">
                <div class="form-group">
                    <input name="nome" type="text" class="form-control" id="formGroupExampleInput" placeholder="Nome completo, o mesmo que está no cartão" value="Fulano Teste">
                </div>
                <div class="row form-group">
                    <div class="col">
                        <input name="card" type="text" class="form-control" placeholder="Número do cartão" value="0000000000000001">
                    </div>
                    <div class="col">
                        <input name="validade" type="text" class="form-control" placeholder="validade" value="12/2022">
                    </div>
                    <div class="col">
                        <input name="band" type="text" class="form-control" placeholder="Bandeira, exemplo VISA" value="VISA">
                    </div> 
                </div>
                <div class="row form-group">
                    <div class="col">
                        <input name="cvv" type="text" class="form-control" placeholder="CVV, código de segunçra" value="123">
                    </div>
                    <div class="col">
                        <input name="valor" type="text" class="form-control" placeholder="Valor" value="15700">
                    </div>
                    
                </div>
                <div class="row form-group submit" >
                    <button type="submit" class="btn btn-primary">Enviar</button>

                </div>
            </form>
            <div class="msg"></div>
        </div>

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>
    </body>
</html>
<style>
.principal{
    width:100%;
    height: 100%;
    padding: 1%;
    display: grid;

}
.submit{
    display: grid;
    justify-content: center;
}
.msg{
    color:red;
    text-align: center;
    font-style: italic;
    font-size: larger;
}
.msg.aprovado{
    color:green;
}

.msg.reprovado{
    color:red;
}

</style>
<script>
window.addEventListener('load', function(){
    $('.pagamento').submit(function(e){
        e.preventDefault();
        $('button').attr('disabled',true)
        var dados = {};
        var form = $(this).serializeArray()
        
        for (var i in form) {
            var f = form[i].name,
                v = form[i].value;
                dados[f] = v;
        }

        $.ajax({
            url: 'http://localhost:8000/api/pay',
            type: 'POST',
            data: dados,
            success: function(res){
                $('button').attr('disabled',false)
                $('.cadastro input').each(function(){
                    $(this).val('');
                })
                if(res.tid != undefined){
                    let str = `Compra realizada com sucesso! PEDIDO: ${res.tid}`;
                    $('.msg').removeClass('reprovado');
                    $('.msg').addClass('aprovado');
                    $('.msg').text(str)
                }else{
                    $('.msg').removeClass('aprovado');
                    $('.msg').addClass('reprovado');
                    $('.msg').text(res.msg)
                }
                console.log(res)
            },
            error:function(e){
                Snackbar.show({ text: res.message,actionTextColor: 'red',pos:'bottom-center' });
                console.log('ERROR',e)
            }
        })    
    })
})
</script>

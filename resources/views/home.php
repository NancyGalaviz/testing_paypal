<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Jekyll v3.8.5">
    <title>Test Paypal</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.3/examples/album/">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <link href="<?php echo url('css/custom.css')?>" rel="stylesheet">
</head>

<body>
    <header>
        <div class="navbar navbar-dark bg-dark shadow-sm">
            <div class="container d-flex justify-content-between">
                <a href="#" class="navbar-brand d-flex align-items-center">
                    <strong>Test Paypal</strong>
                </a>
            </div>
        </div>
    </header>

    <main role="main" id="app">
        <section class="jumbotron text-center">
            <div class="container">
                <h4 class="jumbotron-heading">Saldo disponible (vendedor)</h4>
                <p class="lead text-muted">{{ formatedAmount }}</p>
                <div class="form-row align-items-center">
                    <label for="payment" class="col-md-4">Monto a pagar</label>
                    <div class="form-group col-md-4 mb-2">
                        <input type="text" v-model="paymentAmount" class="form-control" id="payment" placeholder="10.00">
                    </div>
                    <button v-on:click="payment" class="btn btn-primary col-md-4 mb-2">Pagar</button>
                </div>
                <a href="https://www.sandbox.paypal.com/myaccount/summary" class="float-right" target="_blank">Consultar saldo cliente</a>
            </div>
        </section>
    </main>

    <footer class="text-muted">
        <div class="container">
        </div>
    </footer>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.10/dist/vue.js"></script>
    <script>const siteUrl = "<?php echo url(); ?>";</script>
    <script src="<?php echo url('js/custom.js') . '?var='.rand()?>"></script>

</body>

</html>

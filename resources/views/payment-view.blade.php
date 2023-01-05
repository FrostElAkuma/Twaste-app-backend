@php($currency=\App\Models\BusinessSetting::where(['key'=>'currency'])->first()->value)

    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>
        @yield('title')
    </title>
    <!-- SEO Meta Tags-->
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <!-- Viewport-->
    {{--<meta name="_token" content="{{csrf_token()}}">--}}
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon and Touch Icons-->
    <link rel="shortcut icon" href="favicon.ico">
    <!-- Font -->
    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/vendor.min.css">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/vendor/icon-set/style.css">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/custom.css">
    <!-- CSS Front Template -->
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/theme.minc619.css?v=1.0">

    <style>
        .stripe-button-el {
            display: none !important;
        }

        .razorpay-payment-button {
            display: none !important;
        }
    </style>
    <script
        src="{{asset('assets/admin')}}/vendor/hs-navbar-vertical-aside/hs-navbar-vertical-aside-mini-cache.js"></script>
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/toastr.css">

</head>
<!-- Body-->
<body class="toolbar-enabled">
<!-- Page Content-->
<div class="container pb-5 mb-2 mb-md-4">
    <div class="row">
        <div class="col-md-12 mb-5 pt-5">
            <center class="">
                <h1>Payment method</h1>
            </center>
        </div>
        <!--getting the order id order id (::find is a laravel function. I remember using that syntax in uni c++ for classes)-->
        <!--We get the id from the memory then using that id we get hte info from the DB-->
        @php($order=\App\Models\Order::find(session('order_id')))
        <section class="col-lg-12">
            <div class="checkout_details mt-3">
                <div class="row">

                    <!-- Originally there was an if statemnt here but I removed it for now cuz it was giving me an error-->
                        <div class="col-md-6 mb-4" style="cursor: pointer">
                            <div class="card">
                                <div class="card-body pb-0 pt-1" style="height: 70px">
                                <!--Originally this action led to the papal route, but since i won't integrate payapl for now the route leads to payment-success -->
                                    <form class="needs-validation" method="POST" id="payment-form"
                                          action="{{route('payment-success')}}">
                                        {{ csrf_field() }}
                                        <button class="btn btn-block" type="submit">
                                            <img width="100"
                                                 src="{{asset('assets/admin/img/paypal.png')}}"/>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>



                </div>
            </div>
        </section>
    </div>
</div>

<!-- JS Front -->
<!--I get error from chrome when I try to use these Js files using ngrok. But locally It is fine. Will have ot see later when I launch the app -->
<script src="{{asset('assets/admin')}}/js/custom.js"></script>
<script src="{{asset('assets/admin')}}/js/vendor.min.js"></script>
<script src="{{asset('assets/admin')}}/js/theme.min.js"></script>
<script src="{{asset('assets/admin')}}/js/sweet_alert.js"></script>
<script src="{{asset('assets/admin')}}/js/toastr.js"></script>
<script src="{{asset('assets/admin')}}/js/bootstrap.min.js"></script>




<!-- There was a Toastr method here that i delted it was for paypal-->

</body>
</html>
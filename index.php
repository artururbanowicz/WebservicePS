<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <title>Bootstrap 101 Template</title>

  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
  <div class="col-md-3">
    <?php

    define('DEBUG', false);  // Debug mode
    define('PS_SHOP_PATH', 'http://localhost/prestashop17/');    // Root path of your PrestaShop store 
    define('PS_WS_AUTH_KEY', 'MGBK2CVTGAU4NVBA76A1S91L9QI422DV');  // Auth key
    require_once('PSWebServiceLibrary.php');

    function getProductName($productId)
    {
      try {
        $webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, DEBUG);

        $opt['resource'] = 'products';
        $opt['display'] = 'full';
        $opt['filter']['id'] = $productId;

        $xml = $webService->get($opt);

        $resources = $xml->children()->children();

        $nameLanguage = $resources->xpath('name/language[@id=1]');
        $name = (string) $nameLanguage[0];

        return $name;
      } catch (PrestaShopWebserviceException $e) {
        $trace = $e->getTrace();
        if ($trace[0]['args'][0] == 404) echo 'Bad ID';
        else if ($trace[0]['args'][0] == 401) echo 'Bad auth key';
        else echo 'Other error';
      }
    }

    function getOptionName($optionId)
    {
      try {
        $webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, DEBUG);

        $opt['resource'] = 'product_option_values';
        $opt['display'] = 'full';
        $opt['filter']['id'] = $optionId;

        $xml = $webService->get($opt);

        $resources = $xml->children()->children();

        $nameLanguage = $resources->xpath('name/language[@id=1]');
        $name = (string) $nameLanguage[0];

        return $name;
      } catch (PrestaShopWebserviceException $e) {
        $trace = $e->getTrace();
        if ($trace[0]['args'][0] == 404) echo 'Bad ID';
        else if ($trace[0]['args'][0] == 401) echo 'Bad auth key';
        else echo 'Other error';
      }
    }

    function getCombinationName($combId)
    {
      try {
        $webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, DEBUG);

        $combinationName = '';
        $opt['resource'] = 'combinations';
        $opt['display'] = 'full';
        $opt['filter']['id'] = $combId;

        $xml = $webService->get($opt);
        $resources = $xml->children()->children();
        $comb = $resources->xpath('associations/product_option_values/product_option_value/id');

        foreach ($comb as &$value) {
          $name = getOptionName((int) $value);
          $combinationName .= $name .= ' ';
        }

        return $combinationName;
      } catch (PrestaShopWebserviceException $e) {
        $trace = $e->getTrace();
        if ($trace[0]['args'][0] == 404) echo 'Bad ID';
        else if ($trace[0]['args'][0] == 401) echo 'Bad auth key';
        else echo 'Other error';
      }
    }

    function getCombinationsQty()
    {
      try {
        $webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, DEBUG);

        $opt['resource'] = 'stock_availables';
        $opt['display'] = 'full';
        if (isset($_GET['id']))
          $opt['id'] = (int) $_GET['id'];

        $xml = $webService->get($opt);

        $resources = $xml->children()->children();
      } catch (PrestaShopWebserviceException $e) {
        $trace = $e->getTrace();
        if ($trace[0]['args'][0] == 404) echo 'Bad ID';
        else if ($trace[0]['args'][0] == 401) echo 'Bad auth key';
        else echo 'Other error';
      }

      echo "<h1>Lista produktów</h1>";

      echo '<table class="table table-bordered">';
      if (isset($resources)) {
        echo '<thead><tr><th>Nazwa</th><th>Ilosc</th></tr></thead>';

        foreach ($resources as $resource) {
          $productId = (int) $resource->id_product;
          $quantity = $resource->quantity;
          $combId = (int) $resource->id_product_attribute;

          echo '<tbody><tr><td>' . getProductName($productId) . ' ' . getCombinationName($combId) . '</td><td>' .  $quantity .  '</td></tr><tbody>';
        }
      }
      echo '</table>';
    }

    getCombinationsQty();

    ?>
  </div>

  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous"></script>
  <!-- Include all compiled plugins (below), or include individual files as needed -->
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
</body>

</html>
<?php

// 1. Used when multiple altcoin shortcodes on the same page
$altcoin_shortcode_cache = null;

// 2. Currency names and codes (does not work with PHP < 7!)
define('ALTCOIN_CURRENCIES', array(
  'XXBTZEUR' => 'bitcoin',
  'BCHEUR' => 'bitcoin cash',
  'XETHZEUR' => 'ethereum',
  'XETCZEUR' => 'ethereum classic',
  'XLTCZEUR' => 'litecoin',
  'DASHEUR' => 'digital cash',
  'XXRPZEUR' => 'ripple',
  'XREPZEUR' => 'augur',
  'XXMRZEUR' => 'monero',
  'XZECZEUR' => 'zcash'
));

define('ALTCOIN_DEFAULT_CURRENCY_NAME', 'bitcoin');
define('ALTCOIN_DEFAULT_CURRENCY_CODE', 'XXBTZEUR');
define('ALTCOIN_CACHE_TIME', 300);





/**
* Fetches the altcoin tickers from kraken.com.<br/>
* Returns cache if already fetched.<br/>
* Function called with ajax, will print json and die.
*
*/
function altcoin_get_price() {
  // 1. Return cache if it exists
  $cached = get_transient('altcoin_prices');
  if ($cached !== false) { wp_send_json_success($cached->data); }

  // 2. cURL request to the kraken api.
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.kraken.com/0/public/Ticker?pair=".join(',', array_keys(ALTCOIN_CURRENCIES)),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 20,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1
  ));

  $response = curl_exec($curl);
  $curl_err = curl_error($curl);
  curl_close($curl);

  // 3. Returns an error if cURL failed
  if ($curl_err) { wp_send_json_error($curl_err); }


  // 4. Parse response as JSON { error:[], result:{ } }
  $response = json_decode($response);

  // 5. Returns an error if response is not valid, if there is an api error or no result
  if ($response === null || (isset($response->error) && count($response->error) > 0) || !isset($response->result)) {
    wp_send_json_error(null); // Do not leak info
  }

  // 6. Transform response
  $data = array();

  foreach ($response->result as $code=>$v) {
    $data[$code] = array(
      'last'=>doubleval($v->c[0]),
      'low'=>doubleval($v->l[1]),
      'high'=>doubleval($v->h[1]),
      'volume'=>intval($v->v[1])
    );
  }

  // 7. Set cache
  set_transient('altcoin_prices', array('data' => $data, 'tm' => time()), ALTCOIN_CACHE_TIME);

  // 8. Returns response
  wp_send_json_success($data);
}

// 9. Register AJAX actions
add_action('wp_ajax_get_altcoin_price', 'altcoin_get_price');
add_action('wp_ajax_nopriv_get_altcoin_price', 'altcoin_get_price');





/**
* Altcoin price shortcode.<br />
* Cached: will output the same thing if called multiple times on the same page.
*
* @return String The shortcodes' HTML
*/
function altcoin_shortcode() {
  global $altcoin_shortcode_cache;

  // 1. Returns shortcode cache if it exists
  if ($altcoin_shortcode_cache !== null) {
    return $altcoin_shortcode_cache;
  }

  // 2. Check prices cache
  $cached = get_transient('altcoin_prices');
  // 2.1 If in cache, notify freshness to javascript (because of browser caching or plugins like WP SuperCache)
  $was_cached = null;

  // 3. Mock if cache was not found
  if ($cached === false || time() - $cached['tm'] > ALTCOIN_CACHE_TIME) {
    $cached = array();

    foreach (ALTCOIN_CURRENCIES as $code=>$name) {
      $cached[$code] = array(
        "last" => 0,
        "high" => 0,
        "low" => 0,
        "volume" => 0
      );
    }

    // 3.1 Notify JS it was not in cache
    $was_cached = 'false';
  } else {
    $was_cached = $cached['tm'];
    $cached = $cached['data'];
  }

  $options = '';

  // 4. Create the HTML options, prices are in data- attributes
  foreach (ALTCOIN_CURRENCIES as $code=>$name) {
    $options .= '<option value="'.$code.'"';

    if (isset($cached[$code])) {
      foreach ($cached[$code] as $k=>$v) {
        $options .= ' data-'.$k.'="'.$v.'"';
      }
    }

    $options .= '>'.$name.'</option>'.PHP_EOL;
  }

  // 5. Elements
  $title = PHP_EOL.'<h2>'.ALTCOIN_DEFAULT_CURRENCY_NAME.' price</h2>'.PHP_EOL;

  // 5.1 Dropdown
  $label = '<label>Cryptocurrency list:';
  $select = '<select disabled name="altcoin--select">'.$options.'</select>';
  $control = '<div class="altcoin__control">'.$label.$select.'</label></div>';

  // 5.2 Actual values
  $default_info = $cached[ALTCOIN_DEFAULT_CURRENCY_CODE];
  $last_div = '<li class="altcoin__last"><div class="altcoin--key">Last:</div><div class="altcoin--value">'.$default_info['last'].'&nbsp;€</div></li>';
  $low_div = '<li class="altcoin__low"><div class="altcoin--key">Low:</div><div class="altcoin--value">'.$default_info['low'].'&nbsp;€</div></li>';
  $high_div = '<li class="altcoin__high"><div class="altcoin--key">High:</div><div class="altcoin--value">'.$default_info['high'].'&nbsp;€</div></li>';
  $volume_div = '<li class="altcoin__volume"><div class="altcoin--key">Volume:</div><div class="altcoin--value">'.$default_info['volume'].'</div></li>';

  // 5.3 Aggregation of values
  $values_div = '<ul class="altcoin__values">'.PHP_EOL.
  $last_div.PHP_EOL.
  $low_div.PHP_EOL.
  $high_div.PHP_EOL.
  $volume_div.PHP_EOL.
  '</ul>';

  // 5.4. Final aggregation
  $altcoin_shortcode_cache = PHP_EOL.
  '<section class="altcoin" data-cached="'.$was_cached.'" data-expire="'.ALTCOIN_CACHE_TIME.'">'.PHP_EOL.
  $title.
  $values_div.PHP_EOL.
  $control.PHP_EOL.
  '</section>'.PHP_EOL;

  return $altcoin_shortcode_cache;
}

add_shortcode('altcoin_price', 'altcoin_shortcode');

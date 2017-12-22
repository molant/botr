'use strict';

const UtilComponent = require('eazyfront').UtilComponent;
const retry_me = require('js/retry_me');

/**
 * Fetches Altcoin data from server.
 * Emits 'data' events with the result, will retry in case of error.
 *
 * @public
 * @class
 */
class AltcoinFetcher extends UtilComponent {

  constructor() { super(); }

  /**
   * Actually fetches data.
   *
   * @public
   * @param
   */
  fetch() {
    // 1. Prepare request body
    const data_body = new URLSearchParams();
    data_body.set('action', 'get_altcoin_price');

    // 2. Prepare request options
    const options = {
      credentials: 'include',
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'
      },
      body: data_body
    };

    // 3. Prepare action to retry in case of error
    const action = () => {
      return fetch('wp-admin/admin-ajax.php', options)
      .then((response) => {
        // 3.1 If error -> reject
        if (!response.ok) { return Promise.reject(); }

        return response.json();
      }).then((data) => {
        // 3.2 If error -> reject
        if (!data.success) { return Promise.reject(); }

        this.emit('data', data.data);
      });
    };

    // 4. Send request and retry in case of error
    return retry_me(action, (err, nb) => { console.error('Ticker fetch failed: trial '+nb); });
  }

}

module.exports = AltcoinFetcher;

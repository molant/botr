'use strict';

const Component = require('eazyfront').Component;

const AltcoinSelect = require('./AltcoinSelect');
const AltcoinFetcher = require('./AltcoinFetcher');
const AltcoinValues = require('./AltcoinValues');

require('./Altcoin.scss');

const TITLE_SUFFIX = ' PRICE';

/**
* Main Altcoin Component
*
* @class
* @public
*/
class Altcoin extends Component {

  constructor(arg, fetcher = new AltcoinFetcher()) {
    super(arg);

    this.fetcher = fetcher;

    // 1. Link to DOM
    this.values = new AltcoinValues(this.find('.altcoin__values'));
    this.select = new AltcoinSelect(this.find('select'));
    this.$title = this.find('h2');

    // 2. Bind events
    this.select.from(this.fetcher, 'data');
    this.from(this.select, 'data');
    this.values.from(this, 'data');

    // 3. Checked if cached values are still relevant, fetch otherwise
    const cached = (this.$wrapper.data('cached') !== 'false' && this.$wrapper.data('cached') !== false);
    const cache_time = cached ? parseInt(this.$wrapper.data('cached')) : 0;
    const max_cache_time = parseInt(this.$wrapper.data('expire')) || 0;

    if (!cached || Math.floor(Date.now() / 1000) - cache_time > max_cache_time) { this.fetcher.fetch(); }
  }

  from(source, event_name) {
    if (event_name !== 'data' && event_name !== '*') { return; }

    source.on('data', (pair, data) => {
      this.$title.text(pair.name+TITLE_SUFFIX);

      this.emit('data', data);
    });
  }

}

module.exports = Altcoin;

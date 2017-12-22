'use strict';

const Component = require('eazyfront').Component;

const cur_formatter = new Intl.NumberFormat({}, { style: 'currency', currency: 'EUR' });
const vol_formatter = new Intl.NumberFormat();

/**
* Displays the values in localized format.<br/>
* Consumes data event.
*
* @public
* @class
*/
class AltcoinValues extends Component {

  constructor(arg) {
    super(arg);

    // Find the DOM elements that will display the values
    this.last   = this.find('.altcoin__last .altcoin--value');
    this.low    = this.find('.altcoin__low .altcoin--value');
    this.high   = this.find('.altcoin__high .altcoin--value');
    this.volume = this.find('.altcoin__volume .altcoin--value');
  }

  /**
   * Actually displays data.
   *
   * @param {Object} data
   * @param {Number} data.last - Price of the last transaction
   * @param {Number} data.low - Lowest price in the last 24h
   * @param {Number} data.high - Highest price in the last 24h
   * @param {Number} data.volume - VOlume in the last 24h
   * @private
   * @return {void}
   */
  _display(data) {
    this.last.text(cur_formatter.format(data.last));
    this.low.text(cur_formatter.format(data.low));
    this.high.text(cur_formatter.format(data.high));
    this.volume.text(vol_formatter.format(data.volume));
  }

  /**
  * Binds events.<br/>
  * Data format:<br/>
  * <pre>
  * {
  *  last<Number>,
  *  low<Number>,
  *  high<Number>,
  *  volume<Number>
  * }
  * </pre>
  *
  * @param {EventEmitter} source
  * @param {String} event_name
  * @public
  * @return {void}
  */
  from(source, event_name) {
    if (!['data', '*'].includes(event_name)) { return; }

    source.on('data', (data) => { return this._display(data);});
  }

}

module.exports = AltcoinValues;

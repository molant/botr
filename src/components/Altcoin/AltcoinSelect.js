'use strict';

const Component = require('eazyfront').Component;
const u = require('umbrellajs').u;

/**
* The currency selector.<br/>
* Responsible for storing currency data.
*
* @class
* @public
*/
class AltcoinSelect extends Component {

  constructor(arg) {
    super(arg);

    // 1. Init data from DOM
    this.data = this._read_dom_data();

    // 2. Listens to select events
    this.$wrapper.on('change', () => { this._send(); });

    // 3. Send initial state
    this._send();

    // 4. Enable select
    this.$wrapper.first().disabled = false;
  }





  /**
  * Binds data events.<br/>
  * Data format:
  * <pre>
  * {
  *   pairname<String>: {
  *     last<Number>,
  *     high<Number>,
  *     low<Number>,
  *     volume<Number>
  *   },
  *   ...
  * }
  * </pre>
  *
  * @param {EventEmitter} source
  * @param {String} event_name
  * @public
  */
  from(source, event_name) {
    if (!['data', '*'].includes(event_name)) { return; }

    source.on('data', (received) => { return this._receive(received); });
  }





  /**
  * Reads initial data from the DOM (in <option/>)
  *
  * @private
  * @return {Object}
  */
  _read_dom_data() {
    const _data = {};

    this.$wrapper.find('option').each((option) => {
      const code = option.value;
      const $option = u(option);

      _data[code] = {
        name: $option.text(),
        values: {
          last: $option.data('last'),
          high: $option.data('high'),
          low: $option.data('low'),
          volume: $option.data('volume')
        }
      };

    });

    return _data;
  }





  /**
  * Sends data about the selected currency.
  *
  * @return {void}
  * @private
  */
  _send() {
    const code = this.$wrapper.first().value;
    this.emit('data', { code: code, name: this.data[code].name }, Object.assign({}, this.data[code].values));
  }





  /**
  * Reads data received by a 'data' event.
  *
  * @param {Object} received
  * @return {void}
  * @private
  */
  _receive(received) {
    for (let code of Object.keys(received)) {
      this.data[code].values.last   = received[code].last;
      this.data[code].values.high   = received[code].high;
      this.data[code].values.low    = received[code].low;
      this.data[code].values.volume = received[code].volume;
    }

    this._send();
  }

}

module.exports = AltcoinSelect;

'use strict';

const FADEOUT_TEMP = 800; // Fadeout duration in ms
const HOLD_TEMP = 7000; // Time displayed in ms

const Component = require('eazyfront').Component;

// Load HTML Template
const _TPL = require('./Toast.html');

require('./Toast.scss');

/**
* Actual Toast
*
* @public
* @class
*/
class Toast extends Component {

  constructor(title, message) {
    super(_TPL);

    // Find DOM Elements
    this.find('.toaster--title').text(title);
    this.find('.toaster--message').text(message);
    this.$inner = this.find('.toaster--inner');

    // Bind events
    this.from(this, 'fadeout');

    // Set initial state
    this.state = 'hidden';
  }

  /**
  * Fades out then emit the 'destroy' event
  *
  * @private
  */
  _hide() {
    if (this.state !== 'visible') { return; }
    this.state = 'hidden';

    this.$inner.removeClass('toaster--fadein');
    this.$inner.addClass('toaster--fadeout');
    window.setTimeout(() => { this.$wrapper.remove(); this.emit('destroy'); }, FADEOUT_TEMP);
  }

  /**
  * Fades in, wait HOLD_TEMP ms then emits fadeout
  *
  * @private
  */
  _shoe() {
    if (this.state !== 'hidden') { return; }

    this.$inner.addClass('toaster--fadein');
    this.state = 'visible';
    window.setTimeout(() => { this.emit('fadeout'); }, HOLD_TEMP);
  }

  /**
  * Binds 'fadeout' and 'fadein' events
  *
  * @param {EventEmitter} source - Event source
  * @param {String} event_name - Name of the event
  * @public
  */
  from(source, event_name) {
    if (event_name === 'fadeout' || event_name === '*') {
      source.on('fadeout', () => { this._hide(); });

    } else if (event_name === 'fadein' || event_name === '*') {
      source.on('fadein', () => { this._show(); });

    }
  }

}

module.exports = Toast;

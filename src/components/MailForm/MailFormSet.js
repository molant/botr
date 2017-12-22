'use strict';

const UtilComponent = require('eazyfront').UtilComponent;

/**
* Handles multiple instances of MailForm and assure state coherence.
* Emits 'success' and 'error' events
*
* @public
* @class
*/
class MailFormSet extends UtilComponent {

  constructor(forms) {
    super();

    this._forms = forms;

    // Broadcast success event
    const success_handler = (email) => { this.emit('success', email); };

    for (let form of forms) {
      // Capture success events
      form.from(this, 'success');

      // Broadcast success events
      form.on('success', success_handler);

      // Let error events through
      form.on('error', (err) => { this.emit('error', err); }); // BUG .through should be used but doesnt work with 'error' events for some reason
    }
  }

}

module.exports = MailFormSet;

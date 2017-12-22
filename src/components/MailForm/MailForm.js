'use strict';

// TODO: Check mailform=success|error in querystring

const Component = require('eazyfront').Component;
require('./MailForm.scss');

/**
* Handles a MailForm
*
* @public
* @class
*/
class MailForm extends Component {

  /**
  *
  * @param {mixed} arg - Any arg that can be used with Eazyfront.Component
  * @param {String} endpoint - The AJAX endpoint to call
  */
  constructor(arg, endpoint) {
    super(arg);

    this.endpoint = endpoint;

    // 1. Find DOM Elements
    this.$input = this.find('input[type="email"]').first();
    this.$submit_wrapper = this.find('.mail-form--submit');
    this.$submit = this.find('input[type="submit"]');

    // 2. Bind submit event
    this.$wrapper.on('submit', (e) => {
      e.preventDefault();
      this._submit();
      return false;
    });

    // 3. Enables the form by default
    this.disabled = false;
  }

  /**
  * Handles the submit event
  *
  * @private
  */
  _submit() {
    // 1. Find the email address from input
    const email = this.$input.value;

    // 2. Check length or die
    if(email.trim().length < 5) { return; }

    // 3. Disable the form to avoid multiple sends
    this.disabled = true;

    // 4. Show loader
    this.$submit_wrapper.addClass('loading');

    // 5.1 Prepare request body
    const data = new URLSearchParams();
    data.set('action', 'mailform');
    data.set('email', email);

    // 5.2 Prepare request options
    const options = {
      credentials: 'include',
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'
      },
      body: data
    };

    console.log('Sending form data');
    console.log();

    // 5.3 Send request to endpoint
    fetch(this.endpoint, options)
    .then((response) => {
      // Something went wrong -> reject
      if (!response.ok) { return Promise.reject(response); }

      // Parse response as JSON
      return response.json();
    }).then((raw) => {
      // Something went wrong -> reject
      if (!raw.success) { return Promise.reject(raw); }

      // Everything ok, emit success event and hide loader
      this.emit('success', email);
      window.setTimeout(() => { this.$submit_wrapper.removeClass('loading'); } , 320);
    }).catch((err) => {
      // Bubble error
      if (err && err.statusText) {
        this.emit('error', err.statusText);
      } else {
        this.emit('error', err);
      }

      window.setTimeout(() => { this.$submit_wrapper.removeClass('loading'); } , 320);
      this.disabled = false;
    });
  }

  /**
  * Checks if the form is disabled
  *
  * @public
  * @return true|false
  */
  get disabled() { return this.$submit.is(':disabled') || this.$submit.is(':disabled'); }

  /**
  * Sets the disabled state of the form
  *
  * @public
  * @param {boolean} value - True to disable, false to enable
  */
  set disabled(value) {
    this.$input.disabled = value;
    this.$submit.first().disabled = value;
  }

  /**
  * Binds events
  *
  * @param {EventEmitter} source - The source
  * @param {String} event_name - Name of the event
  * @public
  */
  from(source, event_name) {
    if (event_name !== '*' && event_name !== 'success') { return; }

    source.on('success', (email) => {
      this.$input.value = email;
      this.disabled = true;
    });
  }

}

module.exports = MailForm;

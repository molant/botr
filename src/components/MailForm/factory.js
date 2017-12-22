'use strict';

const MailForm = require('./MailForm');
const MailFormSet = require('./MailFormSet');

/**
 * Creates a MailFormSet from the current document.
 */
module.exports = () => {
  // Find forms
  const dom_forms = document.getElementsByClassName('mail-form');
  const forms = [];

  for (let i = 0; i < dom_forms.length; i++) {
    // Dynamiccaly add an id
    dom_forms[i].id = 'mailform--'+i;

    // Create the MailForm instance
    forms.push(new MailForm('#'+dom_forms[i].id, dom_forms[i].dataset.action));
  }

  // Returns the MailFormSet
  return new MailFormSet(forms);
};

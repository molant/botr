'use strict';

require('babel-polyfill');
require('scss/index.scss');

const u = require('umbrellajs').u;
const mailform_set_factory = require('components/MailForm').factory;
const Toaster = require('components/Toaster');
const Altcoin = require('components/Altcoin');

function ready(fn) {
  if (document.attachEvent ? document.readyState === 'complete' : document.readyState !== 'loading') {
    return fn();
  } else {
    document.addEventListener('DOMContentLoader', fn);
  }
}

ready(() => {


  // 1. Create elements
  const toaster = new Toaster(u('body'));

  const dom_altcoins = u('.altcoin');
  const altcoins = [];
  dom_altcoins.each((el) => {
    const id = 'altcoin_'+Math.floor(1+Math.random()*1000);
    el.id = id;
    altcoins.push(new Altcoin('#'+id));
  });

  const mail_form_set = mailform_set_factory();

  /*
  const dom_forms = u('.mail-form');
  const mail_forms = [];

  dom_forms.each((form) => { mail_forms.push(new MailForm('#'+form.id, 'endpoint?')); }); // XXX
  const mail_form_set = new MailFormSet(mail_forms);
  */

  // 2. Binding events
  toaster.from(mail_form_set, 'success', 'Hi! :)', 'We\'ll soon send an email at $1');
  //toaster.from(mail_form_set, 'error', 'Error', '$1');
  mail_form_set.on('error', (e) => { console.log('Error bubbled: '); console.log(e); });
});

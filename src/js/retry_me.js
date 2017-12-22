'use strict';

const promise_retry = require('promise-retry');

const CONFIG = {
  retries: 10,
  factor: 1.5,
  minTimeout: 500,
  maxTimeout: 120000,
  randomize: true,
};

/**
 * Retries a promise
 *
 * @param {Function} action - The action to retry
 * @param {Function} log - A function to log errors log(err:String, trial_num:Number)
 * @return {Promise<mixed>}
 * @public
 */
module.exports = (action, log) => {
  return promise_retry(CONFIG, (retry, number) => {

    return action()
    .catch((err) => {
      log(err, number);
      retry();
    });

  });
};

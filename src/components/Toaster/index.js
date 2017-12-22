'use strict';

const UtilComponent = require('eazyfront').UtilComponent;
const Toast = require('./Toast');

/**
 * Displays toasts.
 * Only one toast can be displayed at the same time.
 *
 * @public
 * @class
 */
class Toaster extends UtilComponent {

  constructor($target) {
    super();

    this.$target = $target;
  }

  /**
   * Binds events.<br/>
   * The message and title of the toast can contain variables:<br/>
   * <pre>
   * const toaster = new Toaster();
   * toaster.from(emitter, 'myeventname', 'Hello $1', 'My name is $2');
   * emitter.emit('myeventname', 'World', 'Romain');
   * </pre>
   * The title of toast will be 'Hello World' and it's content will be 'My name is Romain'.<br/>
   * There is an arbitrary limit of 8 variables.
   *
   *
   * @param {EventEmitter} source - Event source
   * @param {String} event_name - Name of the event
   * @param {String} title - Title of the toast to display
   * @param {String} message - Message of the toast to display
   */
  from(source, event_name, title, message) {

    source.on(event_name, (...args) => {
      let n_title = title;
      let n_message = message;

      // Parse Title and Message for variables
      for (let i = 0; i < 8; i++) {
        n_message = n_message.replace(new RegExp('\\$'+i, 'g'), args[i-1]);
        n_title = n_title.replace(new RegExp('\\$'+i, 'g'), args[i-1]);
      }

      // Create Toast
      let toast = new Toast(n_title, n_message);
      toast.from(this, 'fadein');

      // Help GC when toast has ended
      toast.on('destroy', () => {
        toast = null;
      });

      // Add toast to DOM and display
      this.$target.append(toast.get_wrapper());
      this.emit('fadein');
    });

  }
}

module.exports = Toaster;

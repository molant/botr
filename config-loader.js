'use strict';

const path = require('path');
const fs = require('fs');

// 1. package_info = require('package.json');
module.exports = (package_info) => {
  const theme = {};

  // 2. Loading mandatory theme information

  // 2.1 Name
  theme.name = package_info.wp_boilerplate.name;

  if (typeof theme.name !== 'string') {
    console.error('No theme name is defined, please check wp_boilerplate.name in package.json');
    process.exit(1);
  }

  theme.name = theme.name.trim();
  theme.dir= theme.name.toLowerCase().replace(/[^a-z ]/g, '').replace(/[ ]+/g, '-');

  // 2.2. Output directory
  let wp_location = package_info.wp_boilerplate.wp_location;

  // 2.2.1 Dies if not found
  if (typeof wp_location !== 'string') {
    console.error('Unable to find wp_boilerplate.wp_location in package.json.');
    process.exit(1);
  }

  wp_location = wp_location.trim();
  if (!wp_location.endsWith('/')) { wp_location += '/'; } // Avoids the use of an unnecessary regex
  theme.wp_dir = wp_location.endsWith('wp-content/themes/') ? wp_location : path.join(wp_location, 'wp-content/themes');
  theme.output_dir =  path.join(theme.wp_dir, theme.dir);

  // 2.2.2 Make sure the theme directory exists, dies if not
  if (!fs.existsSync(theme.wp_dir)) {
    console.error('Specified Wordpress location unreachable:', theme.wp_dir);
    console.error('Please make sure this is an absolute path');
    process.exit(1);
  }

  // 2.3. Version
  theme.version = package_info.version.trim();

  // 2.4. Author
  theme.author = {};
  theme.author_name = package_info.author || '';
  theme.author_email = null;
  theme.author_uri = null;

  theme.author_name = theme.author_name.replace(/\t/g, '');


  let email_regex_result = theme.author_name.toLowerCase().match(/<[ \t]*([a-z.-_]+@[a-z.-_]+\.[a-z]+)[ \t]*>/);
  let uri_regex_result = theme.author_name.toLowerCase().match(/\([ ]*((http(s)?(:\/\/))?(www\.)?[a-z0-9-_.]+(.[a-z0-9]{2,})([-a-z0-9:%_+.~#?&//=]*))[ ]*\)/);


  if (email_regex_result !== null) {
    theme.author_email = email_regex_result[1];
    theme.author_name = theme.author_name.replace(/<[ ]*([a-z.-_]+@[a-z.-_]+\.[a-z]+)[ ]*>/gi, '');
  }

  if (uri_regex_result !== null) {
    theme.author_uri = uri_regex_result[1];
    theme.author_name = theme.author_name.replace(/\([ ]*((http(s)?(:\/\/))?(www\.)?[a-z0-9-_.]+(.[a-z0-9]{2,})([-a-z0-9:%_+.~#?&//=]*))[ ]*\)/gi, '');
  }

  theme.author_name = theme.author_name.trim().replace(/[ ]+/, ' ');

  // 2.5. Description
  theme.description = typeof package_info.description === 'string' ? package_info.description.trim() : '';
  theme.description = theme.description.length > 0 ? theme.description : null;

  // 2.6. License
  theme.license = typeof package_info.license === 'string' ? package_info.license.trim() : '';
  theme.license = theme.license.length > 0 ? theme.license : null;

  // 2.7. License URL
  theme.license_url = typeof package_info.license_url === 'string' ? package_info.license_url.trim() : '';
  theme.license_url = theme.license_url.length > 0 ? theme.license_url : null;

  // 2.8. Tags
  theme.tags = Array.isArray(package_info.keywords) ? package_info.keywords.join(' ').toLowerCase().replace(/[ ]+/, ' ') : null;

  // 2.8. Textdomain
  theme.textdomain = typeof package_info.wp_boilerplate.textdomain === 'string' ? package_info.wp_boilerplate.textdomain  : '';
  theme.textdomain = theme.textdomain.length > 0 ? theme.textdomain : null;

  // 2.9. Clean any \r\n

  for (let k of Object.keys(theme)) {
    theme[k] = typeof theme[k] === 'string' ? theme[k].replace(/[\r\n]/g, ' ').replace(/[ \t]+/g, ' ') : null;
  }

  return theme;
};

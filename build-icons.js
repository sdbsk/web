const fs = require('fs');

fs.writeFileSync('assets/admin/scripts/icon-names.js', `export default ${JSON.stringify(
    fs.readdirSync('vendor/google/material-symbols/svg/300/outlined/')
        .filter(file => !file.match(/-fill\.svg$/))
        .map(file => file.match(/^([^.]+)\./)[1]), null, 2)};`);

const fs = require('fs');

fs.writeFileSync('assets/blocks/scripts/icon-names.js', `export default ${JSON.stringify(
    fs.readdirSync('assets/google-material-symbols/')
        .filter(file => !file.match(/-fill\.svg$/))
        .map(file => file.match(/^([^.]+)\./)[1]), null, 2)};\n`);

'use strict';

const express = require('express');

// Constants
const PORT = 80;
const HOST = '0.0.0.0';

// App
const app = express();
app.get('/', (req, res) => {
    res.send('Hello World');
});

// Start server
app.listen(PORT, HOST, () => {
    console.log(`TX-Builder NodeJS Service Running on http://${HOST}:${PORT}`);
});

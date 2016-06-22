var express = require('express');
var app = express();

app.get('/', function (req, res) {
	res.send('Hello Express!');
});

app.get('/about', function (req, res) {
	res.send('About us');
});

app.listen(3311);
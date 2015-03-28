var connect = require('connect');
var path = require('path');
var serveStatic = require('serve-static');

var static_dir = path.normalize(
    path.join(__dirname, '..')
);

var app = connect();

app.use(
    serveStatic(static_dir)
);
app.listen(8000);

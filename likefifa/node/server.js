var sslCert = null;
var sslKey = null;

process.argv.forEach(function (val, index, array) {
    if(index < 2) {
        return true;
    }
    if(/--key/.test(val)) {
        sslKey = val.split('=')[1];
    } if(/--cert/.test(val)) {
        sslCert = val.split('=')[1];
    }
});

var https = require("https"),
    fs = require("fs"),
    express = require('express'),
    app = express(),
    httpapp = express(),
    server = https.createServer(
        {
            key: fs.readFileSync(sslKey),
            cert: fs.readFileSync(sslCert),
            requestCert: true,
            rejectUnauthorized: false
        }, app),
    http = require('http').createServer(httpapp),
    ioLibrary = require("socket.io"),
    io = ioLibrary.listen(http),
    ioSsl = ioLibrary.listen(server);

server.listen(3000);
http.listen(3001);

// Сокеты пользователей
var users = {};
// Связь сокетов и юзеров
var sockets = {};

function connection(connect, socket) {
    // ID текущего юзера
    var user_id = null;

    // Подключение пользователя
    socket.json.send({'event': 'connected'});

    // Отключение пользователя
    socket.on('disconnect', function () {
        // Убиваем сессии пользователя
        var user_id = sockets[socket.id];
        delete sockets[socket.id];
        delete users[user_id];
    });

    // Клиент сообщил id текущего юзера
    socket.on('user_id', function (data) {
        user_id = data.user_id;
        users[user_id] = socket;
        sockets[socket.id] = user_id;
    });

    // Новое сообщение
    socket.on('message', function (data) {
        io.sockets.json.send({
            event: data.event,
            data: data.data
        });

        ioSsl.sockets.json.send({
            event: data.event,
            data: data.data
        });
    });
}

io.sockets.on('connection', function (socket) {
    connection(io, socket);
});

ioSsl.sockets.on('connection', function (socket) {
    connection(ioSsl, socket);
});
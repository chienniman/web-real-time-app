<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Websocket chatroom</title>
    <style type="text/css">
        *{
            list-style:none;
        }
        ::-webkit-scrollbar {
        display: none;
        }
        body{
            height:100vh;
            overflow:hidden;
            background: #f1eece;
            display:flex;
            justify-content: center;
            align-items:center;
        }
        #shell{
            /* https://www.vectorpng.com/png-r9o3q4/ */
            background-image:url("{{ asset('phone.png') }}");
            background-size:100% 100%;
            width:400px;
            height:600px;
            position:relative;
        }
        #screen{
            position: absolute;
            width: 260px;
            height: 75.5%;
            left: 50%;
            bottom: 95px;
            transform: translateX(-50%);
            border-radius: 10px 10px 0 0;
            background: ghostwhite;
        }
        #chatList{
            padding: 15px 20px 0 20px;
            height:84.5%;
            overflow:hidden scroll;
        }
        #typing{
            display:flex;
            justify-content:center;
        }
        #message{
            flex:1;
        }
        .fromMe,.fromGuest{
            position:relative;
            margin-top:15px;
            display:flex;
            border-radius:20px;
        }
        .fromMe{
            flex-direction:row-reverse;
            text-align:left;
            background: #00D025;
            color:white;
        }
        .fromGuest{
            text-align: right;
            color: black;
            background: #dfdcdc;
        }
        .timeStamp{
            white-space: nowrap;
            color: gray;
            position: absolute;
            font-size: 8px;
            left: 50%;
            top: -15px;
            transform:translateX(-50%);
        }
        .text{
            font-size:8px;
            width:100%;
            margin:0px;
            word-wrap: anywhere;
        }
        .fromMe .text{
            padding-left:10px;
        }
        .fromGuest .text{ 
            padding-right:10px;
        }
        .system{
            margin:20px;
        }
        .system .text{
            color:gray;
            text-align:center;
        }
        .avatar {
            vertical-align: middle;
            width: 30px;
            height: 30px;
            border-radius: 50%;
        }

    </style>
</head>
<body>
    <div id="shell">
        <div id="screen">
            <ul id="chatList"></ul>
            <div id="typing">
                <input type="text" id="message" placeholder="請輸入訊息" autofocus>
                <input type="submit" value="送出" onclick="send('message'); ">
            </div>
        </div>
    </div>
    <script>
        const name = prompt("請輸入名稱") || 'Anonymous';
        const wsServer = 'ws://127.0.0.1:8005';
        const websocket = new WebSocket(wsServer);
        const uuid=getUuid();

        // https://juejin.cn/post/7066608015784280072
        function getUuid() {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
                var r = (Math.random() * 16) | 0,
                    v = c == 'x' ? r : (r & 0x3) | 0x8;
                return v.toString(16);
                 });
        };

        websocket.onopen = function (event) {
            console.log('WebSocket Connection ok!');
            send('login');
        };

        websocket.onclose = function (event) {
            console.log('WebSocket Close');
        };

        websocket.onmessage = function (event) {
            let parsed = JSON.parse(event.data);
            appendMessage(parsed);

            let chatList = document.getElementById('chatList');
            chatList.scrollTop = chatList.scrollHeight;
        };

        websocket.onerror = function (event, error) {
            console.error(event.data);
        };

        // https://stackoverflow.com/questions/8888491/how-do-you-display-javascript-datetime-in-12-hour-am-pm-format
        function formatAMPM(date) {
            var hours = date.getHours();
            var minutes = date.getMinutes();
            var ampm = hours >= 12 ? 'pm' : 'am';
            hours = hours % 12;
            hours = hours ? hours : 12;
            minutes = minutes < 10 ? '0'+minutes : minutes;
            var strTime = hours + ':' + minutes + ' ' + ampm;
            return strTime;
        };

        const send = function (type){
            const packet={};
            switch (type) {
                case 'message':
                    let message = document.getElementById("message").value;
                    if(message){
                        packet.type="message";
                        packet.message=message;
                        packet.id=uuid;
                        packet.timeStamp=formatAMPM(new Date);
                        packet.name=name;
                        packeted=JSON.stringify(packet);
                        websocket.send(packeted);
                        document.getElementById("message").value='';
                    };
                    break;
                case 'login':
                    packet.type="login";
                    packet.name=name;
                    packet.message=`${name} join chat!`;
                    packet.timeStamp=formatAMPM(new Date);
                    packeted=JSON.stringify(packet);
                    websocket.send(packeted);
                    break;
            }
        };

        document.onkeypress = function (e) {
            if (e.keyCode == 13 && document.getElementById("message").value) send('message');
        };

        const appendMessage = function (data){
            let chatList = document.getElementById('chatList');
            let className='';

            switch (data.type) {
                case 'message':
                    let from = uuid === data.id ? "fromMe" : "fromGuest";
                    className=from;
                    chatList.innerHTML+=`
                        <li class='${className}'>
                            <img src="{{ asset('${from}.png') }}"alt="Avatar" class="avatar">
                                <span class="timeStamp">
                                    ${data.timeStamp}
                                </span>
                                <p class="text">
                                    ${data.message}
                                </p>
                        </li>
                    `;
                    break;
                case 'login':
                    chatList.innerHTML+=`
                    <li class='system'>
                        <p class="text">
                            ${data.message}
                        </p>
                    </li>
                    `;
                    break;
                case 'logout':
                    chatList.innerHTML+=`
                    <li class='system'>
                        <p class="text">
                            ${data.message}
                        </p>
                    </li>
                    `;
                    break;
            };
        };

    </script>
</body>
</html>
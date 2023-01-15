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
            display:flex;
            padding:5px 0;
        }
        .fromMe{
            flex-direction:row-reverse
        }
        .text{
            word-wrap: anywhere;
        }
    </style>
</head>
<body>
    <div id="shell">
        <div id="screen">
            <ul id="chatList"></ul>
            <div id="typing">
                <input type="text" id="message" placeholder="請輸入訊息" autofocus>
                <input type="submit" value="送出" onclick="sendMessasge(); ">
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

        const sendMessasge = function (){
            let message = document.getElementById("message").value;
            if(message){
                let packet={};
                packet.type="message";
                packet.message=message;
                packet.id=uuid;
                packet.timeStamp=formatAMPM(new Date);
                packet.name=name;
                packeted=JSON.stringify(packet)
                websocket.send(packeted);
                document.getElementById("message").value='';
            };
        };

        document.onkeypress = function (e) {
            if (e.keyCode == 13) sendMessasge();
        };

        const appendMessage = function (data){
            let chatList = document.getElementById('chatList');
            let from = uuid === data.id ? "fromMe" : "fromGuest";

            chatList.innerHTML+=`
                <li class="${from}">
                    <p class="text">
                        ${data.message}
                    </p>
                </li>
            `
        };

    </script>
</body>
</html>
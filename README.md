## workerman
>workerman是一款开源高性能PHP应用容器，它大大突破了传统PHP应用范围，被广泛的用于互联网、即时通讯、APP开发、硬件通讯、智能家居、物联网等领域的开发。<br>

[workerman](https://www.workerman.net/)
## GatewayWorker
>基于workerman容器的分布式即时通讯系统，可用于聊天室、物联网等即时通讯领域的服务<br>

[GatewayWorker](https://www.workerman.net/doc/gateway-worker/)

## 原理

![image](https://user-images.githubusercontent.com/97031067/224073900-4e0e65cf-aed1-48c1-b928-752720d577db.png)<br>
從圖上我們可以看出Gateway負責接收客戶端的連接以及連接上的數據，然後Worker接收Gateway發來的數據做處理，然後再經由Gateway把結果轉發給其它客戶端。每個客戶端都有很多的路由到達另外一個客戶端，例如client⑦與client①可以經由藍色路徑完成數據通訊

## 架構



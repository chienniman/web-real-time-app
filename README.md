## workerman
>workerman是一款开源高性能PHP应用容器，它大大突破了传统PHP应用范围，被广泛的用于互联网、即时通讯、APP开发、硬件通讯、智能家居、物联网等领域的开发。<br>

[workerman](https://www.workerman.net/)
## GatewayWorker
>基于workerman容器的分布式即时通讯系统，可用于聊天室、物联网等即时通讯领域的服务<br>

[GatewayWorker](https://www.workerman.net/doc/gateway-worker/)

## 原理

![image](https://user-images.githubusercontent.com/97031067/224073900-4e0e65cf-aed1-48c1-b928-752720d577db.png)<br>
從圖上我們可以看出Gateway負責接收客戶端的連接以及連接上的數據，然後Worker接收Gateway發來的數據做處理，然後再經由Gateway把結果轉發給其它客戶端。每個客戶端都有很多的路由到達另外一個客戶端，例如client⑦與client①可以經由藍色路徑完成數據通訊

1、Register、Gateway、BusinessWorker進程啟動<br>
2、Gateway、BusinessWorker進程啟動後向Register服務進程發起長連接註冊自己<br>
3、Register服務收到Gateway的註冊後，把所有Gateway的通訊地址保存在內存中<br>
4、Register服務收到BusinessWorker的註冊後，把內存中所有的Gateway的通訊地址發給BusinessWorker<br>
5、BusinessWorker進程得到所有的Gateway內部通訊地址後嘗試連接Gateway<br>
6、如果運行過程中有新的Gateway服務註冊到Register（一般是分佈式部署加機器），則將新的Gateway內部通訊地址列表將廣播給所有BusinessWorker，BusinessWorker收到後建立連接<br>
7、如果有Gateway下線，則Register服務會收到通知，會將對應的內部通訊地址刪除，然後廣播新的內部通訊地址列表給所有BusinessWorker，BusinessWorker不再連接下線的Gateway<br>
8、至此Gateway與BusinessWorker通過Register已經建立起長連接<br>
9、客戶端的事件及數據全部由Gateway轉發給BusinessWorker處理，BusinessWorker默認調用Events.php中的onConnect onMessage onClose處理業務邏輯。 <br>
10、BusinessWorker的業務邏輯入口全部在Events.php中，包括onWorkerStart進程啟動事件(進程事件)、onConnect連接事件(客戶端事件)、onMessage消息事件（客戶端事件）、onClose連接關閉事件（客戶端事件）、onWorkerStop進程退出事件（進程事件）<br>

## 架構



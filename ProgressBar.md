# JS/PHP RealTime Progress Bar

## HTML

index.html

```html
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" type="text/css" href="progress.css">
    </head>
<body>

    <br>
    <div class="wrapper-progress" id="wrapper-progress"></div>

    <script type="text/javascript" src="Progress.js"></script>
    <script>
        window.onload = function() {
            Progress.init({
                container: "wrapper-progress",
                url: "Server.php"
            });
        }
    </script>
</body>
</html>
```

## CSS

progress.css

```css
.progress-results {
    border:1px solid #000;
    padding:10px;
    width:98%;
    height:250px;
    overflow:auto;
    background:#eee;
    margin: 15px 0;
}
.progress-percentage {
    text-align:right;
    display:block;
    margin-top:5px;
}
```

## Javascript
Progress.js

```js
/**
 * Classe para gerenciamento de barra de Progresso
 */
var Progress = {

    esSupport: null,
    es: null,
    result: null,
    url: 'Server.php',
    wrapper: "wrapper-progress",

    init: function (config)
    {
        this.noEsSupport = (window.EventSource === undefined);
        if (this.noEsSupport) {
            this.addLog (
                'This browser does not support server-sent events.<br>'
                + 'You will not get any progress messages.<br>'
                + 'Try Opera, Firefox or Chrome'
            );
        }

        if (config === undefined) {
            return;
        }

        // server retrive data URL
        if (config.url !== undefined) {
            this.url = config.url;
        }

        this.createProgressHtmlStructure();
    },

    createProgressHtmlStructure: function ()
    {
        // progress bar container
        var wrapper = document.getElementById(this.wrapper);

        // start button
        var startButton = document.createElement('input');
        startButton.type = 'button';
        startButton.onclick = function() {Progress.start();};
        startButton.value = "Start";

        // stop button
        var stopButton = document.createElement('input');
        stopButton.type = 'button';
        stopButton.onclick = function() {Progress.stop();};
        stopButton.value = "Stop";

        // result log
        var resultDiv = document.createElement('div');
        resultDiv.id = "progress-results";
        resultDiv.setAttribute("class", "progress-results");

        // progress
        var progressElement = document.createElement('progress');
        progressElement.id = "progressor";
        progressElement.value = 0;
        progressElement.max = 100;
        
        // span
        var progressSpan = document.createElement('span');
        progressSpan.id = "progress-percentage";
        progressSpan.setAttribute('class', 'progress-percentage');
        progressSpan.innerText = 0;

        wrapper.append(startButton);
        wrapper.append(stopButton);
        wrapper.append(resultDiv);
        wrapper.append(progressElement);
        wrapper.append(progressSpan);
    },

    addLog: function (message)
    {
        var r = document.getElementById('progress-results');
        r.innerHTML += message + '<br>';
        r.scrollTop = r.scrollHeight;
    },

    stop: function ()
    {
        this.es.close();
        this.addLog('Interrupted');
    },

    start: function ()
    {
        this.es = new EventSource(this.url);

        this.es.addEventListener('message', function(e) {
            this.result = JSON.parse( e.data );
            Progress.addLog(this.result.message);

            if (e.lastEventId == 'CLOSE') {
                Progress.addLog('Received CLOSE closing');
                Progress.es.close();
                var pBar = document.getElementById('progressor');
                pBar.value = pBar.max; //max out the progress bar
            }
            else {
                var pBar = document.getElementById('progressor');
                pBar.value = this.result.progress;
                var perc = document.getElementById('progress-percentage');
                perc.innerHTML   = this.result.progress  + "%";
                perc.style.width = (Math.floor(pBar.clientWidth * (this.result.progress/100)) + 15) + 'px';
            }
        });

        this.es.addEventListener('error', function (e) {
            Progress.addLog('Error occurred');
            Progress.es.close();
        });
    }
};

```

## PHP

Server.php

```php
    header('Content-Type: text/event-stream');
    // recommended to prevent caching of event data.
    header('Cache-Control: no-cache'); 

    function sendMessage($id, $message, $progress = null)
    {
        $d = array('message' => $message , 'progress' => $progress);      
        echo "id: $id" . PHP_EOL;
        echo "data: " . json_encode($d) . PHP_EOL;
        echo PHP_EOL;      
        ob_flush();
        flush();
    }

    //LONG RUNNING TASK
    $total = 10;
    for ($i = 1; $i <= $total; $i++) {
        sendMessage($i, "on iteration {$i} of {$total}", $i * $total); 
        sleep(1);
    }

    sendMessage('CLOSE', 'Process complete');
```


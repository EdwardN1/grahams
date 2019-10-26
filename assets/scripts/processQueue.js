function QueryStringToJSON(qs) {
    let pairs = qs.slice(1).split('&');

    let result = {};
    pairs.forEach(function (pair) {
        pair = pair.split('=');
        result[pair[0]] = decodeURIComponent(pair[1] || '');
    });

    return JSON.parse(JSON.stringify(result));
}

class ts_execute_queue {
    constructor(outputElement, finishedEvent, processEvent) {
        this._queue = [];
        this._output = outputElement;
        this._index = 0;
        this._finished = false;
        this._finishedEvent = finishedEvent;
        this._processEvent = processEvent;
    }

    queue(url, data) {
        let q_item = {url: url, data: data};
        this._queue.push(q_item);
        //_queue.
    }

    reset() {
        this._queue = [];
        this._index = 0;
        this._finished = false;
    }

    get finished() {
        return this._finished;
    }

    process() {
        let q_item = this._queue[this._index];
        let obj = this;
        jQuery.ajax({
            data: q_item.data,
            type: "POST",
            url: q_item.url,
            success: function (data) {
                try {
                    var r = decodeURIComponent(this.data);
                    var ro = QueryStringToJSON('?' + r);
                } catch (e) {
                    obj.output('<span style="color: orange;">' + e.message + ' data: ' + this.data + '</span>');
                    var r = this.data;
                    var ro = QueryStringToJSON('?' + r);
                }
                let action = ro.action;
                obj._processEvent(action,r,data);
                obj._index++;
                if(obj._queue[obj._index] !== undefined) {
                    obj.process();
                    //window.console.log('Processing');
                } else {
                    obj._finished = true;
                    obj._queue = [];
                    obj._index = 0;
                    obj._finishedEvent();
                }
            }
        });
    }

    output(text) {
        jQuery(this._output).prepend(text + '<br>');
    }
}

/*==========================================================================================*/

/*
$('#UpdateSince').click(function () {
    let q = new ts_execute_queue('#ePimResult', function () {
        alert('action');
    });
    q.action();
});*/

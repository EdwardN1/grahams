jQuery(document).ready(function ($) {
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

        get finished() {
            return this._finished;
        }

        process() {
            let q_item = this._queue[this._index];
            let obj = this;
            $.ajax({
                data: q_item.data,
                type: "POST",
                url: q_item.url,
                success: function (data) {
                    obj._processEvent(data);
                    obj._index++;
                    if(obj._queue[obj._index] !== undefined) {
                        obj.process();
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
            $(this._output).prepend(text + '<br>');
        }
    }

    /*==========================================================================================*/

    $('#UpdateSince').click(function () {
        let q = new ts_execute_queue('#ePimResult', function () {
            alert('action');
        });
        q.action();
    });
});
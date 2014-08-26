/**
 * chat.js
 */
$(document).ready(function() {
    var chatlog = $('.chatlog');

    if (chatlog.length > 0) {
        // An EventSource is declared by linking to the script on the
        // server that provides the event stream; the uid is a value
        // passed via the host page to link users to their PHP
        // session on the server side.
        var evtSource = new EventSource("sse.php?uid=" + uid);

        // Event listeners can be added to the Event Source
        // using normal DOM methods.
        evtSource.addEventListener("message", function(e) {
            console.log('message');
            var el = document.createElement("li");

            el.innerHTML = e.data;
            chatlog.append(el);
        });

        // What the events will be called is determined in the server script;
        // "message" and "useradded" aren't regular DOM events but the ones
        // defined in the server-side code (see listing 4.8)
        evtSource.addEventListener("useradded", function(e) {
            console.log('useradded');
            var el = document.createElement("li");

            el.innerHTML = e.data;
            chatusers.appendChild(el);
        });

        evtSource.addEventListener("ping", function(e) {
            var newElement = document.createElement("li");

            var obj = JSON.parse(e.data);
            newElement.innerHTML = "ping at " + obj.time;
            eventList.appendChild(newElement);
        }, false);

        console.log(evtSource);
        var chatformCallback = function() {
            // A simple function to clear the chat input after the message
            // has been successfully sent to the server.
            chatform.find('input')[0].value = '';
        };

        var chatform = $('#chat');
        chatform.bind('submit', function() {
            var ajax_params = {
                // The add-chat.php takes the message and adds it to the
                // database, along with some information from the
                // session; check the download files for more details.
                url : 'add-chat.php',
                type : 'POST',
                data : chatform.serialize(),
                success : chatformCallback,
                error : function() {
                    window.alert('An error occurred');
                }
            };

            $.ajax(ajax_params);
            // Because the form is submitted by AJAX,
            // you don't want the page to reload
            return false;
        });
    }
});

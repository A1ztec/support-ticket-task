<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>SSE Test</title>
</head>

<body>
    <h1>SSE Messages:</h1>
    <ul id="messages"></ul>

    <script>
        const source = new EventSource("/sse");

        source.onmessage = function (event) {
            console.log("Received SSE:", event.data);
            const li = document.createElement("li");
            li.textContent = event.data;
            document.getElementById("messages").appendChild(li);
        };

        source.onerror = function (event) {
            console.error("SSE Error:", event);
        };
    </script>
</body>

</html>

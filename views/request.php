<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style/root.css">
    <title>Request | PixelApi</title>
</head>

<body>
    <div class="flex-col justify-center items-center vh100 gap-2">
        <span id="result">Status: Pending</span>
        <span>=== PixelApi Form ===</span>
        <form class="flex-col">
            <input type="hidden" name="token" value="" />
            <div class="flex-col gap-2">
                <div class="flex-col">
                    <label for="pixelType">Choose pixel type:</label>
                    <div class="items-center">
                        <input name="pixelType" type="radio" value="SOI" required>SOI
                        <input name="pixelType" type="radio" value="DOI" required>DOI
                    </div>
                </div>

                <div class="flex-col">
                    <label for="userId">User ID:</label>
                    <input name="userId" type="number" required min="1">
                </div>

                <div class="flex-col">
                    <label for="occuredOn">Occured on:</label>
                    <input name="occuredOn" type="datetime-local" required readonly class="disabled">
                </div>

                <div class="flex-col">
                    <label for="portalId">Portal ID:</label>
                    <input name="portalId" type="number" required min="1">
                </div>

                <button type="submit">submit request</button>
            </div>
        </form>
    </div>

    <script>
        function occuredOn() {
            let occuredOn = new Date();
            const timeZoneOffset = occuredOn.getTimezoneOffset();
            occuredOn = new Date(occuredOn.getTime() - (timeZoneOffset * 60 * 1000));
            document.querySelector('input[name="occuredOn"]').value = occuredOn.toISOString().slice(0, 19);
            return parseInt(new Date().getTime() / 1000);
        }

        window.addEventListener('load', () => {
            occuredOn();

            setInterval(function() {
                occuredOn();
            }, 1000)
        });

        function request() {
            let result = document.getElementById('result');
            let status;
            let message;

            let token = {
                'token': document.querySelector('input[name="token"]').value.trim()
            }

            let request = {
                'crsf': {
                    'token': ''
                },
                'jwt': {
                    'token': ''
                },
                'auth0': {
                    'token': ''
                },
                'pixelData': {
                    'pixelType': document.querySelector('input[name="pixelType"]:checked').value.trim(),
                    'userId': parseInt(document.querySelector('input[name="userId"]').value),
                    'occuredOn': occuredOn(),
                    'portalId': parseInt(document.querySelector('input[name="portalId"]').value)
                }
            }

            console.log('Payload: ', request);

            result.style.opacity = 0;
            fetch('/pixelapi', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(request)
            }).then(response => {
                console.log(response);
                status = response.status;
                return response.json();
            }).then(response => {
                message = response.message;
                console.log('Status: ' + status + ' - ' + message);
                if (status === 201) {
                    result.classList.remove('status-error');
                    result.classList.add('status-ok');
                } else if (status === 400 || status === 401 || status === 403) {
                    result.classList.add('status-error');
                    result.classList.remove('status-ok');
                }

                result.textContent = 'Status: ' + status + ' - ' + message;
                result.style.opacity = 1;
            });
        }

        document.querySelector('form').addEventListener('submit', (e) => {
            e.preventDefault();
            request();
        });
    </script>
</body>

</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Password reset</title>
</head>
<body>
    <table>
        <tr>
            <td> {{ $subject }}</td>
        </tr>
        <tr>
            <td>Hola sr. <strong>{{$body['name']}}</strong></td>
        </tr>
        <tr>
            <td>
                <p>Hemos recibido un request para cambiar su contraseña</p>
                Code: <strong> {{ $body['code'] }}</strong>                
            </td>
        </tr>
    </table>
</body>
</html>
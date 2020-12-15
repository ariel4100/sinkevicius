<!DOCTYPE html>

<html>
<style>
    body {
        font-family: Helvetica, sans-serif;
        color:#494949;
    }

    ul {
        list-style: none;
    }

    div{
        background-color: #F8F8F8;
        width: 85%;
        border-radius:20px;
        padding: 1rem 2rem;
    }
</style>
<body>
<div>
    <h2>{{ env('APP_NAME') }}</h2>
    <h3>RR-HH</h3>
    <p>Enviado desde la web </p>
    <br>
    <h3>Datos</h3>
    <ul>
        @if($data['nombre'])<li><strong>Nombre y Apellido:</strong>{{ @$data['nombre'] }} {{ @$data['apellido'] }}</li>@endif
        @if($data['pais'])<li><strong>País:</strong>{{ @$data['pais'] }}</li>@endif
        @if($data['nacionalidad'])<li><strong>Nacionalidad:</strong>{{ @$data['nacionalidad'] }}</li>@endif
        @if($data['dia'])<li><strong>Fecha de Nacimiento:</strong>{{ @$data['dia'] .' - '. @$data['mes'] .' - '. @$data['ano'] }}</li>@endif
        @if($data['dni'])<li><strong>Dni:</strong>{{ @$data['dni'] }}</li>@endif
        <li><strong>Email:</strong> {{ @$data['email'] }}</li>
            @if($data['telefono'])<li><strong>Teléfono:</strong>{{ @$data['telefono'] }}</li>@endif
        <br>
        <br>

    </ul>
</div>
</body>
</html>

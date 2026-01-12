@extends('layouts.app')
@section('title', 'Margen Bruto')

<style>
    /* Asegúrate de que el body y html ocupen el 100% del alto */
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        overflow: hidden; /* Evita barras de desplazamiento */
    }

    /* Contenedor principal del iframe */
    .iframe-container {
        position: relative;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    /* Asegúrate de que el iframe ocupe el 100% del contenedor */
    .iframe-container iframe {
        width: 100%;
        height: 100%;
        border: none; /* Elimina el borde predeterminado del iframe */
    }
    
    /* Estilo del botón flotante */
    .back-button {
        position: fixed; /* Cambia a posición fija */
        top: 10px; /* Distancia desde la parte superior */
        left: 10px; /* Distancia desde la izquierda */
        z-index: 1000; /* Asegura que el botón esté por encima del iframe */
        background: none; /* Elimina el fondo del botón */
        border: none; /* Elimina el borde del botón */
        padding: 0; /* Elimina el padding */
    }

    /* Estilo de la imagen del botón */
    .back-button img {
        width: 40px; /* Ancho de la imagen */
        height: 40px; /* Alto de la imagen */
        cursor: pointer; /* Cambia el cursor al pasar sobre la imagen */
    }
</style>

<!-- Botón de retroceso -->
<a href="{{ route('home') }}" class="back-button">
    <img src="{{ asset('storage/images/Hacia_atras.png') }}" alt="Volver atrás">
</a>

    <!-- Contenedor para el iframe -->
    <div class="iframe-container">
  <!-- Boton de retroceso -->
        <iframe
            title="Margen Bruto V6"
            src="https://app.powerbi.com/view?r=eyJrIjoiYWY0YjI5MjQtYTkzYi00ZDg0LWIwYzYtNjI0OGE5Nzc0NDYzIiwidCI6ImZmZDgyMjAxLWJjNzUtNDA5OS05MjkzLWRlNDdiMzkyNmM5YiIsImMiOjR9"
            allowFullScreen="true">
        </iframe>
    </div>



// Note: This example requires that you consent to location sharing when
// prompted by your browser. If you see the error "The Geolocation service
// failed.", it means you probably did not give permission for the browser to
// locate you.
let map, infoWindow;

function initMap() {
  map = new google.maps.Map(document.getElementById("map"), {
    center: { lat: 5.06288840159673, lng: -75.49857286451012},
    zoom: 8,
  });
  infoWindow = new google.maps.InfoWindow();  
}

window.initMap = initMap;

const xhr = new XMLHttpRequest();
xhr.open('GET', '/clima/Servicios/Servicio.php?Servicio=ciudades');
xhr.onload = function() {
  if (xhr.status === 200) {
    const data = JSON.parse(xhr.responseText);
    var div_general = document.getElementById("terms-list");
    var div_general_historial = document.getElementById("terms-list-historial");
    data.response.forEach(element => {
      const NewDiv = document.createElement('div');
      NewDiv.classList.add("term");
      
      const boton = document.createElement('button');
      boton.classList.add("boton")
      boton.innerHTML = element.name;
      boton.setAttribute("data-id", element.id)
      boton.addEventListener("click", () => {
        infoHumidity(element.id);        
      });

      NewDiv.appendChild(boton)
      div_general.appendChild(NewDiv);

      const NewDivHistorial = document.createElement('div');
      NewDivHistorial.classList.add("term");
      
      const botonHistorial = document.createElement('button');
      botonHistorial.classList.add("boton")
      botonHistorial.innerHTML = "Historial " + element.name;
      botonHistorial.setAttribute("data-id", element.id)
      botonHistorial.addEventListener("click", () => {
        historial(element.id);     
      });

      NewDivHistorial.appendChild(botonHistorial)
      div_general_historial.appendChild(NewDivHistorial);
    });
  } else {
    console.error('Error al hacer la solicitud.');
  }
};
xhr.send();

function infoHumidity(id) {
  var div_general = document.getElementById("map");
  div_general.classList.remove("hidden");
  var div_general = document.getElementById("table");
  div_general.classList.add("hidden");
  const xhr = new XMLHttpRequest();
  xhr.open('GET', '/clima/Servicios/Servicio.php?Servicio=humedad&id=' + id);
  xhr.onload = function() {
    if (xhr.status === 200) {
      const data = JSON.parse(xhr.responseText);
      infoWindow = new google.maps.InfoWindow();
      const pos = {
        lat: parseFloat(data.response.lat),
        lng: parseFloat(data.response.lon),
      };
      infoWindow.setPosition(pos);
      infoWindow.setContent(data.response.humidity.toString() + '% de humedad');
      infoWindow.open(map);
      map.setZoom(8);
      map.setCenter(pos);
    } else {
      console.error('Error al hacer la solicitud.');
    }
  };  
  xhr.send();
}

function historial(id) {
  var div_general = document.getElementById("map");
  div_general.classList.add("hidden");
  var div_general = document.getElementById("table");
  div_general.classList.remove("hidden");
  
  const xhr = new XMLHttpRequest();
  xhr.open('GET', '/clima/Servicios/Servicio.php?Servicio=historial&id=' + id);
  xhr.onload = function() {
    if (xhr.status === 200) {
      const data = JSON.parse(xhr.responseText);
      const tbody = document.getElementById("table-historial");
      tbody.innerHTML = "";
      data.response.forEach(element => {
        const newRow = document.createElement('tr');

        const cityCell = document.createElement('td');
        const dateCell = document.createElement('td');
        const dateActualCell = document.createElement('td');
        const humidityCell = document.createElement('td');

        cityCell.textContent = element.ciudad;
        dateCell.textContent = element.tiempo;
        dateActualCell.textContent = element.tiempo_actual;
        humidityCell.textContent = element.humedad;

        newRow.appendChild(cityCell);
        newRow.appendChild(dateCell);
        newRow.appendChild(dateActualCell);
        newRow.appendChild(humidityCell);

        tbody.appendChild(newRow);

      });      
    } else {
      console.error('Error al hacer la solicitud.');
    }
  };
  xhr.send();

}

window.addEventListener('resize', function() {
  location.reload();
});

if(window.innerWidth <= 755) {
  var izquierdo = document.getElementById("izquierdo");
  var derecho = document.getElementById("derecho");

  izquierdo.classList.remove("div-izquierdo");
  izquierdo.classList.add("box-arriba");

  derecho.classList.remove("div-derecho");
  derecho.classList.add("box-abajo");
}

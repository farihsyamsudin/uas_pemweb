const map = L.map('map',{
    center: [-6.115563, 106.152653],
    zoom: 15,
    scrollWheelZoom: false,
    maxZoom: 16,
    minZoom: 13,
    zoomControl: false
});

L.control.zoom({
    position: 'bottomright',
    zoomInText: '<span aria-hidden="true">+</span>'
}).addTo(map);

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
}).addTo(map);

console.log(dataJson)

dataJson.map(e =>{
    L.marker([e.lat, e.lon], {
        icon : L.icon({
            iconUrl: `img/icon/${e.icon}`,
            iconSize: [45, 45],
        })
    }).addTo(map)
    .bindPopup(`
    <p class="text-xs md:text-sm w-full font-bold tracking-wider ">${e.judul}</p>
    <p class="text-xs md:text-sm w-full font-semibold ">${e.kategori}</p>
    <img src="img/cover/${e.gambar}" class="w-full" alt="${e.gambar}" >
    <p class="text-xs md:text-sm w-full">${e.alamat}</p>
    <p class="text-xs md:text-sm w-full">${e.detail}</p>
    <p class="text-xs md:text-sm w-full">${e.lat}, ${e.lon}</p>
    `)
})

// Bar Handler
const barHandler = document.getElementById("barHandler")
const barContainer = document.getElementById("barContainer")

const barHandlerClick = () =>{
    barContainer.classList.toggle('translate-y-full')
    barContainer.classList.toggle('overflow-y-auto')
    barHandler.classList.toggle('scale-0')
    document.getElementById('map').classList.toggle('blur-md')
}

const kategoriDetailClick = (t, d) =>{
    Swal.fire({
        title: `Kategori ${t}`,
        text: d,
    })
}

function hitungTotal() {
    var elLap = document.getElementById("lapangan");
    var elDur = document.getElementById("durasi");
    var elRak = document.getElementById("jml_raket");
    var elTgl = document.getElementById("tanggal");
    var elJam = document.getElementById("jam");
    var display = document.getElementById("info_harga");

    var hargaDasar = 0;

    if(elLap.selectedIndex > 0) {
        hargaDasar = parseInt(elLap.options[elLap.selectedIndex].getAttribute("data-harga"));
    }

    var durasi = parseInt(elDur.value);
    if(isNaN(durasi)) durasi = 0;

    var raket = parseInt(elRak.value);
    if(isNaN(raket)) raket = 0;

    var tambahan = 0;
    var info = "Normal";

    if(elTgl.value !== "") {
        var d = new Date(elTgl.value);
        var day = d.getDay();
        if(day == 0 || day == 6) {
            tambahan += 50000;
            info = "Weekend (+50rb)";
        }
    }

    if(elJam.value !== "") {
        var jamStr = elJam.value.split(":");
        var jam = parseInt(jamStr[0]);
        if(jam >= 17) {
            tambahan += 20000;
            info += " & Malam (+20rb)";
        }
    }

    var total = ((hargaDasar + tambahan) * durasi) + (raket * 15000);
    display.innerHTML = "Estimasi: Rp " + total + " (" + info + ")";

}

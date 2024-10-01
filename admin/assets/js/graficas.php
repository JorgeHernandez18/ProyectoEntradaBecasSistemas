
<script>
    // Usar los datos PHP en JavaScript
    var datosMensuales = <?php echo $datosJSON; ?>;
    var meses = <?php echo $mesesJSON; ?>;
    var programas = <?php echo json_encode($programas); ?>;
    var totales = <?php echo json_encode($totales); ?>;

    //GRAFICA 7 CARREARAS MAS FRECUENTES
    var ctx = document.getElementById("chart-bars").getContext("2d");
    // Datos de programas y totales
    
    new Chart(ctx, {
      type: "bar",
      data: {
        labels: <?php echo $programasJSON; ?>,
        datasets: [{
          label: "Visitas por Programa",
          tension: 0.4,
          borderWidth: 0,
          borderRadius: 4,
          borderSkipped: false,
          backgroundColor: "rgba(255, 255, 255, .8)",
          data: <?php echo $totalesJSON; ?>,
          maxBarThickness: 45
        }, ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          },
          title: {
            display: true,
            text: 'Top 7 Programas más Frecuentes en la Biblioteca',
            color: '#fff',
            font: {
              size: 16,
              family: "Roboto",
            }
          }
        },
        interaction: {
          intersect: false,
          mode: 'index',
        },
        scales: {
          y: {
            grid: {
              drawBorder: false,
              display: true,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [5, 5],
              color: 'rgba(255, 255, 255, .2)'
            },
            ticks: {
              suggestedMin: 0,
              suggestedMax: Math.max(...<?php echo $totalesJSON; ?>) * 1.1,
              beginAtZero: true,
              padding: 10,
              font: {
                size: 14,
                weight: 300,
                family: "Roboto",
                style: 'normal',
                lineHeight: 2
              },
              color: "#fff"
            },
          },
          x: {
            grid: {
              drawBorder: false,
              display: false, // Ocultamos la cuadrícula del eje X para mejorar la visibilidad
            },
            ticks: {
              display: true,
              color: '#f8f9fa',
              padding: 10,
              font: {
                size: 12,
                weight: 300,
                family: "Roboto",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
        },
      },
    });
    


    //GRAFICA FRUJO ENTRADA POR MES
    var ctx2 = document.getElementById("chart-line").getContext("2d");

    new Chart(ctx2, {
      type: "line",
      data: {
        labels: meses,
        datasets: [{
          label: "Registros mensuales",
          tension: 0,
          borderWidth: 0,
          pointRadius: 5,
          pointBackgroundColor: "rgba(255, 255, 255, .8)",
          pointBorderColor: "transparent",
          borderColor: "rgba(255, 255, 255, .8)",
          borderWidth: 4,
          backgroundColor: "transparent",
          fill: true,
          data: datosMensuales,
          maxBarThickness: 10
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          }
        },
        interaction: {
          intersect: false,
          mode: 'index',
        },
        scales: {
          y: {
            grid: {
              drawBorder: false,
              display: true,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [5, 5],
              color: 'rgba(255, 255, 255, .2)'
            },
            ticks: {
              display: true,
              color: '#f8f9fa',
              padding: 10,
              font: {
                size: 14,
                weight: 300,
                family: "Roboto",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
          x: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: false,
              drawTicks: false,
              borderDash: [5, 5]
            },
            ticks: {
              display: true,
              color: '#f8f9fa',
              padding: 10,
              font: {
                size: 14,
                weight: 300,
                family: "Roboto",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
        },
      },
    });
    

    //GRAFICA FRUJO ENTRADA POR DIAS DE LA SEMANA
    var ctx3 = document.getElementById("chart-line-tasks").getContext("2d");

    new Chart(ctx3, {
      type: "bar",
      data: {
        labels: <?php echo $diasSemanaJSON; ?>,
        datasets: [{
          label: "Entradas de la semana actual",
          tension: 0.4,
          borderWidth: 0,
          borderRadius: 4,
          borderSkipped: false,
          backgroundColor: "rgba(255, 255, 255, .8)",
          borderColor: "transparent",
          data: <?php echo $totalesSemanaJSON; ?>,
          maxBarThickness: 30
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          },
          tooltip: {
            callbacks: {
              title: function(context) {
                return context[0].label + ' ' + new Date().getFullYear();
              }
            }
          }
        },
        interaction: {
          intersect: false,
          mode: 'index',
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              drawBorder: false,
              display: true,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [5, 5],
              color: 'rgba(255, 255, 255, .2)'
            },
            ticks: {
              display: true,
              padding: 10,
              color: '#f8f9fa',
              font: {
                size: 14,
                weight: 300,
                family: "Roboto",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
          x: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: false,
              drawTicks: false,
              borderDash: [5, 5]
            },
            ticks: {
              display: true,
              color: '#f8f9fa',
              padding: 10,
              font: {
                size: 14,
                weight: 300,
                family: "Roboto",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
        },
      },
    });

    
    
    // Función para actualizar la información adicional
    function actualizarInformacion() {
      var totalVisitas = parseInt(totales[0])+parseInt(totales[1])+parseInt(totales[2])+parseInt(totales[3])+parseInt(totales[4])+parseInt(totales[5]);
      var indexMaxVisitas = totales.indexOf(Math.max(...totales));
      var programaMasFrecuente = programas[0];
      
      document.getElementById('totalVisitas').textContent = totalVisitas;
      document.getElementById('programaMasFrecuente').textContent = programaMasFrecuente;
      document.getElementById('periodoSemestre').textContent = '<?php echo addslashes($inicioSemestre . " a " . $finSemestre); ?>';

      // Actualizar la información en la tarjeta
      document.getElementById('totalVisitasSemestre').textContent = <?php echo $totalVisitasSemestre; ?>;
      document.getElementById('mesMasFrecuente').textContent = '<?php echo $mesMasFrecuente; ?>';
      document.getElementById('periodoSemestre2').textContent = '<?php echo addslashes($inicioSemestre . " a " . $finSemestre); ?>';

      // totalesSemana y diasSemana
      var totalesSemana = <?php echo $totalesSemanaJSON; ?>;
      var diasSemana = <?php echo $diasSemanaJSON; ?>;
      var contador = 0;

      // Usar totalesSemana.length en lugar de length(totalesSemana)
      for (var i = 0; i < totalesSemana.length; i++) {
        contador += parseInt(totalesSemana[i]);  // Puedes usar += para simplificar la suma
      }

      document.getElementById('totalVisitasSemana').textContent = contador;

      // Encontrar el día con más visitas
      var maxVisitas = Math.max(...totalesSemana);
      var indexMaxVisitas = totalesSemana.indexOf(maxVisitas);
      var diaMasFrecuente = diasSemana[indexMaxVisitas];
      document.getElementById('diaMasFrecuente').textContent = diaMasFrecuente + ' (' + maxVisitas + ' visitas)';

      // Establecer el período de la semana
      var inicioSemana = '<?php echo $inicioSemana; ?>';
      var hoy = '<?php echo $hoy; ?>';
      document.getElementById('periodoSemana').textContent = inicioSemana + ' a ' + hoy;
    }
    // Llamar a la función para actualizar la información
    actualizarInformacion();
</script>